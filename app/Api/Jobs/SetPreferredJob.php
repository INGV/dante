<?php

namespace App\Api\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Exception;
use App\Api\v1\Models\Tables\EventModel;
use Illuminate\Support\Facades\DB;

class SetPreferredJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $eventId;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;
    
    /**
     * The number of seconds to wait before retrying the job.
     * !!! It works only with Laravel-Horizon 5.8 !!!
     *
     * @var int
     */
    public $retryAfter = 3;    
    
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;
    
    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = false;
    
    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['v.'. config('dante.version'), 'class:'.substr(strrchr(__CLASS__, "\\"), 1), 'eventId:'.$this->eventId];
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }
    
	/*!
	 * \brief Select preferred hypocenter id for event id
	 *
	 * \param eventId Event identifier
	 * \return Hypocenter id or null
	 *
	 */
	public static function selectPreferredHypocenter($eventId) {
		\Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
		$ret = null;

		// Get Model for event
		$event = EventModel::findOrFail($eventId);

		// Select preferred hypocenter id
		$hypocenter = $event->hypocenters()->orderBy('inserted', 'DESC')->first();
		if($hypocenter) {
			$ret = $hypocenter->id;
			\Log::debug(" hypocenter__id=".$hypocenter->id);
		}
		\Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
		return $ret;
	}
    
	/*!
	 * \brief Select preferred magnitude id for event id
	 *
	 * \param eventId Event identifier
	 * \return Magnitude id or null
	 *
	 */
	public static function selectPreferredMagnitude($eventId) {
		\Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
		$ret = null;

		// Select preferred magnitude id
		$magnitude = EventModel::join('hypocenter', 'event.id',		'=', 'hypocenter.fk_event')
			->join('type_hypocenter',	'type_hypocenter.id',			'=', 'hypocenter.fk_type_hypocenter')
			->join('magnitude',			'magnitude.fk_hypocenter',		'=', 'hypocenter.id')
			->join('type_magnitude',	'magnitude.fk_type_magnitude',	'=', 'type_magnitude.id')
			->select('magnitude.id')
			->where('hypocenter.fk_event', '=', $eventId)
			->where(function($q) {
				$q->where('type_hypocenter.value', '<=', 200)
					->orWhereIn('type_hypocenter.value', [501, 1000]);
			})
			->where('type_hypocenter.value', '<>', 99)
			->orderByRaw('IF(type_magnitude.name = "Mw", 10000, type_hypocenter.value) DESC, hypocenter.modified DESC, type_magnitude.priority DESC')
			->first();

		if($magnitude) {
			$ret = $magnitude->id;
		}

		\Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
		return $ret;
	}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        $eventId = $this->eventId;
        try {
            \Log::info("  beginTransaction() - ".__FUNCTION__);
            DB::beginTransaction();
            // Get Event Model
            $event = EventModel::find($eventId);

            // Set previous values for fk_pref_hyp and fk_pref_mag
            $previous_fk_pref_hyp = $event->fk_pref_hyp;
            $previous_fk_pref_mag = $event->fk_pref_mag;
            $previous_fk_events_group = $event->fk_events_group;

            // Init flags for previous fk_pref_hyp and fk_pref_mag
            $flag_fk_pref_hyp_is_changed = false;
            $flag_fk_pref_mag_is_changed = false;

            // Select preferred hypocenter id
            $new_fk_pref_hyp = self::selectPreferredHypocenter($eventId);
            if(!is_null($new_fk_pref_hyp)) {
                // Change fk_pref_hyp only if hypocenter id has changed
                if($new_fk_pref_hyp != $previous_fk_pref_hyp) {
                    $flag_fk_pref_hyp_is_changed = true;
                    \Log::info("  set event.fk_pref_hyp=".$new_fk_pref_hyp);
                    $event->fk_pref_hyp = $new_fk_pref_hyp;
                }
            }

            // Select preferred magnitude id
            $new_fk_pref_mag = self::selectPreferredMagnitude($eventId);

            if(!is_null($new_fk_pref_mag)) {
                // Change fk_pref_hyp only if hypocenter id has changed
                if($new_fk_pref_mag != $previous_fk_pref_mag) {
                    $flag_fk_pref_mag_is_changed = true;
                    \Log::info("  set event.fk_pref_mag=".$new_fk_pref_mag);
                    $event->fk_pref_mag = $new_fk_pref_mag;
                }
            }

            // Update event only if something has changed
            if(
                $flag_fk_pref_hyp_is_changed
                || $flag_fk_pref_mag_is_changed
            ) {
                $event->save();
            }

            //
            $eventToReturn = $event->toArray();

            \Log::info("  commit() - ".__FUNCTION__);
            DB::commit();
        } catch (Exception $ex) {
            \Log::info("  rollBack() - ".__FUNCTION__);
            DB::rollBack();
        }
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        
        return $eventToReturn;
    }
    
    public function failed(Exception $exception)
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
    }
}

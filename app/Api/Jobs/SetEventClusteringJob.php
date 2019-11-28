<?php

namespace App\Api\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Exception;
use Phpml\Clustering\DBSCAN;
use Phpml\Math\Distance;
use App\Api\v1\Models\Tables\EventModel;

// TODO define a custom distance function
class EventDistance implements Distance
{

	// from https://inkplant.com/code/calculate-the-distance-between-two-points
	public function get_meters_between_points($latitude1, $longitude1, $latitude2, $longitude2) {
		if (($latitude1 == $latitude2) && ($longitude1 == $longitude2)) { return 0; } // distance is zero because they're the same point
		$p1 = deg2rad($latitude1);
		$p2 = deg2rad($latitude2);
		$dp = deg2rad($latitude2 - $latitude1);
		$dl = deg2rad($longitude2 - $longitude1);
		$a = (sin($dp/2) * sin($dp/2)) + (cos($p1) * cos($p2) * sin($dl/2) * sin($dl/2));
		$c = 2 * atan2(sqrt($a),sqrt(1-$a));
		$r = 6371008; // Earth's average radius, in meters
		$d = $r * $c;
		return $d; // distance, in meters
	}

	public function get_distance_between_points($latitude1, $longitude1, $latitude2, $longitude2) {
		$meters = get_meters_between_points($latitude1, $longitude1, $latitude2, $longitude2);
		$kilometers = $meters / 1000;
		$miles = $meters / 1609.34;
		$yards = $miles * 1760;
		$feet = $miles * 5280;
		return compact('miles','feet','yards','kilometers','meters');
	}

	public function get_mag_diff_in_secs($mag1, $mag2) {
		// Factor for magnitude difference to seconds unit
		$mag_factor_secs = 5.0;
		$mag_diff_in_secs = abs($mag1 - $mag2) * $mag_factor_secs;
		return $mag_diff_in_secs;
	}

	public function get_depth_diff_in_secs($depth1, $depth2) {
		// Factor for magnitude difference to seconds unit
		$depth_factor_secs = 0.1;
		$depth_diff_in_secs = abs($depth1 - $depth2) * $depth_factor_secs;
		return $depth_diff_in_secs;
	}

	/**
	 *
	 * Compute distance seconds unit
	 *
	 * @param array $a expects keys ot, lat, lon
	 * @param array $b expects keys ot, lat, lon
	 *
	 * @return float distance in seconds
	 *
	 */
	public function distance(array $a, array $b): float
	{
		$average_p_s_wave_speed_km_h = 6.0; // average speed of P and S wave, set to 6 km/h
		$flag_return = false;
		$distance = 0.0;
		$ot_diff_in_sec = 0.0;
		$lat_lon_diff_in_meters = 0.0;
		$lat_lon_diff_in_km = 0.0;
		$lat_lon_diff_in_secs = 0.0;
		$mag_diff_in_secs = 0.0;
		$depth_diff_in_secs = 0.0;

		// If belong to the same event id within an instance
		if($a['id_locator'] != 0 && $b['id_locator'] != 0) {
			if($a['instance'] == $b['instance'] && $a['id_locator'] == $b['id_locator']) {
				$flag_return = true;
			}
		}

		if($a['fk_pref_hyp'] == $b['fk_pref_hyp'] && $a['fk_pref_mag'] == $b['fk_pref_mag']) {
			$flag_return = true;
		}

		// Do not compute if $flag_return is set to true
		if(!$flag_return) {
			$ot_diff_in_sec = abs($a['ot'] - $b['ot']);

			$lat_lon_diff_in_meters = $this->get_meters_between_points($a['lat'], $a['lon'], $b['lat'], $b['lon']);

			$lat_lon_diff_in_km = ($lat_lon_diff_in_meters / 1000.0);

			$lat_lon_diff_in_secs = $lat_lon_diff_in_km / $average_p_s_wave_speed_km_h;

			$mag_diff_in_secs = $this->get_mag_diff_in_secs($a['mag'], $b['mag']);

			$depth_diff_in_secs = $this->get_depth_diff_in_secs($a['depth'], $b['depth']);

			$distance = $ot_diff_in_sec + $lat_lon_diff_in_secs + $mag_diff_in_secs + $depth_diff_in_secs;
		}

		// DEBUG tuning dbscan_clustering_params['epsilon']
		/*
		 * if($distance > 0 && $distance <= 20) {
		 *     print_r(
		 *         [
		 *             'a' => $a,
		 *             'b' => $b,
		 *             'ot_diff_in_sec' => $ot_diff_in_sec,
		 *             'lat_lon_diff_in_meters' => $lat_lon_diff_in_meters,
		 *             'lat_lon_diff_in_km' => $lat_lon_diff_in_km,
		 *             'lat_lon_diff_in_secs' => $lat_lon_diff_in_secs,
		 *             'mag_diff_in_secs' => $mag_diff_in_secs,
		 *             'depth_diff_in_secs' => $depth_diff_in_secs,
		 *             'distance' => $distance
		 *         ]
		 *     );
		 * }
		 */

		return $distance;
	}
}

class SetEventClusteringJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
	public $scheduler_params = [
		'ot'            => null,
		'deltaOtMin'    => 60,
	];

	public $dbscan_clustering_params = [
		'epsilon'           => 20,
		'minSamples'        => 1,
		// distanceMetric initilized by constructor
		'distanceMetric'    => null,
		'minkowski_lambda'  => 3,
	];    
    
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
    public function __construct($ot = null, $deltaOtMin = null)
    {
		if(is_null($ot)) {
			$this->scheduler_params['ot'] = date("Y-m-d H:i:s");
		} else {
			$this->scheduler_params['ot'] = $ot;
		}

		if(is_null($deltaOtMin)) {
			$this->scheduler_params['deltaOtMin'] = 30;
		} else {
            $this->scheduler_params['deltaOtMin'] = $deltaOtMin;
        }
        
        $this->dbscan_clustering_params['distanceMetric'] = new EventDistance();
    }    

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
		\Log::debug(" ot=".$this->scheduler_params['ot']);
        \Log::debug(" deltaOtMin=".$this->scheduler_params['deltaOtMin']);
        
        $ret = null;

		$startTimeDBRaw = "ADDDATE('".$this->scheduler_params['ot']."', INTERVAL -".$this->scheduler_params['deltaOtMin']." MINUTE)";
		$endTimeDBRaw = "ADDDATE('".$this->scheduler_params['ot']."', INTERVAL +".$this->scheduler_params['deltaOtMin']." MINUTE)";

		$events = EventModel::select(
			'event.id',
			'event.id_locator',
			'event.fk_events_group',
			'event.fk_pref_hyp',
			'event.fk_pref_mag',
			'event.type_group',
			'hypocenter.ot',
			'hypocenter.lat',
			'hypocenter.lon',
			'hypocenter.depth',
			'hypocenter.err_ot',
			'hypocenter.err_lat',
			'hypocenter.err_lon',
			'hypocenter.err_depth',
			'provenance.instance',
			'provenance.softwarename',
			'magnitude.mag'
		)
			->join('hypocenter', 'event.fk_pref_hyp', '=', 'hypocenter.id')
			->leftJoin('magnitude', 'event.fk_pref_mag', '=', 'magnitude.id')
			->join('provenance', 'hypocenter.fk_provenance', '=', 'provenance.id')
			->whereRaw('hypocenter.ot BETWEEN '.\DB::raw($startTimeDBRaw).' AND '.\DB::raw($endTimeDBRaw) )
			// TODO definire criteri per l'ordinamento
			// questo garantisce in qualche modo che il rappresentante fra gli
			// eventi in un cluster abbia tipo di ipocentro piu alto
			->orderBy('hypocenter.fk_type_hypocenter', 'DESC')
			->orderBy('hypocenter.quality', 'ASC')
			->distinct()
			->get()->toArray();
		//\Log::debug(" events:",$events);

		$newArrayEventsForGrouping = $this->computeEventGrouping($events);

		// update value in db
		foreach ($newArrayEventsForGrouping as $newEvent) {
			$updateEvent = EventModel::find($newEvent['id']);
			if($updateEvent->fk_events_group != $newEvent['fk_events_group']) {
				$updateEvent->fk_events_group = $newEvent['fk_events_group'];
				$updateEvent->save();
			}
		}

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);

        return $ret;
    }
    
    /*!
     * \brief Compute event grouping
     *
	 * \param arrayEventsForGrouping of item wich contains at least (event_id, fk_groups_event, ot, lat, lon, depth, instance, softwarename)
	 * \return new arrayEventsForGrouping with new association between event id and its event group
     *
     */
    public function computeEventGrouping($arrayEventsForGrouping) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        \Log::debug(" processing events:",$arrayEventsForGrouping);
        $ret = null;

		// print_r($arrayEventsForGrouping);

		// Prepare $samples as input for clustering
		$samples = [];
		$min_ot_in_secs = null;
		$max_ot_in_secs = null;
		foreach($arrayEventsForGrouping as &$item) {
			$date = date_create($item['ot']);
			$ot_in_secs=intval(date_format($date, 'U'));

			// Set min/max ot in seconds
			if(is_null($min_ot_in_secs)) { $min_ot_in_secs = $ot_in_secs; }
			if(is_null($max_ot_in_secs)) { $max_ot_in_secs = $ot_in_secs; }
			if($ot_in_secs > $max_ot_in_secs) { $max_ot_in_secs = $ot_in_secs; }
			if($ot_in_secs < $min_ot_in_secs) { $min_ot_in_secs = $ot_in_secs; }

			// N.B. Per preservare le chiavi nell'array $samples queste non devono essere numeriche!!!
			//      Aggiungo un prefisso alfabetico.
			$prefix_event_id = 'event_id_';
			$samples[$prefix_event_id.$item['id']] = [
				'ot' => $ot_in_secs,
				'lat' => $item['lat'],
				'lon' => $item['lon'],
				// fk_events_group and type_group
				'fk_events_group' => $item['fk_events_group'],
				'type_group' => $item['type_group'],
				// fk_pref_hyp and fk_pref_mag
				'fk_pref_hyp' => $item['fk_pref_hyp'],
				'fk_pref_mag' => $item['fk_pref_mag'],
				// instance and id_locator
				'instance' => $item['instance'],
				'id_locator' => $item['id_locator'],
				// magnitude
				'mag' => $item['mag'],
				// La profondita incide molto, per ora commentata, si potrebbe dividere per un fattore costante
				'depth' => $item['depth']
			];
		}

		// Execute clustering by DBSCAN
		// http://php-ml.readthedocs.io/en/0.4/machine-learning/clustering/dbscan/
		// TODO add parameter in config file
		// TODO Test and choose Distance function from http://php-ml.readthedocs.io/en/0.4/math/distance/
		// echo "OUT: samples"."\n"; print_r($samples);
		$dbscan = new DBSCAN(
			$this->dbscan_clustering_params['epsilon'],
			$this->dbscan_clustering_params['minSamples'],
			$this->dbscan_clustering_params['distanceMetric']
		);
		$ret_cluster = $dbscan->cluster($samples);
		// echo "OUT: ret_cluster"."\n"; print_r($ret_cluster);

		// Change fk_events_group in arrayEventsForGrouping before returning
		foreach($arrayEventsForGrouping as &$item) {
			foreach($ret_cluster as &$item_cluster) {
				$key_list = array_keys($item_cluster);
				if(array_search($prefix_event_id.$item['id'], $key_list)  !== FALSE) {
					// il rappresentante scelto e' il primo della lista, cio'
					// dipende dall'ordine impostato nella query
					$item['fk_events_group'] = str_replace($prefix_event_id, "", $key_list[0]);
				}
			}
		}
		$ret = $arrayEventsForGrouping;

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $ret;
    }
    
    public function failed(Exception $exception)
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
    }
}
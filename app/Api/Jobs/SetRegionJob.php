<?php

namespace App\Api\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/*
use App\Api\V1\WSs\Eventdb\Models\IngvHypocenterModel;
use App\Api\V1\WSs\Eventdb\Models\IngvHypocenterRegionNameModel;
use App\IngvUtilsModel;
use Exception;
use Event;
use App\Dante\Events\DanteExceptionWasThrownEvent;
*/

use App\Api\v1\Models\DanteBaseModel;
use App\Api\v1\Models\Tables\HypocenterModel;
use App\Api\v1\Models\Tables\HypocenterRegionNameModel;


class SetRegionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $hypocenterId;
    
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
    public $retryAfter = 5;
    
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;
    
    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['v.'. config('dante.version'), 'class:'.substr(strrchr(__CLASS__, "\\"), 1), 'hypocenterId:'.$this->hypocenterId];
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($hypocenterId)
    {
        $this->hypocenterId = $hypocenterId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info("START - ".__CLASS__.' -> '.__FUNCTION__);
        
        // get hypocenters to process
        $hypocenterId = $this->hypocenterId;
        
        // get remote host
        $hostByAddr = gethostbyaddr(\Request::ip());
        
        \Log::info(" hostByAddr=".$hostByAddr.", hypocenterId=".$hypocenterId);
        // Get Hypocenter Model
        $hypocenter = HypocenterModel::findOrFail($hypocenterId);

        // Getting "region" from WS
        $requestUrl = config('dante.uri_ws__boundaries__get_region_name').'?lat='.$hypocenter->lat.'&lon='.$hypocenter->lon;
        $region = DanteBaseModel::cacheJsonRequestUrl($requestUrl, 1440)['data']['region_name'];

        // Set region
        if (is_null($region)) {
            \Log::info(" the 'region' is 'null'; nothing to do.");
        } else {
            \Log::info(" set \"hypocenter_region_name.region=".$region."\"");
            HypocenterRegionNameModel::firstOrCreate([
                'fk_hypocenter'             => $hypocenterId,
                'region'					=> $region,
            ]);
        }

        \Log::info("END - ".__CLASS__.' -> '.__FUNCTION__);
    }
    
    public function failed(Exception $exception)
    {
        \Log::info("START - ".__CLASS__.' -> '.__FUNCTION__);
    /*
        // trigger the Event 'DanteExceptionWasThrownEvent' to send email
        $eventArray['message']          = 'Error into: '.__CLASS__; 
        $eventArray['status']           = 500; 
        $eventArray['random_string']    = config('dante.random_string');
        $eventArray['log_file']         = config('dante.log_file');
        \Log::debug(" eventArray:", $eventArray);
        event(new DanteExceptionWasThrownEvent($eventArray));
    */
        \Log::info("END - ".__CLASS__.' -> '.__FUNCTION__);
    }
}

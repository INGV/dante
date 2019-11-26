<?php

namespace App\Api\v1\Controllers;

use Illuminate\Http\Request;

use App\Api\v1\Models\InsertEwModel;
use Illuminate\Support\Facades\Validator;
use App\Api\v1\Controllers\InsertController;

class InsertEwController extends DanteBaseController
{
    protected $httpStatusCodeToReturn = 201;
    
    public function quake2k(Request $request) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        
        $input_parameters = $request->all();
        
        /* Validate '$input_parameters'; it must have 'data' array */
        $this->validateInputToContainsData($input_parameters);
        
        /* Validate '$input_parameters['data']' contains 'ewLogo' */
        InsertEwModel::validateInputToContainsEwLogo($input_parameters['data']);
        
        /* Validate '$input_parameters['data']' contains 'ewMessage' */
        InsertEwModel::validateInputToContainsEwMessage($input_parameters['data']);
        
        /* Get 'ewLogo' and 'ewMessage' */
        $ewLogo = $input_parameters['data']['ewLogo'];
        $ewMessage = $input_parameters['data']['ewMessage'];
        
        /* Validate ewLogo */
        InsertEwModel::validateEwLogo($ewLogo);
        
        /* START - Validator for ewMessage */
        $validator_default_check    = config('dante.validator_default_check');
        $validator_default_message  = config('dante.validator_default_messages');        
        $validator = Validator::make($ewMessage, [
            'quakeId'       => 'integer|required',
            'originTime'    => $validator_default_check['data_time_with_msec'],
            'latitude'      => $validator_default_check['lat'],
            'longitude'     => $validator_default_check['lon'],
            'depth'         => $validator_default_check['depth'],
            'rms'           => 'numeric|required',
            'dmin'          => 'numeric|required',
            'ravg'          => 'numeric|required',
            'gap'           => 'integer|required',
            'nph'           => 'integer|required'
                
        ], $validator_default_message)->validate();
        /* END - Validator for ewMessage */
        
        /* Get instance of 'InsertController' */
        $insertController = new InsertController; 
        
        /* Build 'event' section */
        $dataToInsert['data']['event']['provenance_name']           = $ewLogo['installation'];
        $dataToInsert['data']['event']['provenance_softwarename']   = $ewLogo['module'];
        $dataToInsert['data']['event']['provenance_username']       = $ewLogo['user'];
        $dataToInsert['data']['event']['provenance_hostname']       = $ewLogo['hostname'];
        $dataToInsert['data']['event']['provenance_instance']       = $ewLogo['instance'];
        $dataToInsert['data']['event']['id_locator']                = $ewMessage['quakeId'];

		if($ewMessage['nph'] == 0) {
			$dataToInsert['data']['event']['type_event']	= 'not existing';
		} else {
			$dataToInsert['data']['event']['type_event']	= 'earthquake';
		}

		/* Insert event */
		$outputToReturn = $insertController->store($dataToInsert);
                
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $outputToReturn;
    }
    
    public function pick_scnl(Request $request) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        
        $input_parameters = $request->all();
        
        /* Validate '$input_parameters'; it must have 'data' array */
        $this->validateInputToContainsData($input_parameters);       
        
        /* Validate '$input_parameters['data']' contains 'ewLogo' */
        InsertEwModel::validateInputToContainsEwLogo($input_parameters['data']);
        
        /* Validate '$input_parameters['data']' contains 'ewMessage' */
        InsertEwModel::validateInputToContainsEwMessage($input_parameters['data']);
        
        /* Get 'ewLogo' and 'ewMessage' */
        $ewLogo = $input_parameters['data']['ewLogo'];
        $ewMessage = $input_parameters['data']['ewMessage'];
        
        /* Validate ewLogo */
        InsertEwModel::validateEwLogo($ewLogo);
        
        /* START - Validator for ewMessage */
        $validator_default_check    = config('dante.validator_default_check');
        $validator_default_message  = config('dante.validator_default_messages');        
        $validator = Validator::make($ewMessage, [
            'pickId'       => 'integer|required',
            'station'      => $validator_default_check['sta'],
            'network'      => $validator_default_check['net'],
            'component'    => $validator_default_check['cha'],
            'location'     => $validator_default_check['loc'],
            'firstMotion'  => 'string|size:1|nullable',
            'pickWeight'   => $validator_default_check['weight_integer'],
            'timeOfPick'   => $validator_default_check['data_time_with_msec'],
            'pAmplitude.0' => 'numeric|nullable',
            'pAmplitude.1' => 'numeric|nullable',
            'pAmplitude.2' => 'numeric|nullable',
        ], $validator_default_message)->validate();
        /* END - Validator for ewMessage */
        
        /* Get instance of 'InsertController' */
        $insertController = new InsertController; 
        
        /* Build 'picks' array */
        $dataToInsert['data']['picks'][0]['id_picker']                 = $ewMessage['pickId'];
        $dataToInsert['data']['picks'][0]['arrival_time']              = $ewMessage['timeOfPick'];
        $dataToInsert['data']['picks'][0]['weight_picker']             = $ewMessage['pickWeight'];
        $dataToInsert['data']['picks'][0]['firstmotion']               = $ewMessage['firstMotion'];
        $dataToInsert['data']['picks'][0]['scnl_net']                  = $ewMessage['network'];
        $dataToInsert['data']['picks'][0]['scnl_sta']                  = $ewMessage['station'];
        $dataToInsert['data']['picks'][0]['scnl_cha']                  = $ewMessage['component'];
        $dataToInsert['data']['picks'][0]['scnl_loc']                  = $ewMessage['location'];
        $dataToInsert['data']['picks'][0]['provenance_name']           = $ewLogo['installation'];
        $dataToInsert['data']['picks'][0]['provenance_softwarename']   = $ewLogo['module'];
        $dataToInsert['data']['picks'][0]['provenance_username']       = $ewLogo['user'];
        $dataToInsert['data']['picks'][0]['provenance_hostname']       = $ewLogo['hostname'];
        $dataToInsert['data']['picks'][0]['provenance_instance']       = $ewLogo['instance'];

        /* Insert pick */
        $outputToReturn = $insertController->store($dataToInsert);

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $outputToReturn;
    }
}
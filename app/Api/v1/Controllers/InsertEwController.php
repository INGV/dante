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
        
        // Get instance of 'InsertController'
        $insertController = new InsertController; 
        
        // Build 'event' section
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
}
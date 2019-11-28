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
        $ewLogo     = $input_parameters['data']['ewLogo'];
        $ewMessage  = $input_parameters['data']['ewMessage'];
        
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
    
    public function hyp2000arc(Request $request) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        
        $input_parameters = $request->all();

        /* Validate '$input_parameters'; it must have 'data' array */
        $this->validateInputToContainsData($input_parameters);
        
        /* Validate '$input_parameters['data']' contains 'ewLogo' */
        InsertEwModel::validateInputToContainsEwLogo($input_parameters['data']);
        
        /* Validate '$input_parameters['data']' contains 'ewMessage' */
        InsertEwModel::validateInputToContainsEwMessage($input_parameters['data']);
        
        /* Get 'ewLogo' and 'ewMessage' */
        $ewLogo     = $input_parameters['data']['ewLogo'];
        $ewMessage  = $input_parameters['data']['ewMessage'];
        
        /* Validate ewLogo */
        InsertEwModel::validateEwLogo($ewLogo);
        
        /* Validate '$ewMessage' */
        InsertEwModel::validateHyp2000ArcEwMessage($ewMessage);
        
        // Build 'hypocenter' section
        $hypocenterToInsert['ot']                       = $ewMessage['originTime'];
        $hypocenterToInsert['lat']                      = $ewMessage['latitude'];
        $hypocenterToInsert['lon']                      = $ewMessage['longitude'];        
        $hypocenterToInsert['depth']                    = $ewMessage['depth'];
        $hypocenterToInsert['nph']                      = $ewMessage['nph'];
        $hypocenterToInsert['nph_s']                    = $ewMessage['nphS'];
		$hypocenterToInsert['nph_fm']                   = $ewMessage['nPfm'];
        $hypocenterToInsert['nph_tot']                  = $ewMessage['nphtot'];
        $hypocenterToInsert['azim_gap']                 = $ewMessage['gap'];
        $hypocenterToInsert['rms']                      = $ewMessage['rms'];
        $hypocenterToInsert['e0_az']                    = $ewMessage['e0az'];
        $hypocenterToInsert['e0_dip']                   = $ewMessage['e0dp'];
        $hypocenterToInsert['e0']                       = $ewMessage['e0'];
        $hypocenterToInsert['e1_az']                    = $ewMessage['e1az'];
        $hypocenterToInsert['e1_dip']                   = $ewMessage['e1dp'];
        $hypocenterToInsert['e1']                       = $ewMessage['e1'];
        $hypocenterToInsert['e2']                       = $ewMessage['e2'];
        $hypocenterToInsert['err_h']                    = $ewMessage['erh'];
        $hypocenterToInsert['err_z']                    = $ewMessage['erz'];
        $hypocenterToInsert['min_distance']             = $ewMessage['dmin'];
        $hypocenterToInsert['quality']                  = $ewMessage['ingvQuality'];
        $hypocenterToInsert['provenance_name']          = $ewLogo['installation'];
        $hypocenterToInsert['provenance_softwarename']  = $ewLogo['module'];
        $hypocenterToInsert['provenance_username']      = $ewLogo['user'];
        $hypocenterToInsert['provenance_hostname']      = $ewLogo['hostname'];
        $hypocenterToInsert['provenance_instance']      = $ewLogo['instance'];
        $hypocenterToInsert['type_hypocenter']          = (string)$ewMessage['version'];
        $hypocenterToInsert['model']                    = '2strati';
        $hypocenterToInsert['loc_program']              = 'binder';
        
        /* Check phases */
        if ( (isset($ewMessage['phases'])) && !empty($ewMessage['phases']) ) {
            $n_phase=-1;
            foreach ($ewMessage['phases'] as $phase) {
                /* Validate 'phase' */
				InsertEwModel::validateHyp2000ArcEwMessagePhase($phase);
                
                /* Build 'phase' section */
                if ( $phase['Ponset'] == "P" ) {
                    $n_phase++;
                    $phase['Plabel'] = $phase['Plabel'] ?? '';
                    // Build 'pick' section
                    $phaseToInsert['weight_picker']             = $phase['Pqual'];
                    $phaseToInsert['arrival_time']              = $phase['Pat'];
                    $phaseToInsert['firstmotion']               = $phase['Pfm'];
                    $phaseToInsert['pamp']                      = $phase['pamp'];
                    $phaseToInsert['isc_code']                  = $phase['Ponset'].''.$phase['Plabel'];
                    $phaseToInsert['scnl_net']                  = $phase['net'];
                    $phaseToInsert['scnl_sta']                  = $phase['sta'];
                    $phaseToInsert['scnl_cha']                  = $phase['comp'];
                    $phaseToInsert['scnl_loc']                  = $phase['loc'];
                    $phaseToInsert['provenance_name']           = $ewLogo['installation'];
                    $phaseToInsert['provenance_softwarename']   = $ewLogo['module'];
                    $phaseToInsert['provenance_username']       = $ewLogo['user'];
                    $phaseToInsert['provenance_hostname']       = $ewLogo['hostname'];
                    $phaseToInsert['provenance_instance']       = $ewLogo['instance'];
                    // Build 'phase' section
                    $phaseToInsert['residual']                  = $phase['Pres'];
                    $phaseToInsert['ep_distance']               = $phase['dist'];
                    $phaseToInsert['azimut']                    = $phase['azm'];
                    $phaseToInsert['take_off']                  = $phase['takeoff'];
                    $phaseToInsert['weight_phase_localization'] = $phase['Pwt'];
                    
                    $hypocenterToInsert['phases'][$n_phase]     = $phaseToInsert;
                }
                if ( $phase['Ponset'] == "S" ) {
                    $n_phase++;
                    $phase['Slabel'] = $phase['Slabel'] ?? '';
                    // Build 'pick' section
                    $phaseToInsert['weight_picker']             = $phase['Squal'];
                    $phaseToInsert['arrival_time']              = $phase['Sat'];
                    $phaseToInsert['firstmotion']               = $phase['Sfm'];
                    $phaseToInsert['pamp']                      = $phase['pamp'];
                    $phaseToInsert['isc_code']                  = $phase['Sonset'].$phase['Slabel'] ?? '';
                    $phaseToInsert['scnl_net']                  = $phase['net'];
                    $phaseToInsert['scnl_sta']                  = $phase['sta'];
                    $phaseToInsert['scnl_cha']                  = $phase['comp'];
                    $phaseToInsert['scnl_loc']                  = $phase['loc'];
                    $phaseToInsert['provenance_name']           = $ewLogo['installation'];
                    $phaseToInsert['provenance_softwarename']   = $ewLogo['module'];
                    $phaseToInsert['provenance_username']       = $ewLogo['user'];
                    $phaseToInsert['provenance_hostname']       = $ewLogo['hostname'];
                    $phaseToInsert['provenance_instance']       = $ewLogo['instance'];
                    // Build 'phase' section
                    $phaseToInsert['residual']                  = $phase['Sres'];
                    $phaseToInsert['ep_distance']               = $phase['dist'];
                    $phaseToInsert['azimut']                    = $phase['azm'];
                    $phaseToInsert['take_off']                  = $phase['takeoff'];
                    $phaseToInsert['weight_phase_localization'] = $phase['Swt'];
                    
                    $hypocenterToInsert['phases'][$n_phase]     = $phaseToInsert;
                }                
            }
        }

        /* START - 2/2 - Choice if store 'event' and 'hypocenter' or attach the 'hypocenter' to an existing 'event' */
        $dataToInsert['data']['event']['provenance_name']           = $ewLogo['installation'];
        $dataToInsert['data']['event']['provenance_softwarename']   = $ewLogo['module'];
        $dataToInsert['data']['event']['provenance_username']       = $ewLogo['user'];
        $dataToInsert['data']['event']['provenance_hostname']       = $ewLogo['hostname'];
        $dataToInsert['data']['event']['provenance_instance']       = $ewLogo['instance'];
        $dataToInsert['data']['event']['type_event']                = 'earthquake';
        $dataToInsert['data']['event']['id_locator']                = $ewMessage['quakeId'];
        $dataToInsert['data']['event']['hypocenters'][0]            = $hypocenterToInsert;
		/* END - 2/2 - Choice if store 'event' and 'hypocenter' or attach the 'hypocenter' to an existing 'event' */
        
        /* Get instance of 'InsertController' */
        $insertController = new InsertController;
        
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $insertController->store($dataToInsert);
    }
    
    public function magnitude(Request $request) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        
        $input_parameters = $request->all();
        
        /* Validate '$input_parameters'; it must have 'data' array */
        $this->validateInputToContainsData($input_parameters);
        
        /* Validate '$input_parameters['data']' contains 'ewLogo' */
        InsertEwModel::validateInputToContainsEwLogo($input_parameters['data']);
        
        /* Validate '$input_parameters['data']' contains 'ewMessage' */
        InsertEwModel::validateInputToContainsEwMessage($input_parameters['data']);
        
        /* Get 'ewLogo' and 'ewMessage' */
        $ewLogo     = $input_parameters['data']['ewLogo'];
        $ewMessage  = $input_parameters['data']['ewMessage'];
        
        /* Validate ewLogo */
        InsertEwModel::validateEwLogo($ewLogo);
        
        /* Validate '$ewMessage' */
        InsertEwModel::validateMagnitudeEwMessage($ewMessage);
		
        /* Get instance of 'InsertController' */
        $insertController = new InsertController;
		
        /* START - Check if hypocenter already exists */
		$getEvent = $insertController->getFilteredEvent([
			'instance'		=> $ewLogo['instance'], 
			'id_locator'	=> $ewMessage['quakeId']
			]);
        $getHypocenter = $getEvent
				->join('hypocenter',		'hypocenter.fk_event',	'=', 'event.id')
				->join('type_hypocenter',	'type_hypocenter.id',	'=', 'hypocenter.fk_type_hypocenter')
                ->where('type_hypocenter.name',						'=', $ewMessage['version'])
                ->select('hypocenter.id AS hypocenter__id')
                ->first();
       
        if ($getHypocenter->exists()) {
            // Build 'magnitude' section
            $magnitudeToInsert['mag']                       = $ewMessage['mag'];
            $magnitudeToInsert['err']                       = $ewMessage['error'];
            $magnitudeToInsert['quality']                   = $ewMessage['quality'];
            $magnitudeToInsert['min_dist']                  = $ewMessage['minDist'];
            $magnitudeToInsert['azimut']                    = $ewMessage['azimuth'];
            $magnitudeToInsert['nsta']                      = $ewMessage['nStations'];
            $magnitudeToInsert['ncha']                      = $ewMessage['nChannels'];
            //$magnitudeToInsert['nsta_used']               = $ewMessage[''];
            $magnitudeToInsert['mag_quality']               = $ewMessage['ingvQuality'];
            $magnitudeToInsert['type_magnitude']            = $ewMessage['magType'].'-'.$ewMessage['algorithm'];
            $magnitudeToInsert['provenance_name']           = $ewLogo['installation'];
            $magnitudeToInsert['provenance_instance']       = $ewLogo['instance'];
            $magnitudeToInsert['provenance_softwarename']   = $ewLogo['module'];
            $magnitudeToInsert['provenance_username']       = $ewLogo['user'];
            $magnitudeToInsert['provenance_hostname']       = $ewLogo['hostname'];
            $magnitudeToInsert['hypocenter_id']             = $getHypocenter->hypocenter__id;

            // Check phases arary (that are amplitudes in EW message)
            if ( (isset($ewMessage['phases'])) && !empty($ewMessage['phases']) ) {
                $n_amplitude=0;
                foreach ($ewMessage['phases'] as $phase) {
                    /* Validate ewMessage['phases'] */
                    InsertEwModel::validateMagnitudeEwMessagePhase($phase);

                    /* Build 'amplitude' section */
                    $amplitudeToInsert['scnl_net']                  = $phase['net'];
                    $amplitudeToInsert['scnl_sta']                  = $phase['sta'];
                    $amplitudeToInsert['scnl_cha']                  = $phase['comp'];
                    $amplitudeToInsert['scnl_loc']                  = $phase['loc'];
                    $amplitudeToInsert['type_amplitude']            = (strtolower($ewMessage['magType']) == 'ml' ? 'WoodAnderson' : 'unknown');
                    $amplitudeToInsert['type_magnitude']            = $magnitudeToInsert['type_magnitude'];
                    $amplitudeToInsert['time1']                     = $phase['time1'];
                    $amplitudeToInsert['amp1']                      = $phase['amp1'];
                    $amplitudeToInsert['period1']                   = $phase['period1'];
                    $amplitudeToInsert['time2']                     = $phase['time2'];
                    $amplitudeToInsert['amp2']                      = $phase['amp2'];
                    $amplitudeToInsert['period2']                   = $phase['period2'];
                    $amplitudeToInsert['mag']                       = $phase['mag'];
                    $amplitudeToInsert['ep_distance']               = $phase['dist'];
                    $amplitudeToInsert['mag_correction']            = $phase['corr'];
                    $amplitudeToInsert['provenance_name']           = $ewLogo['installation'];
                    $amplitudeToInsert['provenance_instance']       = $ewLogo['instance'];
                    $amplitudeToInsert['provenance_softwarename']   = $ewLogo['module'];
                    $amplitudeToInsert['provenance_username']       = $ewLogo['user'];
                    $amplitudeToInsert['provenance_hostname']       = $ewLogo['hostname'];

                    $magnitudeToInsert['amplitudes'][$n_amplitude]      = $amplitudeToInsert;

                    $n_amplitude++;
                }
            }
        } else {
            $text = 'The hypocenter with:'
                    . '"event.id_locator='.$ewMessage['quakeId'].'"'
                    . ', '
                    . '"provenance.instance='.$ewLogo['instance'].'"'
                    . ' and '                    
                    . '"hypocenter.fk_type_hypocenter=<id_of__type_hypocenter.name='.$ewMessage['version'].'>"'
                    . 'doesn\'t exists!';
            abort(422, $text);
        }
        /* END - Check if hypocenter already exists */
        
        //
        $dataToInsert['data']['hypocenter_id']    = $getHypocenter->hypocenter__id;
        $dataToInsert['data']['magnitudes'][0]    = $magnitudeToInsert;

        /* Store data */
        $output = $insertController->store($dataToInsert);

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $output;                    
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
        $ewLogo     = $input_parameters['data']['ewLogo'];
        $ewMessage  = $input_parameters['data']['ewMessage'];
        
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
    
    public function strongmotionii(Request $request) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        
        $input_parameters = $request->all();
        
        /* Validate '$input_parameters'; it must have 'data' array */
        $this->validateInputToContainsData($input_parameters);       
        
        /* Validate '$input_parameters['data']' contains 'ewLogo' */
        InsertEwModel::validateInputToContainsEwLogo($input_parameters['data']);
        
        /* Validate '$input_parameters['data']' contains 'ewMessage' */
        InsertEwModel::validateInputToContainsEwMessage($input_parameters['data']);
        
        /* Get 'ewLogo' and 'ewMessage' */
        $ewLogo     = $input_parameters['data']['ewLogo'];
        $ewMessage  = $input_parameters['data']['ewMessage'];
        
        /* Validate ewLogo */
        InsertEwModel::validateEwLogo($ewLogo);
        
        /* Validate '$ewMessage' */
        InsertEwModel::validateStrongmotioniiEwMessage($ewMessage);
		
        /* Get instance of 'InsertController' */
        $insertController = new InsertController;
		
        /* START - Check if hypocenter already exists */
		$getEvent = $insertController->getFilteredEvent([
			'instance'		=> $ewLogo['instance'], 
			'id_locator'	=> $ewMessage['quakeId']
			]);     

		if ($getEvent->exists()) {
			// Build 'strongmotion' section
			$strongmotionToInsert['t_dt']                       = $ewMessage['time'];
			$strongmotionToInsert['pga']                        = $ewMessage['pga'];
			$strongmotionToInsert['tpga_dt']                    = $ewMessage['pgaTime'];
			$strongmotionToInsert['pgv']                        = $ewMessage['pgv'];
			$strongmotionToInsert['tpgv_dt']                    = $ewMessage['pgvTime'];
			$strongmotionToInsert['pgd']                        = $ewMessage['pgd'];
			$strongmotionToInsert['tpgd_dt']                    = $ewMessage['pgdTime'];
			$strongmotionToInsert['alternate_time']             = $ewMessage['alternateTime'];
			$strongmotionToInsert['alternate_code']             = $ewMessage['alternateCode'];
			$strongmotionToInsert['scnl_net']					= $ewMessage['network'];
			$strongmotionToInsert['scnl_sta']					= $ewMessage['station'];
			$strongmotionToInsert['scnl_cha']					= $ewMessage['component'];
			$strongmotionToInsert['scnl_loc']					= $ewMessage['location'];
			$strongmotionToInsert['rsa']						= $ewMessage['RSA'];
			$strongmotionToInsert['provenance_name']            = $ewLogo['installation'];
			$strongmotionToInsert['provenance_instance']        = $ewLogo['instance'];
			$strongmotionToInsert['provenance_softwarename']	= $ewLogo['module'];
			$strongmotionToInsert['provenance_username']		= $ewLogo['user'];
			$strongmotionToInsert['provenance_hostname']		= $ewLogo['hostname'];
		} else {
            $text = 'The event with:'
                    . '"event.id_locator='.$ewMessage['quakeId'].'"'
                    . ', '
                    . '"provenance.instance='.$ewLogo['instance'].'"'
                    . 'doesn\'t exists!';
            abort(422, $text);
        }
        /* END - Check if hypocenter already exists */
        
        //
		$dataToInsert['data']['event_id']               = $getEvent->select('event.*')->first()->id;
		$dataToInsert['data']['strongmotions'][0]       = $strongmotionToInsert;

        /* Store data */
        $output = $insertController->store($dataToInsert);

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $output;
	}
}
<?php

namespace App\Api\v1\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Validator;

class InsertEwModel extends Model
{
    public static function validateInputToContainsEwLogo($input_parameters) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);

        $validator = Validator::make($input_parameters, [
            'ewLogo'            => 'required|array',
        ], ['required'   => 'The ":attribute" array key must exists and must be an array!'])->validate();
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
    }
    
    public static function validateInputToContainsEwMessage($input_parameters) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);

        $validator = Validator::make($input_parameters, [
            'ewMessage'            => 'required|array',
        ], ['required'   => 'The ":attribute" array key must exists and must be an array!'])->validate();
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
    }
    
    public static function validateEwLogo($input_parameters) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        
        $validator_default_message  = config('dante.validator_default_messages');        
        $validator = Validator::make($input_parameters, [
            'type'            => 'string|required',
            'module'          => 'string|required',
            'installation'    => 'string|required',
            'user'            => 'string|required',
            'hostname'        => 'string|required',
            'instance'        => 'string|required'
        ], $validator_default_message)->validate();
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
    }
    
	/**
     * @brief Validate hypocenter fields from 'ewMessage'
     * 
     * Validate a hypocenter fields from 'ewMessage' generated from 'ew2openapi' output
	 * 
	 * @param type $ewMessageHypocenter
	 */
    public static function validateHyp2000ArcEwMessage($ewMessageHypocenter) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        
        $validator_default_check    = config('dante.validator_default_check');
        $validator_default_message  = config('dante.validator_default_messages');        
        $validator = Validator::make($ewMessageHypocenter, [
            'quakeId'                       => 'integer|required',
            'version'                       => 'required',
            'originTime'                    => $validator_default_check['data_time_with_msec'],
            'latitude'                      => $validator_default_check['lat'],
            'longitude'                     => $validator_default_check['lon'],
            'depth'                         => $validator_default_check['depth'],
            'nph'                           => 'integer|nullable',
            'nphS'                          => 'integer|nullable',
            'nphtot'                        => 'integer|nullable',
            'nPfm'                          => 'integer|nullable',
            'gap'                           => 'integer|nullable',
            'dmin'                          => 'integer|nullable',
            'rms'                           => 'numeric|nullable',
            'e0az'                          => 'integer|nullable',
            'e0dp'                          => 'integer|nullable',
            'e0'                            => 'numeric|nullable',
            'e1az'                          => 'integer|nullable',
            'e1dp'                          => 'integer|nullable',
            'e1'                            => 'numeric|nullable',            
            'e2'                            => 'numeric|nullable',
            'erh'                           => 'numeric|nullable',
            'erz'                           => 'numeric|nullable',            
            'Md'                            => $validator_default_check['magnitude'],
            'reg'                           => 'string|nullable',
            'labelpref'                     => 'string|nullable',
            'Mpref'                         => 'numeric|nullable',
            'wtpref'                        => 'numeric|nullable',
            'mdtype'                        => 'string|nullable',
            'mdmad'                         => 'numeric|nullable',
            'mdwt'                          => 'numeric|nullable',
            'ingvQuality'                   => 'string|nullable',
            'numberOfAmpMagWeightCode'      => 'numeric|nullable',
            'medianAbsDiffAmpMag'           => 'numeric|nullable',
            'codeOfCrustAndDelayModel'      => 'string|nullable',
            'preferredMagLabel'             => 'string|size:1|nullable',
            'preferredMag'                  => 'numeric|nullable',
            'numberOfPreferredMags'         => 'numeric|nullable',
            'modelDepthType'                => 'string|size:1|nullable',
            'dominantCrustModelType'        => 'string|size:1|nullable',
            'earthquakeDepthDatum'          => 'integer|nullable',
            'geoidDepth'                    => 'numeric|nullable',
        ], $validator_default_message)->validate();

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
    }
	
	/**
     * @brief Validate a single phase fields from 'ewMessage'
     * 
     * Validate a single phase fields from 'ewMessage' generated from 'ew2openapi' output
	 * 
	 * @param type $phase
	 */
	public static function validateHyp2000ArcEwMessagePhase($phase) {
		\Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
		
		$validator_default_check    = config('dante.validator_default_check');
		$validator_default_message  = config('dante.validator_default_messages');				
		$validator = Validator::make($phase, [
			'sta'                   => 'required|'.$validator_default_check['sta'],
			'net'                   => $validator_default_check['net'],
			'comp'                  => $validator_default_check['cha'],
			'loc'                   => $validator_default_check['loc'],
			'Plabel'                => 'string|nullable',
			'Slabel'                => 'string|nullable',
			'Ponset'                => 'string|nullable',
			'Sonset'                => 'string|nullable',
			'Pres'                  => 'numeric|nullable',
			'Sres'                  => 'numeric|nullable',
			'Pqual'                 => 'integer|nullable|in:0,1,2,3,4,9', // '9' is not a valid value but sometimes it is used from EW when 'Pqual' is 'null'; this will be filtered in the 'IngvNTEwController.hyp2000arc()' method.
			'Squal'                 => 'integer|nullable|in:0,1,2,3,4,9', // '9' is not a valid value but sometimes it is used from EW when 'Pqual' is 'null'; this will be filtered in the 'IngvNTEwController.hyp2000arc()' method.
			'codalen'               => 'integer|nullable',
			'codawt'                => 'integer|nullable',
			'Pfm'                   => 'string|size:1|nullable',
			'Sfm'                   => 'string|size:1|nullable',
			'datasrc'               => 'string|nullable',            
			'Md'                    => 'numeric|nullable',            
			'azm'                   => 'numeric|nullable',
			'takeoff'               => 'integer|nullable',
			'dist'                  => $validator_default_check['distance'],
			'Pwt'                   => 'numeric|nullable',
			'Swt'                   => 'numeric|nullable',
			'pamp'                  => 'integer|nullable',
			'codalenObs'            => 'integer|nullable',
            'amplitude'             => 'numeric|nullable',
            'ampUnitsCode'          => 'integer|nullable',
            'ampType'               => 'integer|nullable',
            'ampMag'                => 'numeric|nullable',
            'ampMagWeightCode'      => 'integer|nullable',
            'importanceP'           => 'numeric|nullable',
            'importanceS'           => 'numeric|nullable',
		], $validator_default_message);
        
        /* Check 'P' fileds only when 'Ponset' is set */
        $validator->sometimes('Pat', $validator_default_check['data_time_with_msec'], function($input) {
            return isset($input->Ponset);
        });
        
        /* Check 'S' fileds only when 'Sonset' is set */
        $validator->sometimes('Sat', $validator_default_check['data_time_with_msec'], function($input) {
            return isset($input->Sonset);
        });
        
        $validator->validate();

		\Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
	}

	/**
     * @brief Validate magnitude fields from 'ewMessage'
     * 
     * Validate a magnitude fields from 'ewMessage' generated from 'ew2openapi' output
	 * 
	 * @param type $ewMessageHypocenter
	 */
	public static function validateMagnitudeEwMessage($ewMessage) {
		\Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
		
		$validator_default_check    = config('dante.validator_default_check');
		$validator_default_message  = config('dante.validator_default_messages');				
		$validator = Validator::make($ewMessage, [
            'quakeId'           => 'integer|required',
            'version'           => 'required',
            'mag'               => $validator_default_check['magnitude'],
            'error'             => $validator_default_check['error'],
            'quality'           => 'numeric|nullable',
            'minDist'           => $validator_default_check['distance'],
            'azimuth'           => $validator_default_check['azimuth'],
            'nStations'         => $validator_default_check['nsta'],
            'nChannels'         => $validator_default_check['ncha'],
            'qAuthor'           => 'string|nullable',
            'qddsVersion'       => 'integer|required',
            'iMagType'          => 'integer|required',
            'magType'           => $validator_default_check['type_magnitude'],
            'algorithm'         => 'string|nullable',
            'ingvQuality'       => $validator_default_check['mag_quality'] 
		], $validator_default_message)->validate();

		\Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
	}
	
	/**
     * @brief Validate a single amplitude ('phase' in the message) fields from 'ewMessage'
     * 
     * Validate a single amplitude ('phase' in the message) fields from 'ewMessage' generated from 'ew2openapi' output
	 * 
	 * @param type $phase
	 */
	public static function validateMagnitudeEwMessagePhase($phase) {
		\Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
		
		$validator_default_check    = config('dante.validator_default_check');
		$validator_default_message  = config('dante.validator_default_messages');				
		$validator = Validator::make($phase, [
			'sta'           => $validator_default_check['sta'],
			'net'           => $validator_default_check['net'],
			'comp'          => $validator_default_check['cha'],
			'loc'           => $validator_default_check['loc'],
			'mag'           => $validator_default_check['magnitude'],
			'dist'          => $validator_default_check['distance'],
			'corr'          => 'numeric|nullable',
			'time1'         => $validator_default_check['data_time_with_msec'],
			'amp1'          => 'numeric|nullable',
			'period1'       => 'numeric|nullable',
			'time2'         => $validator_default_check['data_time_with_msec'],
			'amp2'          => 'numeric|nullable',
			'period2'       => 'integer|nullable'
		], $validator_default_message)->validate();

		\Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
	}
	
	/**
     * @brief Validate strongmotion fields from 'ewMessage'
     * 
     * Validate a strongmotion fields from 'ewMessage' generated from 'ew2openapi' output
	 * 
	 * @param type $ewMessageStrongmotion
	 */
	public static function validateStrongmotioniiEwMessage($ewMessage) {
		\Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
		
		$validator_default_check    = config('dante.validator_default_check');
		$validator_default_message  = config('dante.validator_default_messages');				
		$validator = Validator::make($ewMessage, [
            'quakeId'			=> 'integer|required',
			'station'			=> $validator_default_check['sta'],
			'network'			=> $validator_default_check['net'],
			'component'			=> $validator_default_check['cha'],
			'location'			=> $validator_default_check['loc'],
			'qAuthor'           => 'nullable|string',
            'time'				=> $validator_default_check['data_time_with_msec'],
			'alternateTime'		=> 'nullable|date',
			'alternateCode'		=> 'nullable|integer',
			'pga'               => 'nullable|numeric',
			'pgaTime'			=> 'nullable|date',
			'pgv'				=> 'nullable|numeric',
			'pgvTime'			=> 'nullable|date',
			'pgd'				=> 'nullable|numeric',
			'pgdTime'			=> 'nullable|date',
			'RSA.*.value'		=> 'nullable|numeric',
			'RSA.*.period'		=> 'nullable|numeric',
		], $validator_default_message)->validate();

		\Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
	}
}
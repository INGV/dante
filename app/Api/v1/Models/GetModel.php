<?php

namespace App\Api\v1\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
use App\Api\v1\Models\DanteBaseModel;
use App\Api\v1\Models\Tables\EventModel;
use App\Api\v1\Models\Tables\HypocenterModel;
use App\Api\v1\Models\Tables\VwEventPrefModel;
use App\Api\v1\Models\Tables\TypeMagnitudeModel;
use App\Api\v1\Models\Tables\VwEventExtendedModel;

class GetModel extends Model
{
	public static function getEventsPref($input_parameters) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        $query = VwEventPrefModel::query();
        
        // START - Filter by starttime and endtime
        if( !empty($input_parameters['starttime']) ) {
            $query = $query->where('hyp_ot', '>=', $input_parameters['starttime']);
        }
        if( !empty($input_parameters['endtime']) ) {
            $query = $query->where('hyp_ot', '<=', $input_parameters['endtime']);
        }
        // END - Filter by starttime and endtime
        
        // START - Filter by orderBy
        if( !empty($input_parameters['orderby']) ) {
            $query = $query->orderBySplitted($input_parameters['orderby']);
        }
        // END - Filter by orderBy

        // START - Filter by magnitude
        if( !empty($input_parameters['minmag']) ) {
            $query = $query->whereRaw('ROUND(mag_mag, 1) >= ?', $input_parameters['minmag']);      
        }
        if( !empty($input_parameters['maxmag']) ) {
            $query = $query->whereRaw('ROUND(mag_mag, 1) <= ?', $input_parameters['maxmag']);
        }            
        // END - Filter by magnitude

        // START - Filter by depth
        if( !empty($input_parameters['mindepth']) ) {
            $query = $query->where('hyp_depth', '>=', $input_parameters['mindepth']);
        }
        if( !empty($input_parameters['maxdepth']) ) {
            $query = $query->where('hyp_depth', '<=', $input_parameters['maxdepth']);
        }            
        // END - Filter by depth            

        // START - Filter by type_hypocenter_value
		/*
        if( !empty($input_parameters['mintypehypvalue']) ) {
            $query = $query->where('type_hypocenter_value', '>=', $input_parameters['mintypehypvalue']);
        }
        if( !empty($input_parameters['maxtypehypvalue']) ) {
            $query = $query->where('type_hypocenter_value', '<=', $input_parameters['maxtypehypvalue']);
        }   
        if( !empty($input_parameters['wheretypehypvaluein']) ) {
            $explode_whereversionin = explode(',', $input_parameters['whereversionin']);
            $query = $query->whereIn('type_hypocenter_value', $explode_whereversionin);
        }
		*/
        // END - Filter by type_hypocenter_value

        // START - Filter by type_hypocenter_value
		/*
        if( !empty($input_parameters['regexptypehypvalue']) ) {
            $query = $query->where('type_hypocenter_value', 'regexp', $input_parameters['regexptypehypvalue']);
        }
		*/
        // END - Filter by type_hypocenter_value

        // START - Filters by coordinates for area-rectangle
        if(!empty($input_parameters['minlat']) ) {
            $query = $query->where('hyp_lat', '>=', $input_parameters['minlat']);
        }
        if(!empty($input_parameters['maxlat']) ) {
            $query = $query->where('hyp_lat', '<=', $input_parameters['maxlat']);
        }
        if(!empty($input_parameters['minlon']) ) {
            $query = $query->where('hyp_lon', '>=', $input_parameters['minlon']);
        }
        if(!empty($input_parameters['maxlon']) ) {
            $query = $query->where('hyp_lon', '<=', $input_parameters['maxlon']);
        }
        // END - Filters by coordinates for area-rectangle

        // START - Filter type_event
        $query = $query->whereNotIn('type_event_name', ['not existing', 'testing']);
        // END - Filter type_event            

        // START - Filters by coordinates for area-circle
        if(!empty($input_parameters['lat']) && !empty($input_parameters['lon']) ) {
            $lon_parameter = $input_parameters['lon'];
            $lat_parameter = $input_parameters['lat'];

			/* check radius either degrees or kilometers */
			if( isset($input_parameters['minradius']) && isset($input_parameters['maxradius']) ) {
				$query = $query->whereBetween(DB::raw(DanteBaseModel::sqlComputeDistanceDegree($lon_parameter, $lat_parameter, 'hyp_lon', 'hyp_lat')), [ $input_parameters['minradius'], $input_parameters['maxradius'] ]);
			} else if( isset($input_parameters['minradiuskm']) && isset($input_parameters['maxradiuskm']) ) {
				$query = $query->whereBetween(DB::raw(DanteBaseModel::sqlComputeDistanceKm($lon_parameter, $lat_parameter, 'hyp_lon', 'hyp_lat')), [ $input_parameters['minradiuskm'], $input_parameters['maxradiuskm'] ]);
			} else {
				abort(500, 'Error in: "Filters by coordinates for area-circle".');
			}
        }
        // END - Filters by coordinates for area-circle
        
        // START - Set page for pagination
        if( empty($input_parameters['page']) ) {
            $page = 1;
        } else {
            $page = $input_parameters['page'];
        }
        // END - Set page for pagination

        // START - Set page for pagination
        if( empty($input_parameters['limit']) ) {
            $input_parameters['limit'] = 1000;
        }
        // END - Set page for pagination
        
        // START - Build query to get 'count' for pagination
        $queryCount = $query->count();
        // END - Build query to get 'count' for pagination

        // START - Build query with 'offset' and 'limit' based on pagination
        $query = $query->offset(
                    ($page * $input_parameters['limit']) - $input_parameters['limit']
                )->limit($input_parameters['limit'] * $page);
        // END - Build query with 'offset' and 'limit' based on pagination
        
        // START - Build final array
        $count=0;
        $arrayToReturn=[];
        $getQuery = DanteBaseModel::queryCache($query);
        foreach ($getQuery->toArray() as $item) {
            $arrayToReturn[$count]['id']                                        =   $item['id'];
            $arrayToReturn[$count]['id_locator']                                =   $item['id_locator'];
            $arrayToReturn[$count]['fk_events_group']                           =   $item['fk_events_group'];
            $arrayToReturn[$count]['type_event']                                =   $item['type_event_name'];
            $arrayToReturn[$count]['modified']									=   $item['modified'];
            $arrayToReturn[$count]['inserted']									=   $item['inserted'];
            $arrayToReturn[$count]['provenance']['name']						=   $item['event_provenance_name'];
            $arrayToReturn[$count]['provenance']['instance']					=   $item['event_provenance_instance'];
            $arrayToReturn[$count]['provenance']['softwarename']				=   $item['event_provenance_softwarename'];

            $arrayToReturn[$count]['hypocenter']['id']                          =   $item['fk_pref_hyp'];
            $arrayToReturn[$count]['hypocenter']['ot']                          =   $item['hyp_ot']; // 'hyp_ot' is casted from 'newQuery()' function.
            $arrayToReturn[$count]['hypocenter']['lat']                         =   $item['hyp_lat'];
            $arrayToReturn[$count]['hypocenter']['lon']                         =   $item['hyp_lon'];
            $arrayToReturn[$count]['hypocenter']['err_ot']                      =   $item['hyp_err_ot'];
            $arrayToReturn[$count]['hypocenter']['err_h']                       =   $item['hyp_err_h'];
            $arrayToReturn[$count]['hypocenter']['err_z']                       =   $item['hyp_err_z'];
            $arrayToReturn[$count]['hypocenter']['err_lat']                     =   $item['hyp_err_lat'];
            $arrayToReturn[$count]['hypocenter']['err_lon']                     =   $item['hyp_err_lon'];
            $arrayToReturn[$count]['hypocenter']['depth']                       =   $item['hyp_depth'];
            $arrayToReturn[$count]['hypocenter']['quality']                     =   $item['hyp_quality'];
            $arrayToReturn[$count]['hypocenter']['type']                        =   $item['type_hypocenter_name'];
            $arrayToReturn[$count]['hypocenter']['value']                       =   $item['type_hypocenter_value'];
            $arrayToReturn[$count]['hypocenter']['region']                      =   $item['region'] ?? '';
            $arrayToReturn[$count]['hypocenter']['inserted']                    =   $item['hyp_inserted'];
            $arrayToReturn[$count]['hypocenter']['modified']                    =   $item['hyp_modified'];
            $arrayToReturn[$count]['hypocenter']['provenance']['name']          =   $item['hyp_provenance_name'];
            $arrayToReturn[$count]['hypocenter']['provenance']['instance']      =   $item['hyp_provenance_instance'];
            $arrayToReturn[$count]['hypocenter']['provenance']['softwarename']  =   $item['hyp_provenance_softwarename'];

            $arrayToReturn[$count]['magnitude']['id']                           =   $item['fk_pref_mag'];
            $arrayToReturn[$count]['magnitude']['name']                         =   $item['type_magnitude_name'];
            $arrayToReturn[$count]['magnitude']['value']                        =   $item['mag_mag'];
            $arrayToReturn[$count]['magnitude']['err']                          =   $item['mag_err'];
            $arrayToReturn[$count]['magnitude']['quality']                      =   $item['mag_quality'];
            $arrayToReturn[$count]['magnitude']['mag_quality']                  =   $item['mag_mag_quality'];
            $arrayToReturn[$count]['magnitude']['provenance']['name']           =   $item['mag_provenance_name'];
            $arrayToReturn[$count]['magnitude']['provenance']['instance']       =   $item['mag_provenance_instance'];
            $arrayToReturn[$count]['magnitude']['provenance']['softwarename']   =   $item['mag_provenance_softwarename'];

            $count++;
        }
        // END - Build final array
        
        // Reset array keys
        $arrayToReturn = DanteBaseModel::resetArrayKeys($arrayToReturn);

        $return = new \Illuminate\Pagination\LengthAwarePaginator($arrayToReturn, $queryCount, $input_parameters['limit'], $page, [
                    'path'  => \Request::url(),
                    'query' => \Request::query(),
                ]);
        
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $return;
    }
    
	public static function getEvents($input_parameters) 
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        $query = VwEventExtendedModel::query();

        // START - Filter by starttime and endtime
        if( !empty($input_parameters['starttime']) ) {
            $query = $query->where('hyp_ot', '>=', $input_parameters['starttime']);
        }
        if( !empty($input_parameters['endtime']) ) {
            $query = $query->where('hyp_ot', '<=', $input_parameters['endtime']);
        }
        // END - Filter by starttime and endtime
        
        // START - Filter by orderBy
        if( !empty($input_parameters['orderby']) ) {
            $query = $query->orderBySplitted($input_parameters['orderby']);
        }
        // END - Filter by orderBy

        // START - Filter by magnitude
        if( !empty($input_parameters['minmag']) ) {
            $query = $query->whereRaw('ROUND(mag_mag, 1) >= ?', $input_parameters['minmag']);      
        }
        if( !empty($input_parameters['maxmag']) ) {
            $query = $query->whereRaw('ROUND(mag_mag, 1) <= ?', $input_parameters['maxmag']);
        }            
        // END - Filter by magnitude

        // START - Filter by depth
        if( !empty($input_parameters['mindepth']) ) {
            $query = $query->where('hyp_depth', '>=', $input_parameters['mindepth']);
        }
        if( !empty($input_parameters['maxdepth']) ) {
            $query = $query->where('hyp_depth', '<=', $input_parameters['maxdepth']);
        }            
        // END - Filter by depth            

        // START - Filter by type_hypocenter_name  
        if( !empty($input_parameters['wheretypehypnamein']) ) {
            $explode_wheretypehypnamein = explode(',', $input_parameters['wheretypehypnamein']);
            $query = $query->whereIn('type_hypocenter_name', $explode_wheretypehypnamein);
        }
        // END - Filter by type_hypocenter_name

        // START - Filter by id_locator
        if( !empty($input_parameters['id_locator']) ) {
            $query = $query->where('id_locator', '=', $input_parameters['id_locator']);
        }
        // END - Filter by id_locator
		
        // START - Filter by fk_events_group
        if( !empty($input_parameters['event_group_id']) ) {
			$query = $query->where('fk_events_group', '=', $input_parameters['event_group_id']);
        }
        // END - Filter by fk_events_group
		 
        // START - Filter by event_provenance_instance
        if( !empty($input_parameters['whereinstancein']) ) {
			$explode_whereinstancein = explode(',', $input_parameters['whereinstancein']);
			$query = $query->whereIn('event_provenance_instance', $explode_whereinstancein);
        }
        // END - Filter by event_provenance_instance

        // START - Filters by coordinates for area-rectangle
        if(!empty($input_parameters['minlat']) ) {
            $query = $query->where('hyp_lat', '>=', $input_parameters['minlat']);
        }
        if(!empty($input_parameters['maxlat']) ) {
            $query = $query->where('hyp_lat', '<=', $input_parameters['maxlat']);
        }
        if(!empty($input_parameters['minlon']) ) {
            $query = $query->where('hyp_lon', '>=', $input_parameters['minlon']);
        }
        if(!empty($input_parameters['maxlon']) ) {
            $query = $query->where('hyp_lon', '<=', $input_parameters['maxlon']);
        }
        // END - Filters by coordinates for area-rectangle

        // START - Filter type_event
        $query = $query->whereNotIn('type_event_name', ['not existing', 'testing']);
        // END - Filter type_event            

        // START - Filters by coordinates for area-circle
        if(!empty($input_parameters['lat']) && !empty($input_parameters['lon']) ) {
            $lon_parameter = $input_parameters['lon'];
            $lat_parameter = $input_parameters['lat'];

			/* check radius either degrees or kilometers */
			if( isset($input_parameters['minradius']) && isset($input_parameters['maxradius']) ) {
				$query = $query->whereBetween(DB::raw(DanteBaseModel::sqlComputeDistanceDegree($lon_parameter, $lat_parameter, 'hyp_lon', 'hyp_lat')), [ $input_parameters['minradius'], $input_parameters['maxradius'] ]);
			} else if( isset($input_parameters['minradiuskm']) && isset($input_parameters['maxradiuskm']) ) {
				$query = $query->whereBetween(DB::raw(DanteBaseModel::sqlComputeDistanceKm($lon_parameter, $lat_parameter, 'hyp_lon', 'hyp_lat')), [ $input_parameters['minradiuskm'], $input_parameters['maxradiuskm'] ]);
			} else {
				abort(500, 'Error in: "Filters by coordinates for area-circle".');
			}
        }
        // END - Filters by coordinates for area-circle
        
        // START - Set page for pagination
        if( empty($input_parameters['page']) ) {
            $page = 1;
        } else {
            $page = $input_parameters['page'];
        }
        // END - Set page for pagination

        // START - Set page for pagination
        if( empty($input_parameters['limit']) ) {
            $input_parameters['limit'] = 1000;
        }
        // END - Set page for pagination
        
        // START - Build query to get 'count' for pagination
        $queryCount = $query->count();
        // END - Build query to get 'count' for pagination

        // START - Build query with 'offset' and 'limit' based on pagination
        $query = $query->offset(
                    ($page * $input_parameters['limit']) - $input_parameters['limit']
                )->limit($input_parameters['limit'] * $page);
        // END - Build query with 'offset' and 'limit' based on pagination
        
        // START - Build final array
        $count=0;
        $arrayToReturn=[];
        $getQuery = DanteBaseModel::queryCache($query);
        foreach ($getQuery->toArray() as $item) {
            $arrayToReturn[$count]['id']                                        =   $item['id'];
            $arrayToReturn[$count]['id_locator']                                =   $item['id_locator'];
            $arrayToReturn[$count]['fk_events_group']                           =   $item['fk_events_group'];
            $arrayToReturn[$count]['type_event']                                =   $item['type_event_name'];
            $arrayToReturn[$count]['modified']									=   $item['modified'];
            $arrayToReturn[$count]['inserted']									=   $item['inserted'];
            $arrayToReturn[$count]['provenance']['name']						=   $item['event_provenance_name'];
            $arrayToReturn[$count]['provenance']['instance']					=   $item['event_provenance_instance'];
            $arrayToReturn[$count]['provenance']['softwarename']				=   $item['event_provenance_softwarename'];

            $arrayToReturn[$count]['hypocenter']['id']                          =   $item['fk_pref_hyp'];
            $arrayToReturn[$count]['hypocenter']['ot']                          =   $item['hyp_ot']; // 'hyp_ot' is casted from 'newQuery()' function. 
            $arrayToReturn[$count]['hypocenter']['lat']                         =   $item['hyp_lat'];
            $arrayToReturn[$count]['hypocenter']['lon']                         =   $item['hyp_lon'];
            $arrayToReturn[$count]['hypocenter']['err_ot']                      =   $item['hyp_err_ot'];
            $arrayToReturn[$count]['hypocenter']['err_h']                       =   $item['hyp_err_h'];
            $arrayToReturn[$count]['hypocenter']['err_z']                       =   $item['hyp_err_z'];
            $arrayToReturn[$count]['hypocenter']['err_lat']                     =   $item['hyp_err_lat'];
            $arrayToReturn[$count]['hypocenter']['err_lon']                     =   $item['hyp_err_lon'];
            $arrayToReturn[$count]['hypocenter']['depth']                       =   $item['hyp_depth'];
            $arrayToReturn[$count]['hypocenter']['quality']                     =   $item['hyp_quality'];
            $arrayToReturn[$count]['hypocenter']['type']                        =   $item['type_hypocenter_name'];
            $arrayToReturn[$count]['hypocenter']['value']                       =   $item['type_hypocenter_value'];
            $arrayToReturn[$count]['hypocenter']['region']                      =   $item['region'] ?? '';
            $arrayToReturn[$count]['hypocenter']['inserted']                    =   $item['hyp_inserted'];
            $arrayToReturn[$count]['hypocenter']['modified']                    =   $item['hyp_modified'];
            $arrayToReturn[$count]['hypocenter']['provenance']['name']          =   $item['hyp_provenance_name'];
            $arrayToReturn[$count]['hypocenter']['provenance']['instance']      =   $item['hyp_provenance_instance'];
            $arrayToReturn[$count]['hypocenter']['provenance']['softwarename']	=   $item['hyp_provenance_softwarename'];

            $arrayToReturn[$count]['magnitude']['id']                           =   $item['fk_pref_mag'];
            $arrayToReturn[$count]['magnitude']['name']                         =   $item['type_magnitude_name'];
            $arrayToReturn[$count]['magnitude']['value']                        =   $item['mag_mag'];
            $arrayToReturn[$count]['magnitude']['err']                          =   $item['mag_err'];
            $arrayToReturn[$count]['magnitude']['quality']                      =   $item['mag_quality'];
            $arrayToReturn[$count]['magnitude']['mag_quality']                  =   $item['mag_mag_quality'];
            $arrayToReturn[$count]['magnitude']['provenance']['name']           =   $item['mag_provenance_name'];
            $arrayToReturn[$count]['magnitude']['provenance']['instance']       =   $item['mag_provenance_instance'];
            $arrayToReturn[$count]['magnitude']['provenance']['softwarename']	=   $item['mag_provenance_softwarename'];

            $count++;
        }
        // END - Build final array
        
        // Reset array keys
        $arrayToReturn = DanteBaseModel::resetArrayKeys($arrayToReturn);

        $return = new \Illuminate\Pagination\LengthAwarePaginator($arrayToReturn, $queryCount, $input_parameters['limit'], $page, [
                    'path'  => \Request::url(),
                    'query' => \Request::query(),
                ]);
        
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $return;
    }
    
    public static function getEvent($input_parameters)
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        
		/* Get event_id */
		if (isset($input_parameters['eventid'])) {
			$eventid = $input_parameters['eventid'];
		} else {
			$eventid = HypocenterModel::findOrFail($input_parameters['originid'])->fk_event;
		}
        
		/* Set query */
		$query = EventModel::with([
				'provenance',
				'type_event',
				'hypocenters' => function($query) use ($input_parameters) {
					if (isset($input_parameters['originid'])) {
						$query->where('hypocenter.id', '=', $input_parameters['originid']);
					}
				},
				'hypocenters.model',
				'hypocenters.provenance',
				'hypocenters.loc_program',
				'hypocenters.type_hypocenter',
				'hypocenters.hypocenter_region_name',
				'hypocenters.magnitudes',
				'hypocenters.magnitudes.amplitudes',
				'hypocenters.magnitudes.amplitudes.scnl',
				'hypocenters.magnitudes.amplitudes.type_amplitude',
				'hypocenters.magnitudes.amplitudes.provenance',
				'hypocenters.magnitudes.provenance',
				'hypocenters.magnitudes.type_magnitude',
				'hypocenters.picks.scnl',
				'hypocenters.picks.provenance'
				])->where('id', '=', $eventid);
                
        /* Send query and get data */
        $getQuery = DanteBaseModel::queryCache($query, 1)->toArray();

        /* Inizialize output array */
        $arrayToReturnNew = [];

        /* Build 'event' */
        $event = $getQuery[0];
        $arrayToReturnNew['event']['id']								= $event['id'];
        $arrayToReturnNew['event']['id_locator']						= $event['id_locator'];
        $arrayToReturnNew['event']['type_event']						= $event['type_event']['name'];
        $arrayToReturnNew['event']['provenance_name']					= $event['provenance']['name'];
        $arrayToReturnNew['event']['provenance_instance']				= $event['provenance']['instance'];
        $arrayToReturnNew['event']['provenance_softwarename']			= $event['provenance']['softwarename'];
        $arrayToReturnNew['event']['provenance_username']				= $event['provenance']['username'];
        $arrayToReturnNew['event']['provenance_hostname']				= $event['provenance']['hostname'];
        $arrayToReturnNew['event']['provenance_description']			= $event['provenance']['description'];
        $arrayToReturnNew['event']['modified']							= $event['modified'];
        $arrayToReturnNew['event']['inserted']							= $event['inserted'];

        /* Build 'hypocenter' */
        $hypocenter_count = 0;
        $arrayToReturnNew['event']['hypocenters'] = array();
        foreach ($event['hypocenters'] as $hypocenter) {
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['id']								= $hypocenter['id'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['ot']								= $hypocenter['ot'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['lat']								= $hypocenter['lat'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['lon']								= $hypocenter['lon'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['depth']							= $hypocenter['depth'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['err_ot']							= $hypocenter['err_ot'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['err_lat']							= $hypocenter['err_lat'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['err_lon']							= $hypocenter['err_lat'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['err_depth']						= $hypocenter['err_depth'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['err_h']							= $hypocenter['err_h'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['err_z']							= $hypocenter['err_z'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['confidence_lev']					= $hypocenter['confidence_lev'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['e0_az']							= $hypocenter['e0_az'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['e0_dip']							= $hypocenter['e0_dip'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['e0']								= $hypocenter['e0'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['e1_az']							= $hypocenter['e1_az'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['e1_dip']							= $hypocenter['e1_dip'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['e1']								= $hypocenter['e1'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['e2_az']							= $hypocenter['e2_az'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['e2_dip']							= $hypocenter['e2_dip'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['e2']								= $hypocenter['e2'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['fix_depth']						= $hypocenter['fix_depth'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['min_distance']					= $hypocenter['min_distance'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['max_distance']					= $hypocenter['max_distance'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['azim_gap']						= $hypocenter['azim_gap'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['sec_azim_gap']					= $hypocenter['sec_azim_gap'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['rms']								= $hypocenter['rms'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['w_rms']							= $hypocenter['w_rms'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['is_centroid']						= $hypocenter['is_centroid'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['nph']								= $hypocenter['nph'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['nph_s']							= $hypocenter['nph_s'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['nph_tot']							= $hypocenter['nph_tot'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['nph_fm']							= $hypocenter['nph_fm'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['quality']							= $hypocenter['quality'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['region']							= $hypocenter['hypocenter_region_name']['region'] ?? '';
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['type_hypocenter']					= $hypocenter['type_hypocenter']['name'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['model']							= $hypocenter['model']['name'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['loc_program']						= $hypocenter['loc_program']['name'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['provenance_instance']				= $hypocenter['provenance']['instance'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['provenance_softwarename']			= $hypocenter['provenance']['softwarename'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['provenance_username']				= $hypocenter['provenance']['username'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['provenance_hostname']				= $hypocenter['provenance']['hostname'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['provenance_description']			= $hypocenter['provenance']['description'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['modified']						= $hypocenter['modified'];
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['inserted']						= $hypocenter['inserted'];

            /* Build 'magnitudes' */
            $magnitude_count = 0;
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'] = array();
            foreach ($hypocenter['magnitudes'] as $magnitude) {
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['id']								= $magnitude['id'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['mag']								= $magnitude['mag'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['err']								= $magnitude['err'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['quality']							= $magnitude['quality'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['min_dist']						= $magnitude['min_dist'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['azimut']							= $magnitude['azimut'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['nsta']							= $magnitude['nsta'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['ncha']							= $magnitude['ncha'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['nsta_used']						= $magnitude['nsta_used'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['mag_quality']						= $magnitude['mag_quality'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['type_magnitude']					= $magnitude['type_magnitude']['name'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['provenance_name']					= $magnitude['provenance']['name'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['provenance_instance']				= $magnitude['provenance']['instance'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['provenance_softwarename']			= $magnitude['provenance']['softwarename'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['provenance_username']				= $magnitude['provenance']['username'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['provenance_hostname']				= $magnitude['provenance']['hostname'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['provenance_description']			= $magnitude['provenance']['description'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['modified']						= $magnitude['modified'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['inserted']						= $magnitude['inserted'];

                /* Build 'amplitudes' */
                $amplitude_count = 0;
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'] = array();
                foreach ($magnitude['amplitudes'] as $amplitude) {
                    //$arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['id']							= $amplitude->id;
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['time1']						= $amplitude['time1'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['amp1']						= $amplitude['amp1'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['period1']						= $amplitude['period1'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['time2']						= $amplitude['time2'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['amp2']						= $amplitude['amp2'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['period2']						= $amplitude['period2'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['type_amplitude']				= $amplitude['type_amplitude']['type'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['provenance_name']				= $amplitude['provenance']['name'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['provenance_instance']			= $amplitude['provenance']['instance'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['provenance_softwarename']		= $amplitude['provenance']['softwarename'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['provenance_username']			= $amplitude['provenance']['username'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['provenance_hostname']			= $amplitude['provenance']['hostname'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['provenance_description']		= $amplitude['provenance']['description'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['scnl_net']					= $amplitude['scnl']['net'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['scnl_sta']					= $amplitude['scnl']['sta'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['scnl_cha']					= $amplitude['scnl']['cha'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['scnl_loc']					= $amplitude['scnl']['loc'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['ep_distance']					= $amplitude['st_amp_mag']['ep_distance'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['hyp_distance']				= $amplitude['st_amp_mag']['hyp_distance'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['azimut']						= $amplitude['st_amp_mag']['azimut'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['mag']							= $amplitude['st_amp_mag']['mag'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['err_mag']						= $amplitude['st_amp_mag']['err_mag'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['mag_correction']				= $amplitude['st_amp_mag']['mag_correction'];
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['is_used']						= $amplitude['st_amp_mag']['is_used'];
                    // A solution to remove this 'type_magnitude' query, could be: https://stackoverflow.com/questions/50460159/l5-6-relation-on-pivot-table
                    $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['magnitudes'][$magnitude_count]['amplitudes'][$amplitude_count]['type_magnitude']				= TypeMagnitudeModel::findOrFail($amplitude['st_amp_mag']['fk_type_magnitude'])->toArray()['name'];

                    $amplitude_count++;
                }
                $magnitude_count++;

            }

            /* Build 'phases' */
            $phase_count = 0;
            $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'] = array();
            foreach ($hypocenter['picks'] as $pick) {
                //$arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['id']								= $pick->phase->id;
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['isc_code']						= $pick['phase']['isc_code'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['weight_picker']					= $pick['weight'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['arrival_time']					= $pick['arrival_time'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['err_arrival_time']				= $pick['err_arrival_time'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['firstmotion']						= $pick['firstmotion'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['emersio']							= $pick['emersio'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['pamp']							= $pick['pamp'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['provenance_name']					= $pick['provenance']['name'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['provenance_instance']				= $pick['provenance']['instance'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['provenance_softwarename']			= $pick['provenance']['softwarename'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['provenance_username']				= $pick['provenance']['username'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['provenance_hostname']				= $pick['provenance']['hostname'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['provenance_description']			= $pick['provenance']['description'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['scnl_net']						= $pick['scnl']['net'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['scnl_sta']						= $pick['scnl']['sta'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['scnl_cha']						= $pick['scnl']['cha'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['scnl_loc']						= $pick['scnl']['loc'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['ep_distance']						= $pick['phase']['ep_distance'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['hyp_distance']					= $pick['phase']['hyp_distance'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['azimut']							= $pick['phase']['azimut'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['take_off']						= $pick['phase']['take_off'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['polarity_is_used']				= $pick['phase']['polarity_is_used'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['arr_time_is_used']				= $pick['phase']['arr_time_is_used'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['residual']						= $pick['phase']['residual'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['teo_travel_time']					= $pick['phase']['teo_travel_time'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['weight_phase_a_priori']			= $pick['phase']['weight_in'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['weight_phase_localization']		= $pick['phase']['weight_out'];
                $arrayToReturnNew['event']['hypocenters'][$hypocenter_count]['phases'][$phase_count]['std_error']						= $pick['phase']['std_error'];

                $phase_count++;
            }

            $hypocenter_count++;
        }

        $return['data'] = $arrayToReturnNew;          
        
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $return;
    }
}

<?php

namespace App\Api\v1\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
use App\Api\v1\Models\DanteBaseModel;
use App\Api\v1\Models\Tables\VwEventPrefModel;
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
}

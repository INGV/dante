<?php

namespace App\Api\v1\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
use App\Api\v1\Models\Tables\PickModel;
use App\Api\v1\Models\Tables\ScnlModel;
use App\Api\v1\Models\Tables\EventModel;
use App\Api\v1\Models\Tables\ProvenanceModel;
use App\Api\v1\Models\Tables\TypeEventModel;
use App\Api\v1\Models\Tables\ModelModel;
use App\Api\v1\Models\Tables\LocProgramModel;
use App\Api\v1\Models\Tables\TypeHypocenterModel;
use App\Api\v1\Models\Tables\HypocenterModel;

class InsertModel extends Model
{
    public static function buildArrayWithOnlyFieldsSet($array, $arrayFields = [])
    {
        $arrayToReturn = [];
        foreach ($arrayFields as $value) {
			isset($array[$value]) ? $arrayToReturn[$value]=$array[$value] : null;
        }
        return $arrayToReturn;
    }

        /**
     * 
     */
    public static function provenanceFirstOrCreate($provenanceArray)
    {
        /* Set '$arrayRequired' with 'UNIQUE KEY' and with only fields set */
		$arrayRequired = [];
        $arrayFieldsToCheckRequired = [ 
            'provenance_name'			=>	'name',
			'provenance_username'		=>	'username',
			'provenance_hostname'		=>	'hostname',
            'provenance_instance'       =>	'instance',
            'provenance_softwarename'	=>	'softwarename',
		];
        foreach ($arrayFieldsToCheckRequired as $key => $value) {
			isset($provenanceArray[$key]) ? $arrayRequired[$value]=$provenanceArray[$key] : null ;
        }

        /* Set '$arrayOptional' with only fields set */
		$arrayOptional = [];
        $arrayFieldsToCheckOptional = [ 
            'provenance_description'	=>	'description',
            'provenance_priority'		=>	'priority',
		];
        foreach ($arrayFieldsToCheckOptional as $key => $value) {
			isset($provenanceArray[$key]) ? $arrayOptional[$value]=$provenanceArray[$key] : null ;
        }
		
        /*
         * NOTA: 'name', 'username', 'hostname', 'instance' and 'softwarename' are 'UNIQUE' in 'provenance' table;
         *  then, the check of 'firstOrCreate' must be executed only on these fields. If these 5 records do not exist, the INSERT will be done
         *  with all 7 fileds.
         */
        $provenanceInserted = ProvenanceModel::firstOrCreate(
            $arrayRequired,
            $arrayOptional
        );
                
		$provenanceOutput = ProvenanceModel::find($provenanceInserted->id);
        return $provenanceOutput;
    }

    /**
     * Used to insert 'event' from JSON.
     */        
    public static function insertEvent($event) 
    {	
        /* Set '$arrayFieldsSet' with only fields set */
        /*
        $arrayFieldsToCheck = [ 
            'id_locator',
            'fk_pref_hyp',
            'fk_pref_mag',
            'fk_events_group',
            'type_group'
            ];
        $arrayFieldsSet = self::buildArrayWithOnlyFieldsSet($event, $arrayFieldsToCheck);
        */
		/* Get foreign key id */
        $provenanceOutput = self::provenanceFirstOrCreate($event);
        $type_eventOutput = TypeEventModel::firstOrCreate([
            'name' => $event['type_event']
        ]);
        
        /* Add foreign key to '$arrayFieldsSet' */
        //$arrayFieldsSet['fk_type_event'] = $type_eventOutput->id;
        //$arrayFieldsSet['fk_provenance'] = $provenanceOutput->id;
        
        /* Add foreign key to '$event' */
        $event['fk_type_event'] = $type_eventOutput->id;
        $event['fk_provenance'] = $provenanceOutput->id;        

		/* Insert data */
        $eventInserted = EventModel::create(
            $event
        );
        
		return EventModel::find($eventInserted->id);
    }
    
    /**
     * Used to update 'event' from JSON.
     */        
    public static function updateEvent($eventToUpdateFromDb, $newEvent) 
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        
        // Check 'provenance'
        if (
                isset($newEvent['provenance_name']) && !empty($newEvent['provenance_name'])
                &&
                isset($newEvent['provenance_softwarename']) && !empty($newEvent['provenance_softwarename'])
                &&
                isset($newEvent['provenance_username']) && !empty($newEvent['provenance_username'])
                &&
                isset($newEvent['provenance_hostname']) && !empty($newEvent['provenance_hostname'])
            ) {
            $provenanceOutput = $this->provenanceFirstOrCreate($newEvent);
            $eventToUpdateFromDb->fk_provenance = $provenanceOutput->id;
        }

        // Check 'id_locator'
        if (isset($newEvent['id_locator']) && !empty($newEvent['id_locator'])) {
            $eventToUpdateFromDb->id_locator = $newEvent['id_locator'];
        }
        
        // Check 'fk_events_group'
        if (isset($newEvent['fk_events_group']) && !empty($newEvent['fk_events_group'])) {
            $eventToUpdateFromDb->fk_events_group = $newEvent['fk_events_group'];
        }
        
        // Check 'fk_pref_hyp'
        if (isset($newEvent['fk_pref_hyp']) && !empty($newEvent['fk_pref_hyp'])) {
            $eventToUpdateFromDb->fk_pref_hyp = $newEvent['fk_pref_hyp'];
        }
        
        // Check 'fk_pref_mag'
        if (isset($newEvent['fk_pref_mag']) && !empty($newEvent['fk_pref_mag'])) {
            $eventToUpdateFromDb->fk_pref_mag = $newEvent['fk_pref_mag'];
        }        
        
        // Check 'type_event'
        if (isset($newEvent['type_event']) && !empty($newEvent['type_event'])) {
            $type_eventOutput = TypeEventModel::firstOrCreate([
                'name' => $newEvent['type_event']
            ]);            
            $eventToUpdateFromDb->fk_type_event = $type_eventOutput->id;
        }
        
        $eventToUpdateFromDb->save();

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $eventToUpdateFromDb;
    }
    
    /**
     * Used to insert 'hypocenter' from JSON.
     */        
    public static function insertHypocenter($hypocenter) 
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        // set params to default 'null' if not set
        /*
        $arrayFieldsToSetNull = [
			'geom',
            'err_ot', 
            'err_lat', 
            'err_lon', 
            'err_depth', 
            'err_h', 
            'err_z', 
            'e0_az', 
            'e0_dip', 
            'e0', 
            'e1_az', 
            'e1_dip', 
            'e1', 
            'e2_az', 
            'e2_dip', 
            'e2', 
            'max_distance', 
            'min_distance',
            'azim_gap',
            'sec_azim_gap',
            'rms',
            'w_rms',
            'nph',
            'nph_s',
            'nph_tot',
            'nph_fm',
            'quality'
            ];
        foreach ($arrayFieldsToSetNull as $value) {
            $hypocenter[$value] = (isset($hypocenter[$value]) && !empty($hypocenter[$value])) ? $hypocenter[$value] : null;
        }
        
        // set params to default '0' if not set
        $arrayFieldsToSetZero = [
            'is_centroid', 
            'fix_depth'
            ];
        foreach ($arrayFieldsToSetZero as $value) {
            $hypocenter[$value] = (isset($hypocenter[$value]) && !empty($hypocenter[$value])) ? $hypocenter[$value] : 0;
        }
        */
        // set param to default '68.3' if not set
        //$hypocenter['confidence_lev'] = (isset($hypocenter['confidence_lev']) && !empty($hypocenter['confidence_lev'])) ? $hypocenter['confidence_lev'] : 68.3;

        /* Get foreign key id */
        $provenanceOutput = self::provenanceFirstOrCreate($hypocenter);        
        $modelOutput = ModelModel::firstOrCreate([
            'name'              => $hypocenter['model']
        ]);
        $loc_programOutput = LocProgramModel::firstOrCreate([
            'name'              => $hypocenter['loc_program']
        ]);
        $type_hypocenterOutput = TypeHypocenterModel::firstOrCreate([
            'name'              => $hypocenter['type_hypocenter'],
        ]);
        
        /* Add foreign key to '$hypocenter' */
        $hypocenter['fk_provenance']        = $provenanceOutput->id;
        $hypocenter['fk_type_hypocenter']   = $type_hypocenterOutput->id;
        $hypocenter['fk_model']             = $modelOutput->id;
        $hypocenter['fk_loc_program']       = $loc_programOutput->id;
        $hypocenter['fk_event']             = $hypocenter['eventid'];
        
        /* Add supplementary fields */
        $hypocenter['geom']                 = 'POINT('.$hypocenter['lon'].','.$hypocenter['lat'].')';

        /* Insert data */
        $hypocenterOutput = HypocenterModel::create($hypocenter);
        
        return HypocenterModel::find($hypocenterOutput->id);
    }
    
    /**
     * Used to insert 'pick' from JSON.
     */
    public static function insertPick($pick)
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);

        // set params to default 'null' if not set
        $arrayFieldsToSetNull = [
            'weight_picker',
            'err_arrival_time',
            'firstmotion',
            'emersio',
            'pamp',
            ];
        foreach ($arrayFieldsToSetNull as $value) {
            $pick[$value] = (isset($pick[$value]) && !empty($pick[$value])) ? $pick[$value] : null;
        }
        // set params to default '0' if not set
        $arrayFieldsToSetNull = [
            'id_picker'
            ];
        foreach ($arrayFieldsToSetNull as $value) {
            $pick[$value] = (isset($pick[$value]) && !empty($pick[$value])) ? $pick[$value] : 0;
        }

        $provenanceOutput = self::provenanceFirstOrCreate($pick);

        $scnlOutput = ScnlModel::firstOrCreate([
            'net'               => $pick['scnl_net'],
            'sta'               => $pick['scnl_sta'],
            'cha'               => $pick['scnl_cha'],
            'loc'               => $pick['scnl_loc'] ?? '--',
        ]);

        // CAST 'arrival_time' to 'DATETIME(3)' using MySQL.
        //  Eseguo il CAST via MySQL in quanto potrebbe variare rispetto a quello fatto di PHP e la UNIQUE potrebbe non verificarsi.
        $arrival_time_casted = \DB::select(DB::raw("SELECT CONVERT( CAST('".$pick['arrival_time']."' AS DATETIME(3)), CHAR) AS arrival_time" ))[0]->arrival_time;
        $pick['arrival_time'] = $arrival_time_casted;

        /*
         * NOTA: 'arrival_time', 'fk_provenance', and 'fk_scnl' are 'UNIQUE' in 'pick' table;
         *  then, the check of 'updateOrCreate' must be executed only on these fields. If these 4 records do not exist, the INSERT/UPDATE will be
         *  with all 9 fileds.
         */
        $pickOutput = PickModel::firstOrCreate(
            [
                'arrival_time'      => $pick['arrival_time'],
                'fk_provenance'     => $provenanceOutput->id,
                'fk_scnl'           => $scnlOutput->id,
            ],
            [
                'id_picker'         => $pick['id_picker'],
                'weight'            => $pick['weight_picker'],
                'err_arrival_time'  => $pick['err_arrival_time'],
                'firstmotion'       => $pick['firstmotion'],
                'emersio'           => $pick['emersio'],
                'pamp'              => $pick['pamp']
            ]
        );

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $pickOutput;
    }
}

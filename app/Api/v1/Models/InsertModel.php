<?php

namespace App\Api\v1\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
use App\Api\v1\Models\Tables\PickModel;
use App\Api\v1\Models\Tables\PhaseModel;
use App\Api\v1\Models\Tables\ScnlModel;
use App\Api\v1\Models\Tables\EventModel;
use App\Api\v1\Models\Tables\ProvenanceModel;
use App\Api\v1\Models\Tables\TypeEventModel;
use App\Api\v1\Models\Tables\AmplitudeModel;
use App\Api\v1\Models\Tables\ModelModel;
use App\Api\v1\Models\Tables\MagnitudeModel;
use App\Api\v1\Models\Tables\LocProgramModel;
use App\Api\v1\Models\Tables\TypeMagnitudeModel;
use App\Api\v1\Models\Tables\TypeAmplitudeModel;
use App\Api\v1\Models\Tables\TypeHypocenterModel;
use App\Api\v1\Models\Tables\StAmpMagModel;
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
		/* Get foreign key id */
        $provenanceOutput = self::provenanceFirstOrCreate($event);
        $type_eventOutput = TypeEventModel::firstOrCreate([
            'name' => $event['type_event']
        ]);
                
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
        $hypocenter['fk_event']             = $hypocenter['event_id'];
        
        /* Add supplementary fields */
        $hypocenter['geom']                 = 'POINT('.$hypocenter['lon'].','.$hypocenter['lat'].')';

        /* Insert data */
        $hypocenterOutput = HypocenterModel::create($hypocenter);
        
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return HypocenterModel::find($hypocenterOutput->id);
    }
    
    /**
     * Used to insert 'magnitude' from JSON.
     */    
    public static function insertMagnitude($magnitude) 
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
		
		/* Get foreign key id */
		$provenanceOutput       = self::provenanceFirstOrCreate($magnitude);
        $type_magnitudeOutput   = TypeMagnitudeModel::firstOrCreate([
            'name' => $magnitude['type_magnitude']
        ]);
        
        /* Add foreign key to '$magnitude' */
        $magnitude['fk_provenance']     = $provenanceOutput->id;
        $magnitude['fk_hypocenter']		= $magnitude['hypocenter_id'];
        $magnitude['fk_type_magnitude'] = $type_magnitudeOutput->id;

        /* Insert data */
        $magnitudeOutput = MagnitudeModel::create($magnitude);

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return MagnitudeModel::find($magnitudeOutput->id);
    }
    
    /**
     * Used to insert 'amplitude' from JSON.
     */    
    public static function insertAmplitude($amplitude) 
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        
		/* Get foreign key id */
		$provenanceOutput       = self::provenanceFirstOrCreate($amplitude);    
        $scnlOutput             = ScnlModel::firstOrCreate([
            'net'                   => $amplitude['scnl_net'],
            'sta'                   => $amplitude['scnl_sta'],
            'cha'                   => $amplitude['scnl_cha'],
            'loc'                   => $amplitude['scnl_loc'] ?? '--',
        ]);
        $type_amplitudeOutput = TypeAmplitudeModel::firstOrCreate([
            'type'              => $amplitude['type_amplitude']
        ]);
        
        /* Add foreign key to '$amplitude' */
        $amplitude['fk_scnl']           = $scnlOutput->id;
        $amplitude['fk_provenance']     = $provenanceOutput->id;
        $amplitude['fk_type_amplitude'] = $type_amplitudeOutput->id;

        /* Build array with only field fillable for 'amplitude' table */
        $amplitudeWithOnlyFillableToStore = [];
        $amplitude__fillable = (new AmplitudeModel)->getFillable();
        foreach ($amplitude as $amplitudeKey => $amplitudeValue) {
            if (in_array($amplitudeKey, $amplitude__fillable)) {
                $amplitudeWithOnlyFillableToStore[$amplitudeKey] = $amplitudeValue;
            }
        }

        /* Insert data */
        $amplitudeOutput = AmplitudeModel::create($amplitudeWithOnlyFillableToStore);
        
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $amplitudeOutput;
    }
    
    public static function buildStAmpMagArray($amplitude)
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        $st_amp_magWithOnlyFillableToStore = [];
        $st_amp_mag__fillable = (new StAmpMagModel)->getFillable();
        foreach ($amplitude as $amplitudeKey => $amplitudeValue) {
            if (in_array($amplitudeKey, $st_amp_mag__fillable)) {
                $st_amp_magWithOnlyFillableToStore[$amplitudeKey] = $amplitudeValue;
            }
        }

        /* Get foreign key id */
        $type_magnitudeOutput = TypeMagnitudeModel::firstOrCreate([
            'name'              => $amplitude['type_magnitude']
        ]); 

        /* Add foreign key to 'st_amp_mag' */
        $st_amp_magWithOnlyFillableToStore['fk_type_magnitude']    = $type_magnitudeOutput->id;
            
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $st_amp_magWithOnlyFillableToStore;
    }

        /**
     * Used to insert 'pick' from JSON.
     */
    public static function insertPick($pick)
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        
		/* Get foreign key id */
		$provenanceOutput   = self::provenanceFirstOrCreate($pick);
        $scnlOutput         = ScnlModel::firstOrCreate([
            'net'               => $pick['scnl_net'],
            'sta'               => $pick['scnl_sta'],
            'cha'               => $pick['scnl_cha'],
            'loc'               => $pick['scnl_loc'] ?? '--',
        ]);
        
        /* Add foreign key to '$pick' */
        $pick['fk_scnl']           = $scnlOutput->id;
        $pick['fk_provenance']     = $provenanceOutput->id;
        
        /* CAST 'arrival_time' to 'DATETIME(3)' using MySQL.
         *  Eseguo il CAST via MySQL in quanto potrebbe variare rispetto a quello fatto di PHP e la UNIQUE potrebbe non verificarsi.
         */
        $arrival_time_casted = \DB::select(DB::raw("SELECT CONVERT( CAST('".$pick['arrival_time']."' AS DATETIME(3)), CHAR) AS arrival_time" ))[0]->arrival_time;
        $pick['arrival_time'] = $arrival_time_casted;
        
        /* Build array with only field fillable for 'pick' table */
        $pickWithOnlyFillableToStore = [];
        $pick__fillable = (new PickModel)->getFillable();
        foreach ($pick as $pickKey => $pickValue) {
            if (in_array($pickKey, $pick__fillable)) {
                $pickWithOnlyFillableToStore[$pickKey] = $pickValue;
            }
        }

        /* Fields  'UNIQUE' in 'pick' table */
        $pick_unique = array_intersect_key($pickWithOnlyFillableToStore, array_flip(['arrival_time', 'fk_provenance', 'fk_scnl']));
                
        /* Fields NOT 'UNIQUE' in 'pick' table */
        $pick_not_unique = array_diff($pickWithOnlyFillableToStore, $pick_unique);

        /*
         * NOTA: 'arrival_time', 'fk_provenance', and 'fk_scnl' are 'UNIQUE' in 'pick' table;
         *  then, the check of 'firstOrCreate' must be executed only on these fields. 
         *  If these 3 records do not exist, the INSERT will be done with all 9 fileds.
         */
        $pickOutput = PickModel::firstOrCreate(
            $pick_unique,
            $pick_not_unique
        );

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $pickOutput;
    }
    
    public static function buildPhaseArray($phase)
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        $phaseWithOnlyFillableToStore = [];
        $phase__fillable = (new PhaseModel)->getFillable();
        foreach ($phase as $phaseKey => $phaseValue) {
            if (in_array($phaseKey, $phase__fillable)) {
                $phaseWithOnlyFillableToStore[$phaseKey] = $phaseValue;
            }
        }
            
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $phaseWithOnlyFillableToStore;
    }
}

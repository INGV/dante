<?php

namespace App\Api\v1\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Event;
use Illuminate\Support\Facades\Validator;
use App\Api\v1\Controllers\DanteBaseController;
use App\Api\v1\Controllers\NTSetPreferredController;
use App\Api\v1\Models\Tables\ScnlModel;
use App\Api\v1\Models\Tables\HypocenterModel;
use App\Api\v1\Models\Tables\EventModel;
use App\Api\v1\Models\Tables\MagnitudeModel;
use App\Api\v1\Models\Tables\StrongmotionModel;
use App\Api\v1\Models\Tables\StrongmotionAltModel;
use App\Api\v1\Models\Tables\StrongmotionRsaModel;
use App\Api\v1\Models\Tables\PickModel;
use App\Api\v1\Models\Tables\PhaseModel;
use App\Api\v1\Models\Tables\AmplitudeModel;
use App\Api\v1\Models\Tables\TypeMagnitudeModel;
use App\Api\v1\Models\Tables\TypeHypocenterModel;
use App\Api\v1\Models\Tables\TypeAmplitudeModel;
use App\Api\v1\Models\Tables\LocProgramModel;
use App\Api\v1\Models\Tables\ProvenanceModel;
use App\Api\v1\Models\Tables\StAmpMagModel;
use App\Api\v1\Models\InsertModel;
#use App\Api\V1\Jobs\DanteSetRegionJob;
#use App\Api\V1\Jobs\DanteInsertEventFromEventdbToSeisevJob;
#use App\Dante\Events\DanteExceptionWasThrownEvent;
use App\Api\Jobs\SetPreferredJob;
#use App\Api\V1\Jobs\DanteEventClusteringJob;
#use App\Api\v1\Controllers\NTRabbitMQPushController;


/**
 * @brief Used to insert seismic data into DB.
 *
 * Questa classe viene utilizzata per inserire 'event', 'hypocenter', 'magnitude', 'pick', 'amplitude', 'phase', ecc... nel DB.
 * L'idea alla base e' che ogni entita' che voglia inserire un "oggetto" di quelli riportati sopra nel DB, debba passare per questa classe.
 * 
 * Ad esempio, la classe 'IngvNTEwController' creta per ricevere i messaggi da EW, alla crea un oggetto di questa classe per effettuare l'inserimento nel DB.
 * 
 * Lo Swagger di esempio per inserire un evento si trova sotto la rotta '/event/1/' qui: [http://webservices.ingv.it/swagger-ui/dist/?url=http%3A%2F%2Fjabba.int.ingv.it%3A10013%2Fingvws%2Feventdb%2F1%2Fswagger_full.json](http://webservices.ingv.it/swagger-ui/dist/?url=http%3A%2F%2Fjabba.int.ingv.it%3A10013%2Fingvws%2Feventdb%2F1%2Fswagger_full.json)
 *
 */
class InsertController extends DanteBaseController
{
    protected $httpStatusCodeToReturn = 201;
    
    /*
     * 
     */
    public function validateProvenance($input_parameters) {
        /* Get default message */
        $validator_default_message              = config('dante.validator_default_messages');
        
        /* Get default validation rules from each model */        
        $validator_rules_for_provenance         = (new ProvenanceModel)->getValidatorRulesForStore();
        
        /* Add real value received from JSON */
        $validator_rules = [];
        $validator_rules['provenance_name']           = $validator_rules_for_provenance['name'];
        $validator_rules['provenance_priority']       = $validator_rules_for_provenance['priority'];
        $validator_rules['provenance_instance']       = $validator_rules_for_provenance['instance'];
        $validator_rules['provenance_username']       = $validator_rules_for_provenance['username'];
        $validator_rules['provenance_hostname']       = $validator_rules_for_provenance['hostname'];
        $validator_rules['provenance_description']    = $validator_rules_for_provenance['description'];
        $validator_rules['provenance_softwarename']   = $validator_rules_for_provenance['softwarename'];        
        Validator::make($input_parameters, $validator_rules, $validator_default_message)->validate();
    }
    
    /**
     * @brief Validate input 'event' JSON tag
     * 
     * Questo metodo, si occupa di validare tutti i campi all'interno del tag 'event' presenti nel JSON.
     * In caso di errore, chiama il metodo 'IngvUtilsModel::abortWhenValidatorFails' che provvedere a preparare l'errore per l'utente
     * 
     * @param type $input_parameters
     * @return Nothing
     */
    public function validateEvent($input_parameters) {       
        /* Validate Provenance */
        $this->validateProvenance($input_parameters);

        /* Validator Event */
        $validator_default_check        = config('dante.validator_default_check');
        $validator_default_message      = config('dante.validator_default_messages');        
        $validator = Validator::make($input_parameters, [
            'id_locator'                => 'integer',
            'type_event'                => 'string|required',
        ], $validator_default_message)->validate();
    }
	
    /**
     * @brief Get the unique 'event' by 'provenance.instance' and 'event.id_locator'
	 * 
     * @param array $array
     * @return $event
     */
    public function getFilteredEvent($array) {
        return EventModel::
                join('provenance', 'event.fk_provenance', '=', 'provenance.id')
                ->where([
                    ['provenance.instance', '=', $array['instance']],
                    ['event.id_locator',    '=', $array['id_locator']]
                ]);
    }
    
    /**
     * @brief Insert a complete JSON 'event' into the DB.
     * 
     * Questo metodo prende in input l'array completo 'event'; provvede ad effettuare la validazione dei soli dati presendi nella chiave 'event' e, nediante il metodo 'IngvEventModel::insertEvent()' provvede ad inserire un 'event' nel DB.
     * Successivamente, controlla se nell'array completo 'event' e' presente anche la chiave 'hypocenters' (cioe', se ci sono degli ipocentri da inserire per questo evento); in caso positivo, lancia il metodo '$this->insertHypocenters'.
     * 
     * @param type $data Array completo 'event' (vedere lo Swagger).
     * @return array $eventToReturn E' l'array di output a seguito dell'inseriemnto.
     */
    public function processEvent($data) {
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        $eventToStore = $data['event'];
                
        /* Validate 'event' */
        $this->validateEvent($eventToStore);
		
		/* Check if an 'event' with 'event.id_locator' and 'provenance.instance' already exists */
        $getEvent = $this->getFilteredEvent([
			'instance'      => $eventToStore['provenance_instance'], 
			'id_locator'    => $eventToStore['id_locator']
			]);

		\Log::debug(' 1/2 - an event with "instance='.$eventToStore['provenance_instance'].'" and "id_locator='.$eventToStore['id_locator'].'":');
		if ($getEvent->exists()) {
			$event = $getEvent->select('event.*')->first();
			\Log::debug('  2/2 - already exists and is "id='.$event->id.'".');
			$eventOutput = InsertModel::updateEvent($event, $eventToStore);
            $this->httpStatusCodeToReturn = 200;
		} else {
			\Log::debug('  2/2 - does not exist; inserting.');
			/* Insert event */
			$eventOutput = InsertModel::insertEvent($eventToStore);
        }
  
        /* Prepare output */
        $eventToReturn['event'] = $eventOutput->toArray();

        /* Check if 'hypocenters' array key, exists */
        if ( (isset($eventToStore['hypocenters'])) && !empty($eventToStore['hypocenters']) ) {
            $hypocentersInserted = $this->insertHypocenters($eventToStore, $eventOutput->id);          
            $eventToReturn['event'] = array_merge($eventToReturn['event'], $hypocentersInserted);
        }
        
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $eventToReturn;
    }   
    
    /**
     * @brief Insert a complete JSON 'hypocenters' into the DB.
     * 
     * $data is something like:
     * {
     *  eventid: 23123423
     *  hypocenters: [
     *  ]
     * }
     * 
     * where "eventid" could be null if exists an "event" block
     * 
     * @param $data is something like:
     * {
     *  eventid: 23123423
     *  hypocenters: [
     *  ]
     * }
     * 
     * @param type $eventidToAttachTo Description
     * 
     * @return Array Array di output a seguito dell'inserimento
     */
    public function insertHypocenters($data, $eventidToAttachTo=null) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        
        $validator_default_check    = config('dante.validator_default_check');
        $validator_default_message  = config('dante.validator_default_messages');
        $n_hypocenter=0;       
        
        /* Get '$hypocenters' array to process */
        $hypocenters = $data['hypocenters'];

        /* Get 'event_id' to attach hypocenters */
        if( isset($eventidToAttachTo) && !empty($eventidToAttachTo) ) {
            $event_id = $eventidToAttachTo;
        } elseif( isset($data['eventid']) && !empty($data['eventid']) ) {
            $event_id = $data['eventid'];
        } else {
            $event_id = null;
        }
        
        /*** START - Set validation rules to validate hypocenter ***/
        /* Get default validation rules from each model */ 
        $validator_rules_for_hypocenter         = (new HypocenterModel)->getValidatorRulesForStore();
        $validator_rules_for_type_hypocenter    = (new TypeHypocenterModel)->getValidatorRulesForStore(['removeUnique' => true]);
        $validator_rules_for_loc_program        = (new LocProgramModel)->getValidatorRulesForStore(['removeUnique' => true]);

        /* Copy validation rules for hypocenter, to final validation rules array */
        $validator_rules = $validator_rules_for_hypocenter;

        /* Remove foreign keys; because are 'required' by default */
        unset(
                $validator_rules['fk_event'],
                $validator_rules['fk_model'],
                $validator_rules['fk_provenance'],
                $validator_rules['fk_loc_program'],
                $validator_rules['fk_type_hypocenter'],
        );

        /* Add real value received from JSON */
        $validator_rules['type_hypocenter']           = $validator_rules_for_type_hypocenter['name'];
        $validator_rules['loc_program']               = $validator_rules_for_loc_program['name'];
        /*** END - Set validation rules to validate hypocenter ***/
        
        /* Processing hypocenters */
        foreach ($hypocenters as $hypocenter) {
            $hypocenter['event_id'] = $event_id;
            
            /*** START - Validate hypocenter ***/
            /* Validate Provenance */
            $this->validateProvenance($hypocenter);

            /* Validate */
            Validator::make($hypocenter, $validator_rules, $validator_default_message)->validate();
            /*** END - Validate hypocenter ***/

            // Insert hypocenter
            $hypocenterOutput = InsertModel::insertHypocenter($hypocenter);
            
            // Prepare output
            $hypocentersToReturn['hypocenters'][$n_hypocenter] = $hypocenterOutput->toArray();

            // Check magnitudes
            if ( (isset($hypocenter['magnitudes'])) && !empty($hypocenter['magnitudes']) ) {
                $magnitudesInserted = $this->insertMagnitudes($hypocenter, $hypocenterOutput->id);
                $hypocentersToReturn['hypocenters'][$n_hypocenter] = array_merge($hypocentersToReturn['hypocenters'][$n_hypocenter], $magnitudesInserted);
            }
            // Check phases
            if ( (isset($hypocenter['phases'])) && !empty($hypocenter['phases'])) {
                $phasesInserted = $this->insertPhases($hypocenter, $hypocenterOutput->id);
                $hypocentersToReturn['hypocenters'][$n_hypocenter] = array_merge($hypocentersToReturn['hypocenters'][$n_hypocenter], $phasesInserted);
            }
                        
            // Encrease n_hypocenter
            $n_hypocenter++;
        }
        
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $hypocentersToReturn;
    }

    public function insertMagnitudes($data, $hypocenteridToAttachTo=null) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);

        $validator_default_check    = config('dante.validator_default_check');
        $validator_default_message  = config('dante.validator_default_messages');
        $n_magnitude=0;
        
        /* Get '$magnitudes' array to process */
        $magnitudes = $data['magnitudes'];
        
        /* Get 'hypocenter_id' to attach to magnitudes to processed */
        if( isset($hypocenteridToAttachTo) && !empty($hypocenteridToAttachTo) ) {
            $hypocenter_id = $hypocenteridToAttachTo;
        } elseif( isset($data['hypocenter_id']) && !empty($data['hypocenter_id']) ) {
            $hypocenter_id = $data['hypocenter_id'];
        } else {
            $hypocenter_id = null;
        }

        /*** START - Set validation rules to validate magnitude ***/
        /* Get default validation rules from each model */ 
        $validator_rules_for_magnitude          = (new MagnitudeModel)->getValidatorRulesForStore();
        $validator_rules_for_type_magnitude     = (new TypeMagnitudeModel)->getValidatorRulesForStore(['removeUnique' => true]);

        /* Copy validation rules for magnitude, to final validation rules array */
        $validator_rules = $validator_rules_for_magnitude;

        /* Remove foreign keys; because are 'required' by default */
        unset(
                $validator_rules['fk_hypocenter'],
                $validator_rules['fk_type_magnitude'],
                $validator_rules['fk_provenance'],
        );

        /* Add real value received from JSON */
        $validator_rules['type_magnitude']      = $validator_rules_for_type_magnitude['name'];
        $validator_rules['hypocenter_id']       = $validator_default_check['hypocenter_id'];
        /*** END - Set validation rules to validate magnitude ***/
        
        /* Processing magnitudes */
        foreach ($magnitudes as $magnitude) {
            /* Set 'hypocenter_id' to attach to magnitude to processed */
            $magnitude['hypocenter_id'] = $hypocenter_id;
            
            /*** START - Validate magnitude ***/
            /* Validate Provenance */
            $this->validateProvenance($magnitude);

            /* Validate */
            Validator::make($magnitude, $validator_rules, $validator_default_message)->validate();
            /*** END - Validate magnitude ***/

            /* Insert magnitude */
            $magnitudeOutput = InsertModel::insertMagnitude($magnitude);

            /* Prepare output */
            $magnitudesToReturn['magnitudes'][$n_magnitude] = $magnitudeOutput->toArray();

            // Check amplitudes
            if ( (isset($magnitude['amplitudes'])) && !empty($magnitude['amplitudes']) ) {
                $amplitudesInserted = $this->insertAmplitudes($magnitude, $magnitudeOutput->id);
                $magnitudesToReturn['magnitudes'][$n_magnitude] = array_merge($magnitudesToReturn['magnitudes'][$n_magnitude], $amplitudesInserted);
            }
            
            // Encrease n_magnitude
            $n_magnitude++;            
        }
        
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $magnitudesToReturn;
    }
	
    public function insertStrongmotions($data, $eventidToAttachTo=null) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);

        $validator_default_check    = config('dante.validator_default_check');
        $validator_default_message  = config('dante.validator_default_messages');
        
        /* Get '$strongmotions' array to process */
        $strongmotions = $data['strongmotions'];

        /* Get 'event_id' to attach stronmotions */
        if( isset($eventidToAttachTo) && !empty($eventidToAttachTo) ) {
            $event_id = $eventidToAttachTo;
        } elseif( isset($data['event_id']) && !empty($data['event_id']) ) {
            $event_id = $data['event_id'];
        } else {
            $event_id = null;
        }
        
        /* Validate '$event_id' */
		$validator = Validator::make(['event_id' => $event_id], [
			'event_id'	=> $validator_default_check['event_id']
		], $validator_default_message)->validate();
        
		/* Validate that strongmotions contains arrays */
		$validator = Validator::make($strongmotions, [
			'*'	=> 'array'
		], ['array' => 'Array ":attribute" of "strongmotions" must contains a strongmotion array'])->validate();
        
        /*** START - Set validation rules to validate strongmotion ***/
        /* Get default validation rules from each model */ 
        $validator_rules_for_strongmotion       = (new StrongmotionModel)->getValidatorRulesForStore();
        $validator_rules_for_strongmotion_alt   = (new StrongmotionAltModel)->getValidatorRulesForStore();
        $validator_rules_for_strongmotion_rsa   = (new StrongmotionRsaModel)->getValidatorRulesForStore();
        $validator_rules_for_scnl               = (new ScnlModel)->getValidatorRulesForStore(['removeUnique' => true]);

        /* Copy validation rules for strongmotion, to final validation rules array */
        $validator_rules = $validator_rules_for_strongmotion;

        /* Remove foreign keys; because are 'required' by default */
        unset(
                $validator_rules['fk_scnl'],
                $validator_rules['fk_event'],
                $validator_rules['fk_provenance'],
        );

        /* Add real value received from JSON */
        $validator_rules['scnl_net']            = $validator_rules_for_scnl['net'];
        $validator_rules['scnl_sta']            = $validator_rules_for_scnl['sta'];
        $validator_rules['scnl_cha']            = $validator_rules_for_scnl['cha'];
        $validator_rules['scnl_loc']            = $validator_rules_for_scnl['loc'];
        $validator_rules['alternate_time']      = $validator_rules_for_strongmotion_alt['t_alt_dt'];
        $validator_rules['alternate_code']      = $validator_rules_for_strongmotion_alt['altcode'];
        $validator_rules['rsa.*.value']         = $validator_rules_for_strongmotion_rsa['value'];
        $validator_rules['rsa.*.period']         = $validator_rules_for_strongmotion_rsa['period'];
        /*** END - Set validation rules to validate strongmotion ***/

        /* Get 'event' Model */
        $event = EventModel::findOrFail($event_id);
        
        /* Processing strongmotions */
        $n_strongmotion=0;
        foreach ($strongmotions as $strongmotion) {
            $strongmotion['event_id'] = $event_id;
            
            /*** START - Validate strongmotion ***/
            /* Validate Provenance */
            $this->validateProvenance($strongmotion);

            /* Validate strongmotion params */
            Validator::make($strongmotion, $validator_rules, $validator_default_message)->validate();
            /*** END - Validate strongmotion ***/

            /* Insert strongmotion */
            $strongmotionOutput = InsertModel::insertStrongmotion($strongmotion);
                        
            // Prepare output
            $strongmotionsToReturn['strongmotions'][$n_strongmotion] = $strongmotionOutput->toArray();
            
            // Encrease n_strongmotion
            $n_strongmotion++;            
        }

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $strongmotionsToReturn;
    }
    
    public function insertPicks($data) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);

        $validator_default_check    = config('dante.validator_default_check');
        $validator_default_message  = config('dante.validator_default_messages');
        
        $picks = $data['picks'];
		
		// START - Validator for 'picks'
		$validator = Validator::make($picks, [
			'*'	=> 'array'
		], ['array' => 'Array ":attribute" of "picks" must contains a pick array'])->validate();
		// END - Validator for 'picks'
  
        // processing picks
        $n_pick = 0;
        foreach ($picks as $pick) {
            // START - Validator
            $validator = Validator::make($pick, [
                // 'phase' validation
                'id_picker'                 => 'integer|nullable',
                'weight_picker'             => $validator_default_check['weight_integer'],
                'arrival_time'              => $validator_default_check['data_time_with_msec'],
                'err_arrival_time'          => 'numeric|nullable',
                'firstmotion'               => 'string|size:1|nullable',
                'emersio'                   => 'string|size:1|nullable',
                'pamp'                      => 'numeric|nullable',
                'provenance_name'           => $validator_default_check['provenance__name'],
				'provenance_priority'       => $validator_default_check['provenance__priority'],
                'provenance_instance'       => $validator_default_check['provenance__instance'],
                'provenance_softwarename'   => $validator_default_check['provenance__softwarename'],
                'provenance_username'       => $validator_default_check['provenance__username'],
                'provenance_hostname'       => $validator_default_check['provenance__hostname'],
                'provenance_description'    => $validator_default_check['provenance__description'],
                'scnl_net'                  => $validator_default_check['net'],
                'scnl_sta'                  => $validator_default_check['sta'],
                'scnl_cha'                  => $validator_default_check['cha'],
                'scnl_loc'                  => $validator_default_check['loc'],
            ], $validator_default_message)->validate();
            // END - Validator

            // Insert pick
            $pickOutput = InsertModel::insertPick($pick);
            
            // Prepare output
            $picksToReturn['picks'][$n_pick] = $pickOutput->toArray();
            
            // Encrease n_pick
            $n_pick++;            
        }
        
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $picksToReturn;
    }
    
    public function insertPhases($data, $hypocenteridToAttachTo=null) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        
        $validator_default_check    = config('dante.validator_default_check');
        $validator_default_message  = config('dante.validator_default_messages');
        
        /* Get '$phases' array to process */
        $phases = $data['phases'];
        
        /* Get 'hypocenter_id' to attach to magnitudes to processed */
        if( isset($hypocenteridToAttachTo) && !empty($hypocenteridToAttachTo) ) {
            $hypocenter_id = $hypocenteridToAttachTo;
        } elseif( isset($data['hypocenter_id']) && !empty($data['hypocenter_id']) ) {
            $hypocenter_id = $data['hypocenter_id'];
        } else {
            $hypocenter_id = null;
        }
        
        /* Validate '$hypocenter_id' */
		$validator = Validator::make(['hypocenter_id' => $hypocenter_id], [
			'hypocenter_id'	=> $validator_default_check['hypocenter_id']
		], $validator_default_message)->validate();
        
		/* Validate that phases contains arrays */
		$validator = Validator::make($phases, [
			'*'	=> 'array'
		], ['array' => 'Array ":attribute" of "phases" must contains a phase array'])->validate();
        
        /*** START - Set validation rules to validate phase ***/
        /* Get default validation rules from each model */ 
        $validator_rules_for_pick               = (new PickModel)->getValidatorRulesForStore();
        $validator_rules_for_phase              = (new PhaseModel)->getValidatorRulesForStore();
        $validator_rules_for_scnl               = (new ScnlModel)->getValidatorRulesForStore(['removeUnique' => true]);

        /* Copy validation rules for magnitude, to final validation rules array */
        $validator_rules = $validator_rules_for_pick + $validator_rules_for_phase;

        /* Remove foreign keys; because are 'required' by default */
        unset(
                /* from 'pick' */
                $validator_rules['fk_scnl'],
                $validator_rules['fk_provenance'],                
                /* from 'phase' */
                $validator_rules['fk_pick'],
                $validator_rules['fk_hypocenter'],
        );

        /* Add real value received from JSON */
        $validator_rules['scnl_net']            = $validator_rules_for_scnl['net'];
        $validator_rules['scnl_sta']            = $validator_rules_for_scnl['sta'];
        $validator_rules['scnl_cha']            = $validator_rules_for_scnl['cha'];
        $validator_rules['scnl_loc']            = $validator_rules_for_scnl['loc'];
        /*** END - Set validation rules to validate phase ***/
        
        // Get 'hypocenter' Model
        $hypocenter = HypocenterModel::findOrFail($hypocenter_id);
		
        /* Processing phases */
        $n_phase=0;
        foreach ($phases as $phase) {
            /*** START - Validate phase ***/
            /* Validate Provenance */
            $this->validateProvenance($phase);
            
            /* Validate phase params */
            Validator::make($phase, $validator_rules, $validator_default_message)->validate();            
            /*** END - Validate phase ***/
            
            /* Insert pick */
            $pickOutput = InsertModel::insertPick($phase);
            
            /* Build pivot array fileds for many-to-many relation between 'hypocenter' and 'pick' (that is 'phase') */
            $phaseArray = InsertModel::buildPhaseArray($phase);

            /* Insert many-to-many relation between magnitude and amplitude */
            $hypocenter->picks()->attach($pickOutput->id, $phaseArray);
            
            /* Prepare output */
            //$phasesToReturn['picks'][$n_phase] = PickModel::with('phase')->findOrFail($pickOutput->id)->toArray();
            $phasesToReturn['phases'][$n_phase] = PhaseModel::with('pick')
                    ->where([
                            ['fk_pick', '=', $pickOutput->id],
                            ['fk_hypocenter', '=', $hypocenter->id]
                    ])->first()->toArray();

            /* Encrease n_phase */
            $n_phase++;
        }
        
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $phasesToReturn;
    }
    
    public function insertAmplitudes($data, $receivedMagnitude_id=null) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        
        $validator_default_check    = config('dante.validator_default_check');
        $validator_default_message  = config('dante.validator_default_messages');
        
        /* Get '$amplitudes' array to process */
        $amplitudes = $data['amplitudes'];

        /* Get 'magnitude_id' to attach to amplitudes to processed */
        if( isset($receivedMagnitude_id) && !empty($receivedMagnitude_id) ) {
            $magnitude_id = $receivedMagnitude_id;
        } elseif( isset($data['magnitude_id']) && !empty($data['magnitude_id']) ) {
            $magnitude_id = $data['magnitude_id'];
        } else {
            $magnitude_id = null;
        }
        
        /* Validate '$magnitude_id' */
		$validator = Validator::make(['magnitude_id' => $magnitude_id], [
			'magnitude_id'	=> $validator_default_check['magnitude_id']
		], $validator_default_message)->validate();
        
		/* Validate that amplitudes contain arrays */
		$validator = Validator::make($amplitudes, [
			'*'	=> 'array'
		], ['array' => 'Array ":attribute" of "amplitudes" must contains a amplitude array'])->validate();
        
        /*** START - Set validation rules to validate amplitude ***/
        /* Get default validation rules from each model */ 
        $validator_rules_for_amplitude          = (new AmplitudeModel)->getValidatorRulesForStore();
        $validator_rules_for_type_amplitude     = (new TypeAmplitudeModel)->getValidatorRulesForStore(['removeUnique' => true]);
        $validator_rules_for_scnl               = (new ScnlModel)->getValidatorRulesForStore(['removeUnique' => true]);
        $validator_rules_for_st_amp_mag         = (new StAmpMagModel)->getValidatorRulesForStore(['removeUnique' => true]);
        $validator_rules_for_type_magnitude     = (new TypeMagnitudeModel)->getValidatorRulesForStore(['removeUnique' => true]);

        /* Copy validation rules for amplitude, to final validation rules array */
        $validator_rules = $validator_rules_for_amplitude + $validator_rules_for_st_amp_mag;

        /* Remove foreign keys; because are 'required' by default */
        unset(
                /* from 'amplitude' */
                $validator_rules['fk_type_amplitude'],
                $validator_rules['fk_provenance'],
                $validator_rules['fk_scnl'],
                /* from 'st_amp_mag' */
                $validator_rules['fk_magnitude'],
                $validator_rules['fk_amplitude'],
                $validator_rules['fk_type_magnitude'],
        );

        /* Add real value received from JSON */
        $validator_rules['type_magnitude']      = $validator_rules_for_type_magnitude['name'];
        $validator_rules['type_amplitude']      = $validator_rules_for_type_amplitude['type'];
        $validator_rules['scnl_net']            = $validator_rules_for_scnl['net'];
        $validator_rules['scnl_sta']            = $validator_rules_for_scnl['sta'];
        $validator_rules['scnl_cha']            = $validator_rules_for_scnl['cha'];
        $validator_rules['scnl_loc']            = $validator_rules_for_scnl['loc'];
        /*** END - Set validation rules to validate amplitude ***/
        
        /* Get 'magnitude' Model */
        $magnitude = MagnitudeModel::findOrFail($magnitude_id);

        /* Processing amplitudes */
        $n_amplitude=0;
        foreach ($amplitudes as $amplitude) {          
            /*** START - Validate amplitude ***/
            /* Validate Provenance */
            $this->validateProvenance($amplitude);

            /* Validate */
            Validator::make($amplitude, $validator_rules, $validator_default_message)->validate();
            /*** END - Validate amplitude ***/

            /* Insert amplitude */
            $amplitudeOutput = InsertModel::insertAmplitude($amplitude);
            
            /* Build pivot array fileds for many-to-many relation between 'magnitude' and 'amplitude' (that is 'st_amp_mag') */
            $st_amp_magArray = InsertModel::buildStAmpMagArray($amplitude);

            /* Insert many-to-many relation between magnitude and amplitude */
            $magnitude->amplitudes()->attach($amplitudeOutput->id, $st_amp_magArray);
            
            /* Get many-to-many relation just inserted ('st_amp_mag') */
            $st_amp_mag = StAmpMagModel::where([
                ['fk_magnitude', '=', $magnitude_id],
                ['fk_amplitude', '=', $amplitudeOutput->id],
            ])->first();
            
            /* Prepare output */
            //$amplitudesToReturn['amplitudes'][$n_amplitude] = AmplitudeModel::with('st_amp_mag')->findOrFail($amplitudeOutput->id)->toArray();
            $amplitudesToReturn['amplitudes'][$n_amplitude]                 = $amplitudeOutput->toArray();
            $amplitudesToReturn['amplitudes'][$n_amplitude]['st_amp_mag']   = $st_amp_mag->toArray();

            /* Encrease n_amplitude */
            $n_amplitude++;                        
        }
        
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $amplitudesToReturn;
    }
    
    public function processRequestToInsert(Request $request)
    {       
        return $this->store($request->all());
    }
    
    public function setRabbitMQArray($type, $body) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
            if (config('dante.rabbitmq_push_enable')) {
                $rabbitmq_eventdb_type_to_push = config('dante.rabbitmq_eventdb_type_to_push');
                if (in_array($type, $rabbitmq_eventdb_type_to_push)) {
                    $this->rabbitMQArrayType = ['type' => $type, 'body' => $body];
                }
            } else {
                \Log::debug(" 'dante.rabbitmq_push_enable' is set to FALSE");
            }
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
    }

    public function store($input_parameters)
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);

        /* Validate '$input_parameters'; it must have 'data' array */
        $this->validateInputToContainsData($input_parameters);
        
        /* START - Insert new data */
        try {
			\Log::debug("  beginTransaction() - ".__FUNCTION__);
            \DB::beginTransaction();
            if( (isset($input_parameters['data']['event'])) && !empty($input_parameters['data']['event']) ) {
                $event = $input_parameters['data'];
                $eventReturned = $this->processEvent($event);
            } else if( (isset($input_parameters['data']['hypocenters'])) && !empty($input_parameters['data']['hypocenters']) ) {
                $hypocenters = $input_parameters['data'];
                $hypocentersReturned = $this->insertHypocenters($hypocenters);
                $output = $hypocentersReturned;
            } else if( (isset($input_parameters['data']['magnitudes'])) && !empty($input_parameters['data']['magnitudes']) ) {
                $magnitudes = $input_parameters['data'];
                $magnitudesReturned = $this->insertMagnitudes($magnitudes);
                $output = $magnitudesReturned;
            } else if( (isset($input_parameters['data']['phases'])) && !empty($input_parameters['data']['phases']) ) {
                $phases = $input_parameters['data'];
                $phasesReturned = $this->insertPhases($phases);
                $output = $phasesReturned;
            } else if( (isset($input_parameters['data']['picks'])) && !empty($input_parameters['data']['picks']) ) {
                $picks = $input_parameters['data'];
                $picksReturned = $this->insertPicks($picks);
                $output = $picksReturned;
            } else if( (isset($input_parameters['data']['amplitudes'])) && !empty($input_parameters['data']['amplitudes']) ) {
                $amplitudes = $input_parameters['data'];
                $amplitudesReturned = $this->insertAmplitudes($amplitudes);
                $output = $amplitudesReturned;
            } else if( (isset($input_parameters['data']['strongmotions'])) && !empty($input_parameters['data']['strongmotions']) ) {
                $strongmotions = $input_parameters['data'];
                $strongmotionsReturned = $this->insertStrongmotions($strongmotions);
                $output = $strongmotionsReturned;
            }
			\Log::debug("  commit() - ".__FUNCTION__);
            \DB::commit();
        } catch (Exception $ex) {
			\Log::debug("  rollBack() - ".__FUNCTION__);
            \DB::rollBack();
        }
    return response()->json($eventReturned, $this->httpStatusCodeToReturn);
        /* END - Insert new data */
        
		/* START - setPreferred hyp and mag */
        \Log::info("START - Call \"SetPreferredJob\" Job");
		if( (isset($input_parameters['data']['event'])) && !empty($input_parameters['data']['event']) ) {
            /* 'dispatchNow()' run the job syncronous; then It can return the value */
            $eventReturnedWithPreferredHypocenterAndMagnitude = SetPreferredJob::dispatchNow($eventReturned['event']['id']);
			$eventReturned['event']['fk_pref_hyp'] = $eventReturnedWithPreferredHypocenterAndMagnitude['fk_pref_hyp'];
			$eventReturned['event']['fk_pref_mag'] = $eventReturnedWithPreferredHypocenterAndMagnitude['fk_pref_mag'];
			$output = $eventReturned;
		} else if( (isset($input_parameters['data']['hypocenters'])) && !empty($input_parameters['data']['hypocenters']) ) {
            SetPreferredJob::dispatch($hypocenters['eventid']);
		} else if( (isset($input_parameters['data']['magnitudes'])) && !empty($input_parameters['data']['magnitudes']) ) {
			$eventId = HypocenterModel::find($magnitudes['hypocenter_id'])->fk_event;
            SetPreferredJob::dispatch($eventId);
		}
        \Log::info("END - Call \"SetPreferredJob\" Job");
		/* END - setPreferred hyp and mag */
        
		/* Get inserted hypocenter(s) */
		if ( isset($eventReturned['event']['hypocenters']) && !empty($eventReturned['event']['hypocenters']) ) {
			$hypocenters=$eventReturned['event']['hypocenters'];
		}
		if ( isset($hypocentersReturned['hypocenters']) && !empty($hypocentersReturned['hypocenters']) ) {
			$hypocenters=$hypocentersReturned['hypocenters'];
		}
        
		/* START - Synchronous actions */
		\Log::info("START - Actions");
		if ( isset($hypocenters) && !empty($hypocenters) ) {
			\Log::debug(" hypocenters to process:", $hypocenters);
			foreach ($hypocenters as $hypocenter) {
				$hypocenter_id=$hypocenter['id'];
                /* START - Set region */
                if (1==2) {
				\Log::info(" START - Call \"DanteSetRegionJob\" Job on hypocenter=".$hypocenter_id);
				try {
					\Log::debug("  dispatch");
					DanteSetRegionJob::dispatch($hypocenter_id)->onQueue('high');
					\Log::debug("  dispatched");
				}
				catch (Exception $e) {
					\Log::debug("  dispatch_exception");
					/* trigger the Event 'DanteExceptionWasThrownEvent' to send email */
					$eventArray['message']          = 'Error calling "DanteSetRegionJob" Job on hypocenter='.$hypocenter_id;
					$eventArray['status']           = 404;
					$eventArray['random_string']    = config('dante.random_string');
					$eventArray['log_file']         = config('dante.log_file');
					Event::fire(new DanteExceptionWasThrownEvent($eventArray));
				}
				\Log::info(" END - Call \"DanteSetRegionJob\" Job on hypocenter=".$hypocenter_id);
                }
				/* END - Set region */

				/* START - Run clustering */
				\Log::info(" START - Clustering");
				\Log::info("  ENABLE_CLUSTERING is set to \"".config('dante.enableClustering')."\" into the file \".env\".");
				if (config('dante.enableClustering')) {
					$hypocenter_ot=$hypocenter['ot'];
					\Log::debug("  Processing ot=\"".$hypocenter_ot."\"");
                    DanteEventClusteringJob::dispatch($hypocenter_ot);
				}
				\Log::info(" END - Clustering");
				/* END - Run clustering */
			}
		} else {
			\Log::debug(" hypocenters array, is not set.");
		}
		\Log::info("END - Actions");
		/* END - Synchronous actions */
        
        return response()->json($output, $this->httpStatusCodeToReturn);

        
        
        
        
        /* set headers */
        $headers = [
            'Location' => route('event.show', 'toDo')
        ];
        
        /* Set arrayOutputOptions */
        $this->setArrayOutputOptions(['status' => $this->httpStatusCodeToReturn, 'headers' => $headers]);       
        
        /* prepare output */
        $prepareOutput = $this->prepareOutput($output, $this->arrayOutputOptions);

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $prepareOutput;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        try {
            $event = EventModel::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            \Log::debug(" Exception");
            abort(404, 'event not found');
        }

        $event->delete();

        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        return response(null, 204);
    }
}
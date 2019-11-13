<?php

namespace App\Api\v1\Models\Tables;

use App\Api\v1\Models\DanteBaseModel;

class ProvenanceModel extends DanteBaseModel
{
    /* protected $connection = 'mysql_hdbrm_eventdb_ro'; */ /* set other DB connection */
    protected $table = 'provenance';
    /**
     * This array is used, from "__construct" to:
     * - build 'fillable' array (attributes that are mass assignable - 'id' and 'modified' are auto-generated)
     * 
     * And is also used from 'getValidatorRulesForStore' and 'getValidatorRulesForUpdate' (they are in the 'DanteBaseModel'), to
     *  centralize the Validator rules used in the Controller;
     *
     * @var array
     */  
    protected $baseArray = [
		'name'          => 'string',
		'instance'		=> 'required|string',
		'softwarename'  => 'required|string',
		'username'		=> 'nullable|string',
		'hostname'		=> 'nullable|string',
		'description'	=> 'nullable|string',
		'priority'		=> 'integer'
    ];   
    
    public function __construct(array $attributes = []) {
        parent::updateBaseArray();
        parent::setFillableFromBaseArray();
        parent::__construct($attributes);
    }
    
    /**
     * Get the event for the provenance.
     */
    public function event()
    {
        return $this->hasMany('App\Api\v1\Models\EventModel', 'fk_provenance', 'id');
    }
    
    public static function danteFirstOrCreate($arrayInput) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
		
        /* Check and set params with 'DEFAULT' or with 'DEFAULT NULL' in the DB table */
		$arrayRequired = [];
        $arrayFieldsToCheckRequired = [ 
            'provenance_name'			=>	'name',
			'provenance_username'		=>	'username',
			'provenance_hostname'		=>	'hostname',
            'provenance_softwarename'	=>	'softwarename',
		];
        foreach ($arrayFieldsToCheckRequired as $key => $value) {
			if(isset($arrayInput[$key]) && !empty($arrayInput[$key])) {$arrayRequired[$value]=$arrayInput[$key];}
        }
		
		/* Check and set params with 'DEFAULT' or with 'DEFAULT NULL' in the DB table */
		$arrayOptional = [];
        $arrayFieldsToCheckOptional = [ 
            'provenance_description'	=>	'description',
            'provenance_priority'		=>	'priority',
		];
        foreach ($arrayFieldsToCheckOptional as $key => $value) {
			if(isset($arrayInput[$key]) && !empty($arrayInput[$key])) {$arrayOptional[$value]=$arrayInput[$key];}
        }
		
		/* Set mandatory params */
		$arrayRequired['instance']		= $arrayInput['provenance_instance'];
		$arrayRequired['softwarename']	= $arrayInput['provenance_softwarename'];		
		
        /*
         * NOTA: 'name', 'instance', 'softwarename', 'username' and 'hostname' are 'UNIQUE' in 'provenance' table;
         *  then, the check of 'firstOrCreate' must be executed only on these fields. If these 5 records do not exist, the INSERT will be
         *  with all 6 fileds.
         */
        $provenanceInserted = IngvProvenanceModel::firstOrCreate(
                $arrayRequired,
                $arrayOptional
        );
		$provenanceOutput = IngvProvenanceModel::find($provenanceInserted->id);

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $provenanceOutput;
    }
}

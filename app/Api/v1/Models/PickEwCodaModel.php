<?php

namespace App\Api\v1\Models;

use App\Api\v1\Models\DanteBaseModel;

class PickEwCodaModel extends DanteBaseModel
{
    /* protected $connection = 'mysql_hdbrm_eventdb_ro'; */ /* set other DB connection */
    protected $table = 'pick_ew_coda';
    
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
		'fk_pick'		=> 'required|integer',
		'ccntr_0'		=> 'nullable|integer',
		'ccntr_1'		=> 'nullable|integer',
		'ccntr_2'		=> 'nullable|integer',
		'ccntr_3'		=> 'nullable|integer',
		'ccntr_4'		=> 'nullable|integer',
		'ccntr_5'		=> 'nullable|integer',
		'caav_0'		=> 'nullable|integer',
		'caav_1'		=> 'nullable|integer',
		'caav_2'		=> 'nullable|integer',
		'caav_3'		=> 'nullable|integer',
		'caav_4'		=> 'nullable|integer',
		'caav_5'		=> 'nullable|integer'
    ];   
    
    public function __construct(array $attributes = []) {
        parent::updateBaseArray();
        parent::setFillableFromBaseArray();
        parent::__construct($attributes);
    }
}

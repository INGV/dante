<?php

namespace App\Api\v1\Models;

use App\Api\v1\Models\DanteBaseModel;

class LocProgramModel extends DanteBaseModel
{
    /* protected $connection = 'mysql_hdbrm_eventdb_ro'; */ /* set other DB connection */
    protected $table = 'loc_program';
    
    /**
     * This array is used, from "__construct" to:
     * - build 'fillable' array (attributes that are mass assignable - 'id' and 'modified' are auto-generated)
     * 
     * And is also used from 'getValidatorRulesForStore' (that is in the 'IngvModel') and 'getValidatorRulesForUpdate', to:
     * - centralize the Validator rules used in the Controller;
     *
     * @var array
     */    
    protected $baseArray = [
		'name'		=> 'required|string|unique:loc_program,name'
    ];   
    
    public function __construct(array $attributes = []) {
        parent::updateBaseArray();
        parent::setFillableFromBaseArray();
        parent::__construct($attributes);
    }
}

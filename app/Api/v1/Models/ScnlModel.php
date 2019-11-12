<?php

namespace App\Api\v1\Models;

use App\Api\v1\Models\DanteBaseModel;

class ScnlModel extends DanteBaseModel
{
    /* protected $connection = 'mysql_hdbrm_eventdb_ro'; */ /* set other DB connection */
    protected $table = 'scnl';
    
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
		'net'		=> '---net---',
		'sta'		=> '---sta---',
		'cha'		=> '---cha---',
		'loc'		=> '---loc---',
		'lat'		=> '---lat---',
		'lon'		=> '---lon---',
		'elev'		=> '---elev---'
    ];   
    
    public function __construct(array $attributes = []) {
        parent::updateBaseArray();
        parent::setFillableFromBaseArray();
        parent::__construct($attributes);
    }
}

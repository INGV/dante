<?php

namespace App\Api\v1\Models;

use App\Api\v1\Models\DanteBaseModel;

class FocalmechanismModel extends DanteBaseModel
{
    /* protected $connection = 'mysql_hdbrm_eventdb_ro'; */ /* set other DB connection */
    protected $table = 'focalmechanism';
    
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
		'strike1'				=> 'integer',
		'dip1'					=> 'integer',
		'rake1'					=> 'integer',
		'strike2'				=> 'integer',
		'dip2'					=> 'integer',
		'rake2'					=> 'integer',
		'azim_gap'				=> 'numeric',
		'nsta_polarity'			=> 'integer',
		'misfit'				=> 'numeric',
		'stdr'					=> 'numeric',
		'rmsAngDiffAccPref'		=> 'numeric',
		'fracAcc30degPref'		=> 'numeric',
		'quality'				=> 'string|min:1|max:2|nullable',
		'url'					=> 'string',
		'fk_hypocenter'			=> '---hypocenter_id---',
		'fk_provenance'			=> '---provenance_id---',
		'fk_model'				=> '---model_id---',
		'fk_loc_program'		=> '---loc_program_id---'
    ];   
    
    public function __construct(array $attributes = []) {
        parent::updateBaseArray();
        parent::setFillableFromBaseArray();
        parent::__construct($attributes);
    }
}

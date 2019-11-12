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
     * And is also used from 'getValidatorRulesForStore' and 'getValidatorRulesForUpdate' (they are in the 'DanteBaseModel'), to
     *  centralize the Validator rules used in the Controller;
     *
     * @var array
     */   
    protected $baseArray = [
		'strike1'				=> 'nullable|integer',
		'dip1'					=> 'nullable|integer',
		'rake1'					=> 'nullable|integer',
		'strike2'				=> 'nullable|integer',
		'dip2'					=> 'nullable|integer',
		'rake2'					=> 'nullable|integer',
		'azim_gap'				=> 'nullable|numeric',
		'nsta_polarity'			=> 'nullable|integer',
		'misfit'				=> 'nullable|numeric',
		'stdr'					=> 'nullable|numeric',
		'rmsAngDiffAccPref'		=> 'nullable|numeric',
		'fracAcc30degPref'		=> 'nullable|numeric',
		'quality'				=> 'nullable|string|min:1|max:2',
		'url'					=> 'nullable|string',
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

<?php

namespace App\Api\v1\Models;

use App\Api\v1\Models\DanteBaseModel;

class PhaseModel extends DanteBaseModel
{
    /* protected $connection = 'mysql_hdbrm_eventdb_ro'; */ /* set other DB connection */
    protected $table = 'phase';
    
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
		'isc_code'          => 'required|string',
		'fk_hypocenter'		=> 'required|integer',
		'fk_pick'           => 'required|integer',
		'ep_distance'		=> 'nullable|numeric',
		'hyp_distance'		=> 'nullable|numeric',
		'azimut'            => 'nullable|numeric',
		'take_off'          => 'nullable|numeric',
		'polarity_is_used'	=> 'nullable|integer',
		'arr_time_is_used'	=> 'nullable|integer',
		'residual'          => 'nullable|numeric',
		'teo_travel_time'	=> 'date',
		'weight_in'         => 'nullable|integer',
		'weight_out'		=> 'nullable|numeric',
		'std_error'         => 'nullable|integer'
    ];   
    
    public function __construct(array $attributes = []) {
        parent::updateBaseArray();
        parent::setFillableFromBaseArray();
        parent::__construct($attributes);
    }
}

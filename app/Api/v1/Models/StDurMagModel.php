<?php

namespace App\Api\v1\Models;

use App\Api\v1\Models\DanteBaseModel;

class StDurMagModel extends DanteBaseModel
{
    /* protected $connection = 'mysql_hdbrm_eventdb_ro'; */ /* set other DB connection */
    protected $table = 'st_dur_mag';
    
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
        'fk_magnitude'      => 'required|integer',
        'fk_scnl'           => 'required|integer',
        'ep_distance'       => 'nullable|numeric',
        'hyp_distance'      => 'nullable|numeric',
        'azimut'            => 'nullable|numeric',
        'dur'               => 'nullable|numeric',
        'mag'               => 'required|---magnitude---',
        'err_mag'           => '---err---',
        'fk_type_magnitude' => 'required|numeric',
        'is_used'           => 'nullable|numeric'
    ];   
    
    public function __construct(array $attributes = []) {
        parent::updateBaseArray();
        parent::setFillableFromBaseArray();
        parent::__construct($attributes);
    }
}

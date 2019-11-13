<?php

namespace App\Api\v1\Models\Tables;

use App\Api\v1\Models\DanteBaseModel;

class StAmpMagModel extends DanteBaseModel
{
    /* protected $connection = 'mysql_hdbrm_eventdb_ro'; */ /* set other DB connection */
    protected $table = 'st_amp_mag';
    
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
		'fk_magnitude'		=> '---magnitude_id---',
		'fk_amplitude'		=> '---amplitude_id---',
		'ep_distance'		=> 'nullable|numeric',
		'hyp_distance'		=> 'nullable|numeric',
		'azimut'			=> 'nullable|numeric',
		'mag'				=> 'required|numeric',
		'err_mag'			=> '---error---',
		'mag_correction'	=> 'nullable|numeric',
		'is_used'			=> 'nullable|integer',
		'fk_type_magnitude'	=> '---type_magnitude_id---'
    ];   
    
    public function __construct(array $attributes = []) {
        parent::updateBaseArray();
        parent::setFillableFromBaseArray();
        parent::__construct($attributes);
    }
    
    /**
     * Get the type_magnitude record associated with the st_amp_mag.
     */
    public function type_magnitude()
    {
        return $this->hasOne('App\Api\v1\Models\TypeMagnitudeModel', 'id', 'fk_type_magnitude');
    }
}

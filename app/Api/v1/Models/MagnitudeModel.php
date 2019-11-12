<?php

namespace App\Api\v1\Models;

use App\Api\v1\Models\DanteBaseModel;

class MagnitudeModel extends DanteBaseModel
{
    /* protected $connection = 'mysql_hdbrm_eventdb_ro'; */ /* set other DB connection */
    protected $table = 'magnitude';
    
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
		'mag'               => 'required|---magnitude---',
		'err'               => '---error---',
		'quality'           => 'nullable|numeric',
		'min_dist'          => 'nullable|numeric',
		'azimut'            => 'nullable|numeric',
		'nsta'              => 'nullable|integer',
		'ncha'              => 'nullable|integer',
		'nsta_used'         => 'nullable|integer',
		'mag_quality'		=> '---mag_quality---',
		'fk_hypocenter'		=> 'required|integer',
		'fk_type_magnitude' => 'required|integer',
		'fk_provenance'		=> 'required|integer'
    ];   
    
    public function __construct(array $attributes = []) {
        parent::updateBaseArray();
        parent::setFillableFromBaseArray();
        parent::__construct($attributes);
    }
    
    /**
     * Get the hypocenter that owns the magnitude.
     */
    public function hypocenter()
    {
        return $this->belongsTo('App\Api\v1\Models\HypocenterModel', 'fk_hypocenter', 'id');
    }
    
    /**
     * Get the amplitudes that belong to magnitude.
     */
    public function amplitudes()
    {
        return $this->belongsToMany('App\Api\v1\Models\AmplitudeModel', 'st_amp_mag', 'fk_magnitude', 'fk_amplitude')->withPivot(
				'ep_distance',
				'hyp_distance',
				'azimut',
				'mag',
				'err_mag',
				'mag_correction',
				'is_used',
				'fk_type_magnitude'
				)
				->as('st_amp_mag')
				->withTimestamps();
    }

    /**
     * Get the type_magnitude record associated with the magnitude.
     */
    public function type_magnitude()
    {
        return $this->hasOne('App\Api\v1\Models\TypeMagnitudeModel', 'id', 'fk_type_magnitude');
    }
	
    /**
     * Get the provenance record associated with the magnitude.
     */
    public function provenance()
    {
        return $this->hasOne('App\Api\v1\Models\ProvenanceModel', 'id', 'fk_provenance');
    }
}

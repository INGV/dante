<?php

namespace App\Api\v1\Models\Tables;

use App\Api\v1\Models\DanteBaseModel;

use App\Api\v1\Models\Tables\PhaseModel;

class HypocenterModel extends DanteBaseModel
{
    protected $table = 'hypocenter';
    
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
		'ot'                    => '---data_time_with_msec---',
		'lat'                   => '---lat---',
		'lon'                   => '---lon---',
		'geom'					=> 'nullable',
		'depth'                 => '---depth---',
		'err_ot'                => '---error---',
		'err_lat'               => '---error---',
		'err_lon'               => '---error---',
		'err_depth'             => '---error---',
		'err_h'                 => '---error---',
		'err_z'                 => '---error---',
		'confidence_lev'        => 'nullable|numeric',
		'e0_az'                 => 'nullable|numeric',
		'e0_dip'                => 'nullable|numeric',
		'e0'                    => 'nullable|numeric',
		'e1_az'                 => 'nullable|numeric',
		'e1_dip'                => 'nullable|numeric',
		'e1'                    => 'nullable|numeric',
		'e2_az'                 => 'nullable|numeric',
		'e2_dip'                => 'nullable|numeric',
		'e2'                    => 'nullable|numeric',
		'fix_depth'             => 'boolean|size:1',
		'min_distance'          => '---distance---',
		'max_distance'          => '---distance---',
		'azim_gap'              => 'nullable|numeric',
		'sec_azim_gap'          => 'nullable|numeric',
		'rms'                   => 'nullable|numeric',
		'w_rms'                 => 'nullable|numeric',
		'is_centroid'           => 'integer|in:0,1',
		'nph'                   => 'nullable|integer',
		'nph_s'                 => 'nullable|integer',
		'nph_tot'               => 'nullable|integer',
		'nph_fm'                => 'nullable|integer',
		'quality'               => 'nullable|size:2|string',
		'fk_provenance'         => '---provenance_id---',
		'fk_type_hypocenter'	=> '---type_hypocenter_id---',
		'fk_event'              => '---event_id---',
		'fk_model'              => '---model_id---',
		'fk_loc_program'		=> '---loc_program_id---',
    ];   
    
    public function __construct(array $attributes = []) {
        parent::updateBaseArray();
        parent::setFillableFromBaseArray();
        parent::__construct($attributes);
    }
    
    public function setGeomAttribute($value)
    {
        \Log::debug("METHOD - ".__CLASS__.' -> '.__FUNCTION__);
		\Log::debug(" value=".$value);
		$this->attributes['geom'] = \DB::raw("ST_GeomFromWKB($value, 4326)");
    }
    
    /**
	 * @brief This method call 'DanteBaseModel.getGeomAttributeForPointFromNewQuery'. 
	 * 
	 * More info about it can be found on the 'parent' method 'IngvModel.getGeomAttributeForPointFromNewQuery'
	 *  
	 * 
	 * @param type $value Contiene il valore di 'geom' risultato della query presente nel metodo 'newQuery'
	 * @return geometry POINT()
	 */
    public function getGeomAttribute($value)
    {
        //\Log::debug("METHOD - ".__CLASS__.' -> '.__FUNCTION__);
        return parent::getGeomAttributeForPointFromNewQuery($value);
    }
    
	/**
	 * @brief This method, override the default query.
	 * 
	 * More info about it can be found on the 'parent' method 'DanteBaseModel.newQueryForHypocenter'
	 * 
	 * @param bool $excludeDeleted
	 * @return Query output, adding new fields.
	 */
    public function newQuery($excludeDeleted = true)
    {
		//\Log::debug("METHOD - ".__CLASS__.' -> '.__FUNCTION__);
		return parent::newQueryForHypocenter($excludeDeleted);
    }

    /**
     * Get the magnitudes for the hypocenter.
     */    
    public function magnitudes()
    {
        return $this->hasMany('App\Api\v1\Models\Tables\MagnitudeModel', 'fk_hypocenter', 'id');
    }
    
    /**
     * Get the provenance record associated with the hypocenter.
     */
    public function provenance()
    {
        return $this->hasOne('App\Api\v1\Models\Tables\ProvenanceModel', 'id', 'fk_provenance');
    }

    /**
     * Get the type_hypocenter record associated with the hypocenter.
     */
    public function type_hypocenter()
    {
        return $this->hasOne('App\Api\v1\Models\Tables\TypeHypocenterModel', 'id', 'fk_type_hypocenter');
    }
	
    /**
     * Get the model record associated with the hypocenter.
     */
    public function model()
    {
        return $this->hasOne('App\Api\v1\Models\Tables\ModelModel', 'id', 'fk_model');
    }
	
    /**
     * Get the loc_program record associated with the hypocenter.
     */
    public function loc_program()
    {
        return $this->hasOne('App\Api\v1\Models\Tables\LocProgramModel', 'id', 'fk_loc_program');
    }
	
    /**
     * Get the hypocenter_region_name that owns the hypocenter.
     */
    public function hypocenter_region_name()
    {
        return $this->belongsTo('App\Api\v1\Models\Tables\HypocenterRegionNameModel', 'id', 'fk_hypocenter');
    }
    
    /**
     * Get the event that owns the hypocenter.
     */
    public function event()
    {
        return $this->belongsTo('App\Api\v1\Models\Tables\EventModel', 'fk_event', 'id');
    }
    
    /**
     * Get the picks and phases that belong to hypocenter.
     */
    public function picks()
    {
        $phase__fillable     = (new PhaseModel)->getFillable();
        $phase__fillable[]   = 'id';
        return $this->belongsToMany('App\Api\v1\Models\Tables\PickModel', 'phase', 'fk_hypocenter', 'fk_pick')->withPivot(
				$phase__fillable
				)
				->as('phase')
				->withTimestamps();
    }
}

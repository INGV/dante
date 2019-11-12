<?php

namespace App\Api\v1\Models;

use App\Api\v1\Models\DanteBaseModel;

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
		'ot'                    => 'required|---data_time_with_msec---',
		'lat'                   => '---lat---',
		'lon'                   => '---lon---',
		'geom'					=> 'nullable',
		'depth'                 => '---depth---',
		'err_ot'                => 'nullable|---error---',
		'err_lat'               => 'nullable|---error---',
		'err_lon'               => 'nullable|---error---',
		'err_depth'             => 'nullable|---error---',
		'err_h'                 => 'nullable|---error---',
		'err_z'                 => 'nullable|---error---',
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
		'fix_depth'             => 'boolean',
		'min_distance'          => 'nullable|numeric',
		'max_distance'          => 'nullable|numeric',
		'azim_gap'              => 'nullable|numeric',
		'sec_azim_gap'          => 'nullable|numeric',
		'rms'                   => 'nullable|numeric',
		'w_rms'                 => 'nullable|numeric',
		'is_centroid'           => 'integer',
		'nph'                   => 'nullable|integer',
		'nph_s'                 => 'nullable|integer',
		'nph_tot'               => 'nullable|integer',
		'nph_fm'                => 'nullable|integer',
		'quality'               => 'nullable|string',
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
		\Log::debug("METHOD - ".__CLASS__.' -> '.__FUNCTION__);
		return parent::newQueryForHypocenter($excludeDeleted);
    }

    /**
     * Get the event that owns the hypocenter.
     */
    public function event()
    {
        return $this->belongsTo('App\Api\v1\Models\EventModel', 'fk_event', 'id');
    }
}

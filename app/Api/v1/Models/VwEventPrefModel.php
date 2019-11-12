<?php

namespace App\Api\v1\Models;

use App\Api\v1\Models\DanteBaseModel;

class VwEventPrefModel extends DanteBaseModel
{
    /* protected $connection = 'mysql_hdbrm_eventdb_ro'; */ /* set other DB connection */
    protected $table = 'vw_event_pref';
    
	/**
	 *
	 * Date Mutators
	 * - https://laravel.com/docs/5.6/eloquent-mutators#date-mutators
     * ATTENTION: do not insert 'hyp_ot', It is casted from 'newQuery()' function
	 */    
    protected $dates = [
        'hyp_modified',
        'hyp_inserted',    
    ];
    
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
		'id_locator'					=> 'string',
		'fk_pref_hyp'					=> 'string',
		'fk_pref_mag'					=> 'string',
		'fk_events_group'				=> 'string',
		'type_group'					=> 'string',
		'fk_type_event'					=> 'string',
		'fk_provenance'					=> 'string',
        'event_provenance_name'			=> 'string',
		'event_provenance_instance'		=> 'string',
		'event_provenance_softwarename'	=> 'string',
		'hyp_ot'						=> 'string',
		'hyp_lat'						=> 'string',
		'hyp_lon'						=> 'string',
		'hyp_depth'						=> 'string',
		'hyp_err_ot'					=> 'string',
		'hyp_err_lat'					=> 'string',
		'hyp_err_lon'					=> 'string',
		'hyp_err_depth'					=> 'string',
		'hyp_err_h'						=> 'string',
		'hyp_err_z'						=> 'string',
		'hyp_geom'						=> 'string',
		'hyp_fk_type_hypocenter'		=> 'string',
		'hyp_quality'					=> 'string',
		'hyp_fk_provenance'				=> 'string',
		'hyp_modified'					=> 'string',
		'hyp_inserted'					=> 'string',
		'hyp_provenance_name'			=> 'string',
		'hyp_provenance_instance'		=> 'string',
		'hyp_provenance_softwarename'	=> 'string',
		'mag_mag'						=> 'string',
		'mag_err'						=> 'string',
		'mag_quality'					=> 'string',
		'mag_mag_quality'				=> 'string',
		'mag_fk_hypocenter'				=> 'string',
		'mag_fk_type_magnitude'			=> 'string',
		'mag_fk_provenance'				=> 'string',
		'mag_provenance_name'			=> 'string',
		'mag_provenance_instance'		=> 'string',
		'mag_provenance_softwarename'	=> 'string',
		'type_event_name'				=> 'string',
		'type_hypocenter_value'			=> 'string',
		'type_hypocenter_name'			=> 'string',
		'type_magnitude_name'			=> 'string',
		'region'						=> 'string'
    ];   
    
    public function __construct(array $attributes = []) {
        parent::updateBaseArray();
        parent::setFillableFromBaseArray();
        parent::__construct($attributes);
    }
    
	/**
	 * @brief This method call 'IngvModel.getGeomAttributeForPointFromNewQuery'. 
	 * 
	 * More info about it can be found on the 'parent' method 'IngvModel.getGeomAttributeForPointFromNewQuery'
	 *  
	 * 
	 * @param type $value Contiene il valore di 'geom' risultato della query presente nel metodo 'newQuery'
	 * @return geometry POINT()
	 */
    public function getHypGeomAttribute($value)
    {
		\Log::debug("METHOD - ".__CLASS__.' -> '.__FUNCTION__);
        return parent::getGeomAttributeForPointFromNewQuery($value);
    }
	
	/**
	 * @brief This method, override the default query.
	 * 
	 * More info about it can be found on the 'parent' method 'IngvModel.newQueryForHypocenter'
	 * 
	 * @param bool $excludeDeleted
	 * @return Query output, adding new fields.
	 */
    public function newQuery($excludeDeleted = true)
    {
		\Log::debug("METHOD - ".__CLASS__.' -> '.__FUNCTION__);
		return parent::newQueryForHypocenter($excludeDeleted, $nameGeom = 'hyp_geom', $nameLat = 'hyp_lat', $nameLon = 'hyp_lon', $nameOt = 'hyp_ot');
    }
}

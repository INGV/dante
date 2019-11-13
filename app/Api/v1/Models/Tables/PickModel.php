<?php

namespace App\Api\v1\Models\Tables;

use App\Api\v1\Models\DanteBaseModel;

class PickModel extends DanteBaseModel
{
    /* protected $connection = 'mysql_hdbrm_eventdb_ro'; */ /* set other DB connection */
    protected $table = 'pick';
    
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
		'id_picker'         => 'integer',
		'weight'            => 'nullable|integer',
		'arrival_time'		=> 'required|---data_time_with_msec---',
		'err_arrival_time'  => 'numeric',
		'firstmotion'		=> 'nullable|string',
		'emersio'           => 'nullable|string',
		'pamp'              => 'nullable|numeric',
		'fk_provenance'		=> 'required|integer',
		'fk_scnl'           => 'required|integer'
    ];   
    
    public function __construct(array $attributes = []) {
        parent::updateBaseArray();
        parent::setFillableFromBaseArray();
        parent::__construct($attributes);
    }
    
	/**
	 * @brief This method, override the default query field (es: 'arrival_time') with the processed fields
	 * 
	 * Questo metodo esegue:
	 *  - l'override del campo 'arrival_time' con ' CONVERT( CAST(arrival_time AS DATETIME(3)), CHAR) AS arrival_time ' per estrarre i millisecondi; in PHP non esiste un "format" in grado di estrarli.
	 * 
	 * @param bool $excludeDeleted
	 * @return Query output, adding new fields.
	 */
    public function newQuery($excludeDeleted = true)
    {
		\Log::debug("METHOD - ".__CLASS__.' -> '.__FUNCTION__);
		$table = $this->getTable();
		
		$raw = " CONVERT( CAST($table.arrival_time AS DATETIME(3)), CHAR) AS arrival_time ";
        return parent::newQuery($excludeDeleted)->addSelect($table.'.*',\DB::raw($raw));
    }

    /**
     * Get the picks that belong to hypocenter.
     */
    public function hypocenters()
    {
        return $this->belongsToMany('App\Api\v1\Models\HypocenterModel', 'phase', 'fk_pick', 'fk_hypocenter')->withPivot('ep_distance');
    }

    /**
     * Get the phase for the pick.
     */
    public function phase()
    {
        return $this->hasMany('App\Api\v1\Models\PhaseModel', 'fk_pick', 'id');
    }
	
    /**
     * Get the provenance record associated with the pick.
     */
    public function provenance()
    {
        return $this->hasOne('App\Api\v1\Models\ProvenanceModel', 'id', 'fk_provenance');
    }
	
    /**
     * Get the scnl record associated with the pick.
     */
    public function scnl()
    {
        return $this->hasOne('App\Api\v1\Models\ScnlModel', 'id', 'fk_scnl');
    }
}

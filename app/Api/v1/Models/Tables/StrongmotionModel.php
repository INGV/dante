<?php

namespace App\Api\v1\Models\Tables;

use App\Api\v1\Models\DanteBaseModel;

class StrongmotionModel extends DanteBaseModel
{
    /* protected $connection = 'mysql_hdbrm_eventdb_ro'; */ /* set other DB connection */
    protected $table = 'strongmotion';
    
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
        't_dt'              => '---data_time_with_msec---',
        'pga'               => 'nullable|numeric',
        'tpga_dt'           => 'nullable|date',
        'pgv'               => 'nullable|numeric',
        'tpgv_dt'           => 'nullable|date',
        'pgd'               => 'nullable|numeric',
        'tpgd_dt'           => 'nullable|date',
		'rsa_030'			=> 'nullable|numeric',
		'rsa_100'			=> 'nullable|numeric',
		'rsa_300'			=> 'nullable|numeric',
        'fk_scnl'           => '---scnl_id---',
        'fk_event'			=> '---event_id---',
        'fk_provenance'     => '---provenance_id---',
    ];   
    
    public function __construct(array $attributes = []) {
        parent::updateBaseArray();
        parent::setFillableFromBaseArray();
        parent::__construct($attributes);
    }
    
    /**
     * Get the strongmotion_rsa for the strongmotion.
     */    
    public function strongmotion_rsas()
    {
        return $this->hasMany('App\Api\v1\Models\Tables\StrongmotionRsaModel', 'fk_strongmotion', 'id');
    }
	
    /**
     * Get the strongmotion_alt for the strongmotion.
     */    
    public function strongmotion_alts()
    {
        return $this->hasMany('App\Api\v1\Models\Tables\StrongmotionAltModel', 'fk_strongmotion', 'id');
    }

	/**
	 * @brief This method, override the default query field (es: 't_dt') with the processed fields
	 * 
	 * Questo metodo esegue:
	 *  - l'override del campo 't_dt' con ' CONVERT( CAST(t_dt AS DATETIME(3)), CHAR) AS t_dt ' per estrarre i millisecondi; in PHP non esiste un "format" in grado di estrarli.
	 * 
	 * @param bool $excludeDeleted
	 * @return Query output, adding new fields.
	 */
    public function newQuery($excludeDeleted = true)
    {
		\Log::debug("METHOD - ".__CLASS__.' -> '.__FUNCTION__);
		$table = $this->getTable();
		
		$raw  = " ";
		$raw .= " CONVERT( CAST($table.t_dt AS DATETIME(3)), CHAR) AS t_dt ";
		$raw .= ", ";
		$raw .= " CONVERT( CAST($table.tpga_dt AS DATETIME(3)), CHAR) AS tpga_dt ";
		$raw .= ", ";
		$raw .= " CONVERT( CAST($table.tpgv_dt AS DATETIME(3)), CHAR) AS tpgv_dt ";
		$raw .= ", ";
		$raw .= " CONVERT( CAST($table.tpgd_dt AS DATETIME(3)), CHAR) AS tpgd_dt ";
        return parent::newQuery($excludeDeleted)->addSelect($table.'.*',\DB::raw($raw));
    }
}

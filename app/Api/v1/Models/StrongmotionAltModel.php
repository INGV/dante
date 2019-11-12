<?php

namespace App\Api\v1\Models;

use App\Api\v1\Models\DanteBaseModel;

class StrongmotionAltModel extends DanteBaseModel
{
    /* protected $connection = 'mysql_hdbrm_eventdb_ro'; */ /* set other DB connection */
    protected $table = 'strongmotion_alt';
    
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
		'fk_strongmotion'		=> '---strongmotion_id---',
		't_alt_dt'				=> '---data_time_with_msec---',
		'altcode'				=> 'required|integer'
    ];   
    
    public function __construct(array $attributes = []) {
        parent::updateBaseArray();
        parent::setFillableFromBaseArray();
        parent::__construct($attributes);
    }
    
	/**
	 * @brief This method, override the default query field (es: 't_alt_dt') with the processed fields
	 * 
	 * Questo metodo esegue:
	 *  - l'override del campo 't_alt_dt' con ' CONVERT( CAST(t_alt_dt AS DATETIME(3)), CHAR) AS t_alt_dt ' per estrarre i millisecondi; in PHP non esiste un "format" in grado di estrarli.
	 * 
	 * @param bool $excludeDeleted
	 * @return Query output, adding new fields.
	 */
    public function newQuery($excludeDeleted = true)
    {
		\Log::debug("METHOD - ".__CLASS__.' -> '.__FUNCTION__);
		$table = $this->getTable();
		
		$raw  = " ";
		$raw .= " CONVERT( CAST($table.t_alt_dt AS DATETIME(3)), CHAR) AS t_alt_dt ";
        return parent::newQuery($excludeDeleted)->addSelect($table.'.*',\DB::raw($raw));
    }
}

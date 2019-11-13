<?php

namespace App\Api\v1\Models\Tables;

use App\Api\v1\Models\DanteBaseModel;

class TdmtModel extends DanteBaseModel
{
    /* protected $connection = 'mysql_hdbrm_eventdb_ro'; */ /* set other DB connection */
    protected $table = 'tdmt';
    
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
        'depth'             => 'nullable|---depth---',
        'mw'                => 'nullable|---magnitude---',
        'm0'                => 'nullable|numeric',
        'e0'                => 'nullable|numeric',
        'quality'           => 'nullable|string|min:1|max:2',
        'mxx'               => 'nullable|numeric',
        'mxy'               => 'nullable|numeric',
        'mxz'               => 'nullable|numeric',
        'myy'               => 'nullable|numeric',
        'myz'               => 'nullable|numeric',
        'mzz'               => 'nullable|numeric',
        'url'               => 'nullable|string',
        'data_url'          => 'nullable|string',
        'varred'            => 'nullable|numeric',
        'pdc'               => 'nullable|integer',
        'pclvd'             => 'nullable|integer',
        'piso'              => 'nullable|integer',
		'fk_focalmechanism'	=> 'required|integer|exists:focalmechanism,id',
        'fk_hypocenter'     => '---hypocenter_id---',
        'fk_hypocenter_out' => 'nullable|integer|exists:hypocenter,id',
        'fk_provenance'     => 'required|integer',
        'fk_model'          => 'required|integer',
        'loc_ot'            => 'date',
        'loc_lat'           => 'nullable|---lat---',
        'loc_lon'           => 'nullable|---lon---',
        'loc_depth'         => 'nullable|---depth---'
    ];   
    
    public function __construct(array $attributes = []) {
        parent::updateBaseArray();
        parent::setFillableFromBaseArray();
        parent::__construct($attributes);
    }
    
	/**
	 * @brief This method, override the default query field (es: 'loc_ot') with the processed fields
	 * 
	 * Questo metodo esegue:
	 *  - l'override del campo 'loc_ot' con ' CONVERT( CAST(loc_ot AS DATETIME(3)), CHAR) AS loc_ot ' per estrarre i millisecondi; in PHP non esiste un "format" in grado di estrarli.
	 * 
	 * @param bool $excludeDeleted
	 * @return Query output, adding new fields.
	 */
    public function newQuery($excludeDeleted = true)
    {
		$table = $this->getTable();
		
		$raw  = " ";
		$raw .= " CONVERT( CAST($table.loc_ot AS DATETIME(3)), CHAR) AS loc_ot ";
        return parent::newQuery($excludeDeleted)->addSelect($table.'.*',\DB::raw($raw));
    }
}

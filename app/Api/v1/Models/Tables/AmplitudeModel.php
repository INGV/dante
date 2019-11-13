<?php

namespace App\Api\v1\Models\Tables;

use App\Api\v1\Models\DanteBaseModel;

class AmplitudeModel extends DanteBaseModel
{
    /* protected $connection = 'mysql_hdbrm_eventdb_ro'; */ /* set other DB connection */
    protected $table = 'amplitude';
    
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
		'time1'             => '---data_time_with_msec---',
		'amp1'              => 'required|numeric',
		'period1'           => 'nullable|numeric',
		'time2'             => '---data_time_with_msec---',
		'amp2'              => 'required|numeric',
		'period2'           => 'nullable|numeric',
		'fk_type_amplitude' => 'required|integer|exists:type_amplitude,id',
		'fk_provenance'		=> 'required|integer|exists:provenance,id',
		'fk_scnl'			=> 'required|integer|exists:scnl,id'
    ];   
    
    public function __construct(array $attributes = []) {
        parent::updateBaseArray();
        parent::setFillableFromBaseArray();
        parent::__construct($attributes);
    }
    
	/**
	 * @brief This method, override the default query field (es: 'time1') with the processed fields
	 * 
	 * Questo metodo esegue:
	 *  - l'override del campo 'time1' con ' CONVERT( CAST(time1 AS DATETIME(3)), CHAR) AS time1' per estrarre i milliseconds; in PHP non esiste un "format" in grado di estrarli.
	 *  - l'override del campo 'time2' con ' CONVERT( CAST(time2 AS DATETIME(3)), CHAR) AS time2' per estrarre i milliseconds; in PHP non esiste un "format" in grado di estrarli.
	 * 
	 * @param bool $excludeDeleted
	 * @return Query output, adding new fields.
	 */
    public function newQuery($excludeDeleted = true)
    {
		\Log::debug("METHOD - ".__CLASS__.' -> '.__FUNCTION__);
		$table = $this->getTable();
		
		$raw = " ";
		$raw .= " CONVERT( CAST($table.time1 AS DATETIME(3)), CHAR) AS time1 ";
		$raw .= ", ";
		$raw .= " CONVERT( CAST($table.time2 AS DATETIME(3)), CHAR) AS time2 ";
        return parent::newQuery($excludeDeleted)->addSelect($table.'.*',\DB::raw($raw));
    }
	
    /**
     * Get the amplitudes that belong to magnitude.
     */
    public function magnitudes()
    {
        return $this->belongsToMany('App\Api\v1\Models\MagnitudeModel', 'st_amp_mag', 'fk_amplitude', 'fk_magnitude')->withPivot('ep_distance');
    }
    
    /**
     * Get the st_amp_mag for the phase.
     */    
    public function st_amp_mag()
    {
        return $this->hasMany('App\Api\v1\Models\StAmpMagModel', 'fk_amplitude', 'id');
    }
	
    /**
     * Get the type_magnitude record associated with the amplitude.
     */
    public function type_magnitude()
    {
        return $this->hasOne('App\Api\v1\Models\TypeMagnitudeModel', 'id', 'fk_type_magnitude');
    }
	
    /**
     * Get the type_amplitude record associated with the amplitude.
     */
    public function type_amplitude()
    {
        return $this->hasOne('App\Api\v1\Models\TypeAmplitudeModel', 'id', 'fk_type_amplitude');
    }
	
    /**
     * Get the scnl record associated with the amplitude.
     */
    public function scnl()
    {
        return $this->hasOne('App\Api\v1\Models\ScnlModel', 'id', 'fk_scnl');
    }
	
    /**
     * Get the provenance record associated with the amplitude.
     */
    public function provenance()
    {
        return $this->hasOne('App\Api\v1\Models\ProvenanceModel', 'id', 'fk_provenance');
    }
}

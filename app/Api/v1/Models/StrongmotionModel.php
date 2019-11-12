<?php

namespace App\Api\v1\Models;

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
        return $this->hasMany('App\Api\v1\Models\StrongmotionRsaModel', 'fk_strongmotion', 'id');
    }
	
    /**
     * Get the strongmotion_alt for the strongmotion.
     */    
    public function strongmotion_alts()
    {
        return $this->hasMany('App\Api\v1\Models\StrongmotionAltModel', 'fk_strongmotion', 'id');
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
	
    /**
     * Used to insert 'strongmotion' from JSON.
     */    
    public static function insertStrongmotion($strongmotion) 
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);

        /* set params to default 'null' if not set */
        $arrayFieldsToSetNull = [
            'pga',
            'tpga_dt',
            'pgv',
            'tpgv_dt',
            'pgd',
            'tpgd_dt',
            'rsa_030',
			'rsa_100',
			'rsa_300',
            ];
        foreach ($arrayFieldsToSetNull as $value) {
            $strongmotion[$value] = (isset($strongmotion[$value]) && !empty($strongmotion[$value])) ? $strongmotion[$value] : null;
        }
		
		/* Get foreign key id */
		$provenanceOutput = ProvenanceModel::danteFirstOrCreate($strongmotion);
        $scnlOutput = ScnlModel::firstOrCreate([
            'net'               => $strongmotion['scnl_net'],
            'sta'               => $strongmotion['scnl_sta'],
            'cha'               => $strongmotion['scnl_cha'],
            'loc'               => $strongmotion['scnl_loc'] ?? '--',
        ]);
		
		/* Check if '$value['period']' are standard (0.30, 1.00, 3.00) or not; if 'yes' it will be inserted in the 'strongmotion' table 
		 * otherwise a new line in the in the 'strongmotion_rsa' will be created
		 */
		$strongmotion_rsa__count = 0;
		$strongmotion_rsa__arrayToInsert = [];
		if (is_array($strongmotion['rsa'])) {
			foreach ($strongmotion['rsa'] as $value) {
				if( abs($value['period'] - 0.3) < 0.001)  {
					 $strongmotion['rsa_030'] = $value['value'];
				}else if( abs($value['period'] - 1) < 0.001 ) {
					 $strongmotion['rsa_100'] = $value['value'];
				}else if( abs($value['period'] - 3) < 0.001 ) {
					 $strongmotion['rsa_300'] = $value['value'];
				}else{
					$strongmotion_rsa__arrayToInsert[$strongmotion_rsa__count]['period'] = $value['period'];
					$strongmotion_rsa__arrayToInsert[$strongmotion_rsa__count]['value'] = $value['value'];
					$strongmotion_rsa__count++;
				}
			}
		}
		
		/* Update or Create 'strongmotion' tuple */
        $strongmotionOutput = StrongmotionModel::firstOrCreate(
			[
				'fk_event'          => $strongmotion['event_id'],
				'fk_scnl'           => $scnlOutput->id,
                'fk_provenance'     => $provenanceOutput->id
			],
            [
				't_dt'				=> $strongmotion['t_dt'],
				'pga'				=> $strongmotion['pga'],
				'tpga_dt'			=> $strongmotion['tpga_dt'],
                'pgv'				=> $strongmotion['pgv'],
                'tpgv_dt'           => $strongmotion['tpgv_dt'],
                'pgd'				=> $strongmotion['pgd'],
                'tpgd_dt'			=> $strongmotion['tpgd_dt'],
                'rsa_030'           => $strongmotion['rsa_030'],
                'rsa_100'           => $strongmotion['rsa_100'],
				'rsa_300'           => $strongmotion['rsa_300']
            ]
        );

		/* Update or create 'strongmotion_rsa' tuple */
		if ($strongmotion_rsa__count > 0) {
			foreach ($strongmotion_rsa__arrayToInsert as $value) {
				$strongmotionRsaOutput = StrongmotionRsaModel::updateOrCreate(
					[
						'fk_strongmotion'   => $strongmotionOutput->id,
						'period'	        => $value['period'],
					],
					[
						'value'				=> $value['value'],
					]
				);
			}
		}
		
		/* Update or create 'strongmotion_alt' tuple */
		if ( array_key_exists('alternate_time', $strongmotion) && $strongmotion['alternate_time'] != '1970-01-01 00:00:00.000' ) {
			$alternate_time = $strongmotion['alternate_time'];
		}else{
			$alternate_time = null;
		}
		if ( array_key_exists('alternate_code', $strongmotion) && $strongmotion['alternate_code'] != 0 ) {
			$alternate_code = $strongmotion['alternate_code'];
		}else{
			$alternate_code = null;
		}
		if (!is_null($alternate_time) && !is_null($alternate_code)) {
			$strongmotionAltOutput = StrongmotionAltModel::updateOrCreate(
				[
					'fk_strongmotion'   => $strongmotionOutput->id,
					't_alt_dt'	        => $alternate_time,
					'altcode'			=> $alternate_code,
				]
			);
		}
		
		/* Retrieve complete data */
		$strongmotionOutput = StrongmotionModel::with('strongmotion_rsas','strongmotion_alts')->find($strongmotionOutput->id);

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $strongmotionOutput;
    }
}

<?php

namespace App\Api\v1\Models;

use Illuminate\Database\Eloquent\Model;

class DanteBaseModel extends Model
{
    protected $table = null;
    
    /** 
     * The attributes that are mass assignable
     *
     * @var array
     */    
    protected $fillable = [];

    
    /**
     * The name of the 'updated at' column.
     *
     * @var string
     */
    const UPDATED_AT = 'modified';
    
    /**
     * The name of the 'created at' column.
     *
     * @var string
     */
    const CREATED_AT = 'inserted';
    
    /**
     * This method is used to build the 'protected $fillable' array (used for attributes that are mass assignable) from 
     * 'protected $baseArray' array
     * 
     * @var array
     */
    public function setFillableFromBaseArray() {        
        /* Set 'fillable' */
        if ( isset($this->baseArray) ) {
            foreach ($this->baseArray as $key => $value) {
                $newFillable[] = $key;
            }
            $this->fillable($newFillable);
        }
    }
    
    /**
     * This method is used to build the Validator roles (for 'store' route) array from 'protected $baseArray' array
     * 
     * @var array
     */
    public function getValidatorRulesForStore() {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);

        if ( isset($this->baseArray) ) {
            \Log::debug("END(1) - ".__CLASS__.' -> '.__FUNCTION__);
            return $this->baseArray;
        }
        \Log::debug("END(2) - ".__CLASS__.' -> '.__FUNCTION__);
    }
    
	/**
	 * @brief Used to get 'hypocenter.geom' in 'POINT()' format, from 'newQuery'. 
	 * 
	 * Il valore di 'geom' proveniente del metodo 'newQuery' e' qualcosa del tipo: 'POINT(12.33 42.32)'
	 * Il seguente metodo converte il valore di 'geom', proveniente dal metodo 'newQuery', da 'POINT(12.33 42.32)' a 'POINT(12.33, 42.32)'
	 *  
	 * 
	 * @param type $value Contiene il valore di 'geom' risultato della query presente nel metodo 'newQuery'
	 * @return geometry POINT()
	 */
    public function getGeomAttributeForPointFromNewQuery($value)
    {
		//\Log::debug("METHOD - ".__CLASS__.' -> '.__FUNCTION__);
		//\Log::debug(" value=".$value);
		if (empty($value)) {
			$b = null;
		} else {
			$a = explode(')', explode('POINT(', $value)[1])[0];
			//\Log::debug(" a=".$a);
			$b = str_replace(' ', ',', $a);
			//\Log::debug(" b=".$b);
		}
		$return = 'POINT('.$b.')';
		//\Log::debug(" return=".$return);
        return $return;
    }
    
	/**
	 * @brief This method, override the default query 'hypocenter' fields (es: 'geom', 'ot', 'lat' and 'lon') with the processed fields
	 * 
	 * Questo metodo esegue:
	 *  - l'override del campo 'hypocenter.geom' con 'AsText(geom)'
	 *  - l'override del campo (o lo aggiunge se non esiste) 'lat' con 'ST_Y(hypocenter.geom)'
	 *  - l'override del campo (o lo aggiunge se non esiste) 'lon' con 'ST_X(hypocenter.geom)'
	 *  - l'override del campo 'ot' con ' CONVERT( CAST(ot AS DATETIME(3)), CHAR) AS ot ' per estrarre i millisecond
	 * 
	 * @param bool $excludeDeleted
	 * @return Query output, adding new fields.
	 */
    public function newQueryForHypocenter($excludeDeleted = true, $nameGeom = 'geom', $nameLat = 'lat', $nameLon = 'lon', $nameOt = 'ot')
    {
		\Log::debug("METHOD - ".__CLASS__.' -> '.__FUNCTION__);
		$raw  = ' ';
		$raw .= ' IFNULL(AsText('.$nameGeom.'), CONCAT( "POINT(",'.$nameLon.',",",'.$nameLat.',")") ) AS '.$nameGeom.' ';
		$raw .= ', ';
		$raw .= ' IFNULL(ST_Y('.$nameGeom.'), '.$nameLat.') AS '.$nameLat.' ';
		$raw .= ', ';
		$raw .= ' IFNULL(ST_X('.$nameGeom.'), '.$nameLon.') AS '.$nameLon.' ';
		$raw .= ', ';
		$raw .= ' CONVERT( CAST('.$nameOt.' AS DATETIME(3)), CHAR) AS '.$nameOt.' ';
        return parent::newQuery($excludeDeleted)->addSelect($this->table.'.*',\DB::raw($raw));
    }
    
    /**
     * This method is used to update the value of 'protected $baseArray' that contains '---<something>---' 
     * with the value contained in 'dante.validator_default_check'
     * 
     * @var array
     */
    public function updateBaseArray() {
        if ( isset($this->baseArray) ) {
            $validator_default_check = config('dante.validator_default_check');

            $baseArrayNew = $this->baseArray;
            foreach ($baseArrayNew as $key => $value) {
                if (strpos($value, '---') !== false) {
                    preg_match('~---(.*?)---~', $value, $output);
                    
                    $valueFiltered = $output[1]; // if '$value' contains '---magnitude---', $output[1]' will contains, 'magnitude'.
                    if ( array_key_exists($valueFiltered, $validator_default_check) ) {
                        $baseArrayNew[$key] = preg_replace('/---[\s\S]+?---/', $validator_default_check[$valueFiltered], $value);
                    }
                }
            }
            $this->baseArray = $baseArrayNew;
        }
    }
    
    /**
     * This method is used to build the Validator roles (for 'update' route) array starting from 'protected $baseArray' array
     * 
     * @var array
     */
    public function getValidatorRulesForUpdate() {
        if ( isset($this->baseArray) ) {
            $arrayToReturn = $this->baseArray;

            /* Remove all 'required' */
            foreach ($arrayToReturn as &$str) {
                $str = str_replace('required|', '', $str);
                $str = str_replace('|required', '', $str);
                $str = str_replace('required' , '', $str);
            }

            return $arrayToReturn;
        }
    }
    
    public static function queryCache($query, $cacheExpireInSeconds = 120) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        
		// Set cache key
		$cacheKeyString = $query->getModel()->getConnection()->getDatabaseName().
							' -->> '.
							$query->toSql().
							' -->> '.
							implode('|',$query->getBindings());
		\Log::debug(' cacheKeyString: '.$cacheKeyString);
		$cacheKeyStringMD5 = md5($cacheKeyString);
		\Log::debug(' cacheKeyStringMD5: '.$cacheKeyStringMD5);
        
        // Closure for executing a query
        $func_execute_sql = function() use ($query, $itemPerPage) {
            \Log::debug('  Sending query (DB_NAME="'.$query->getModel()->getConnection()->getDatabaseName().'"): '.$query->toSql());
			\Log::debug('   with bindings: ',$query->getBindings());
            return $query->get();
        };
        
		//
        if ( config('dante.enableQueryCache') ) {
            \Log::debug(' Query cache enabled');
            $ret = \Cache::remember($cacheKeyStringMD5, $cacheExpireInSeconds, $func_execute_sql);
        } else {
            \Log::debug(' Query cache NOT enabled');
			if ( \Cache::has($cacheKeyStringMD5) ) {
				\Log::debug('  forget: '.$cacheKeyStringMD5);
				\Cache::forget($cacheKeyStringMD5);
			}
            $ret = $func_execute_sql();
        }

		\Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $ret;
    }
}

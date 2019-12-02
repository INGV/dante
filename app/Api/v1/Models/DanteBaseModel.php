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
     * This method is used to build the Validator roles array (for 'store' route) from 'protected $baseArray' in each Model
     *  the option 'removeUnique' is used to remove the 'unique' condition from array
     * 
     * @var array
     */
    public function getValidatorRulesForStore($arrayConf = ['removeUnique' => false]) {
        if ( isset($this->baseArray) ) {
            if($arrayConf['removeUnique']) {
                $newArray = [];
                foreach ($this->baseArray as $key => $value) { // ie: $key = 'name' and $val => 'required|string|unique:loc_program,name'
                    if (strpos($value, 'unique') !== false) {
                        // true
                        $explodedValue = explode('|', $value); // ie: 'required','string','unique:loc_program,name']
                        $string = '';
                        foreach ($explodedValue as $value2) {
                            if (strpos($value2, 'unique') !== false) {
                                // true
                                continue;
                            } else {
                                $string .= $value2.'|';
                            }
                        }
                        $string = rtrim($string,'|');
                    } else {
                        $string = $value;
                    }
                    $newArray[$key] = $string;
                }
                return $newArray;
            }
            return $this->baseArray;
        }
        return [];
        /*
        if (isset($this->baseArray)) {
            if (isset($searchKeyToReturn) && array_key_exists($searchKeyToReturn, $this->baseArray)) {
                return [
                    $searchKeyToReturn => $this->baseArray[$searchKeyToReturn]
                ];
            }
            return $this->baseArray;
        }
        */
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
        $func_execute_sql = function() use ($query) {
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
    
	/**
	 * @brief Used to request an URL that return a JSON (for example a Web Services) and cache it.
	 *
	 * Questo metodo prende in input una URL (che restituisce un JSON), effettua la richiesta, lo converte in ARRAY e fa il cache del risultato
	 * 
	 * @param type $requestUrl Url to request that returns a JSON
	 * @param type $cacheExpireInSeconds Minute to cache the output
     * @return array JSON from request converted to ARRAY
	 *
	 */	
    public static function cacheJsonRequestUrl($requestUrl, $cacheExpireInSeconds = 120) {
        \Log::debug("  START - ".__CLASS__.' -> '.__FUNCTION__);
        
		$cacheKeyString = $requestUrl;
		$cacheKeyStringMD5 = md5($cacheKeyString);
        \Log::debug(' cacheKeyString: '.$cacheKeyString);
        \Log::debug(' cacheKeyStringMD5: '.$cacheKeyStringMD5);
        
        /* Closure to get data */
        $func_execute_request_url = function() use ($requestUrl) {
            \Log::debug('    Set GuzzleHttp Client: ');
            $client = new \GuzzleHttp\Client([
                'timeout'  => 10.0,
            ]);
			\Log::debug('    Done');
			\Log::debug('    Sending request url: '.$requestUrl);
			$res = $client->request('GET', $requestUrl);
			\Log::debug('    Done');
			\Log::debug("     getStatusCode=".$res->getStatusCode());
			if ($res->getStatusCode() == 200) {
				$ret = json_decode($res->getBody(), true);
			} else {
				$ret = null;
			}
			return $ret;
        };

        /* Caching */
        if ( config('dante.enableCache') ) {
            \Log::debug('   Cache enabled (timeout='.$cacheExpireInSeconds.'sec)');
            $ret = \Cache::remember($cacheKeyStringMD5, $cacheExpireInSeconds, $func_execute_request_url);
        } else {
            \Log::debug('   Cache NOT enabled');
			if ( \Cache::has($cacheKeyStringMD5) ) {
				\Log::debug('    forget: '.$cacheKeyStringMD5);
				\Cache::forget($cacheKeyStringMD5);
			}
            $ret = $func_execute_request_url();
        }
        \Log::debug("  END - ".__CLASS__.' -> '.__FUNCTION__);
        return $ret;
    }
    
	/**
	 * Compute distance
	 *
	 * @return SQL string containing specific function for MySQL or PostgreSQL
	 */
	public static function sqlComputeDistanceDegree($sqlLon1, $sqlLat1, $sqlLon2, $sqlLat2) {
		$sql_compute_distance_function = '';
		/* TODO  define SQL string for raw computation based on the following code. It must be SQL compliant. */
		$sql_raw_compute_distance_function_inline = '';
		/*
		DECLARE l_lon1            DOUBLE DEFAULT RADIANS(in_lon1);
		DECLARE l_lat1            DOUBLE DEFAULT RADIANS(in_lat1);
		DECLARE l_lon2            DOUBLE DEFAULT RADIANS(in_lon2);
		DECLARE l_lat2            DOUBLE DEFAULT RADIANS(in_lat2);
		DECLARE delta_lat         DOUBLE DEFAULT ABS( l_lat1 - l_lat2 );
		DECLARE delta_lon         DOUBLE DEFAULT ABS( l_lon1 - l_lon2 );
		DECLARE delta             DOUBLE DEFAULT  0;
		IF (delta_lat + delta_lon) > 0.000001 THEN
			-- Great-circle distance http://en.wikipedia.org/wiki/Great-circle_distance
			SET delta = ACOS( ( SIN(l_lat1) * SIN(l_lat2) ) + ( COS(l_lat1) * COS(l_lat2) * COS(delta_lon) ) );
		END IF;
		RETURN delta;
		 */
		switch(env('DB_CONNECTION')) {
		case 'pgsql' :
			/* PostGIS is required for st_distance() */
			$sql_compute_distance_function = '(st_distance(st_makepoint('.$sqlLon1.', '.$sqlLat1.'),st_makepoint('.$sqlLon2.', '.$sqlLat2.'))';
			break;
		case 'mysql':
			/* MySQL 5.6 or greater is required */
			$sql_compute_distance_function = 'ST_Distance(POINT('.$sqlLon1.', '.$sqlLat1.') , POINT('.$sqlLon2.', '.$sqlLat2.'))';
			break;
		default:
			/* TODO */
			$sql_compute_distance_function = $sql_raw_compute_distance_function_inline;
			break;
		}
		return $sql_compute_distance_function;
	}
    
	/**
	 * Compute distance
	 *
	 * @return SQL string containing specific function for MySQL or PostgreSQL
	 */
	public static function sqlComputeDistanceKm($sqlLon1, $sqlLat1, $sqlLon2, $sqlLat2) {
		$sql_compute_distance_function = '';
		switch(env('DB_CONNECTION')) {
		case 'pgsql' :
			/* PostGIS is required for st_distance() */
			$sql_compute_distance_function = '(st_distance_sphere(st_makepoint('.$sqlLon1.', '.$sqlLat1.'),st_makepoint('.$sqlLon2.', '.$sqlLat2.'))';
			break;
		default:
			$sql_compute_distance_function = '('.self::sqlComputeDistanceDegree($sqlLon1, $sqlLat1, $sqlLon2, $sqlLat2).' * '.Config('ingv.default_degreeToKm').')';
			break;
		}
		return $sql_compute_distance_function;
	}
    
    /**
     * Used to reset multidimensional array keys
     */
    public static function resetArrayKeys($array) {
        $numberCheck = false;
        foreach ($array as $k => $val) {
            if (is_array($val)) $array[$k] = self::resetArrayKeys($val); //recurse
            if (is_numeric($k)) $numberCheck = true;
        }
        if ($numberCheck === true) {
            return array_values($array);
        } else {
            return $array;
        }
    }
}

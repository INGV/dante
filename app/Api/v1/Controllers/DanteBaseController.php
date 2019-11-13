<?php

namespace App\Api\v1\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DanteBaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message, $code = 500)
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
    
    public function paginateCache($model, $cacheExpireInSeconds = 120) 
    {
		$cacheKeyString = request()->fullUrl();
		$cacheKeyStringMD5 = md5($cacheKeyString);
        \Log::debug(' cacheKeyString: '.$cacheKeyString);
        \Log::debug(' cacheKeyStringMD5: '.$cacheKeyStringMD5);
        
        /* Closure to get data */
        $func_get_data = function() use ($model) {
            \Log::debug('  retreaving data...');
            return $model::paginate(config('dante.default_params.limit'));
        };
        
		/* Caching */
        if ( config('dante.enableCache') ) {
            \Log::debug(' Cache enabled');
            $ret = \Cache::remember($cacheKeyStringMD5, $cacheExpireInSeconds, $func_get_data);
        } else {
            \Log::debug(' Cache NOT enabled');
			if ( \Cache::has($cacheKeyStringMD5) ) {
				\Log::debug('  forget: '.$cacheKeyStringMD5);
				\Cache::forget($cacheKeyStringMD5);
			}
            $ret = $func_get_data();
        }
        return $ret;
    }
}

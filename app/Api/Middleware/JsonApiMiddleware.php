<?php

namespace App\Api\Middleware;

use Closure;

class JsonApiMiddleware
{	
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {	
		\Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);

        if ($request->isMethod('POST') || $request->isMethod('PUT')) {
            json_decode($request->getContent());
            if (json_last_error() != JSON_ERROR_NONE) {
				abort(400, 'Input data must be a valid JSON!');
            }
        }
		
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
		return $next($request);
    }
}

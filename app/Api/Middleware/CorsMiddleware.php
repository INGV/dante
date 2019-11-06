<?php

namespace App\Api\Middleware;

use Closure;

class CorsMiddleware
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
        \Log::info(' $request->getMethod() = '.$request->getMethod());
        \Log::info(' $request->fullUrl() = '.$request->fullUrl());
        \Log::info(' $request->header() = ', $request->header());
        \Log::info(' $request->all() = ', $request->all());

        // ALLOW OPTIONS METHOD
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization'
        ];

        if ($request->getMethod() == "OPTIONS") {
            \Log::debug(" A1");
            // The client-side application can set only headers allowed in Access-Control-Allow-Headers
            return response('OK', 200, $headers);
        }

        \Log::debug(" A2");
        $response = $next($request);
        \Log::debug(" http status code is:".$response->status());
        foreach ($headers as $key => $value) {
            \Log::debug(" set header: $key -> $value");
            $response->headers->set($key, $value);
        }

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $response;
    }
}

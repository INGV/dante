<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/* START - EventDB API */
Route::prefix('eventdb/_table/v1')->group(function(){
    Route::apiResources([
        'event'             => 'App\Api\v1\Controllers\EventController',
        'hypocenter'        => 'App\Api\v1\Controllers\HypocenterController',
        'magnitude'         => 'App\Api\v1\Controllers\MagnitudeController',
        'type_event'         => 'App\Api\v1\Controllers\TypeEventController',
        'amplitude'         => 'App\Api\v1\Controllers\AmplitudeController',
        // --PLACEHOLDER-- - Used from 'ingv/script.sh'; DO NOT REMOVE!!!
        ],[
            'parameters' => [
                'event' => 'id',
                ],
            'middleware' => [
                'App\Api\Middleware\JsonApiMiddleware'
            ]
        ]);
});
/* END - EventDB API */

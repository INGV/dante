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
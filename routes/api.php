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
        'hypocenter'                => 'App\Api\v1\Controllers\HypocenterController',
        'magnitude'                 => 'App\Api\v1\Controllers\MagnitudeController',
        'type_event'                => 'App\Api\v1\Controllers\TypeEventController',
        'amplitude'                 => 'App\Api\v1\Controllers\AmplitudeController',
        'event'                     => 'App\Api\v1\Controllers\EventController',
        'focalmechanism'            => 'App\Api\v1\Controllers\FocalmechanismController',
        'hypocenter_region_name'    => 'App\Api\v1\Controllers\HypocenterRegionNameController',
        'loc_program'               => 'App\Api\v1\Controllers\LocProgramController',
        'model'                     => 'App\Api\v1\Controllers\ModelController',
        'phase'                     => 'App\Api\v1\Controllers\PhaseController',
        'pick'                      => 'App\Api\v1\Controllers\PickController',
        'pick_ew_coda'              => 'App\Api\v1\Controllers\PickEwCodaController',
        'provenance'                => 'App\Api\v1\Controllers\ProvenanceController',
        'scnl'                      => 'App\Api\v1\Controllers\ScnlController',
        'st_amp_mag'                => 'App\Api\v1\Controllers\StAmpMagController',
        'st_dur_mag'                => 'App\Api\v1\Controllers\StDurMagController',
        'strongmotion_alt'          => 'App\Api\v1\Controllers\StrongmotionAltController',
        'strongmotion'              => 'App\Api\v1\Controllers\StrongmotionController',
        'tdmt'                      => 'App\Api\v1\Controllers\TdmtController',
        'type_amplitude'            => 'App\Api\v1\Controllers\TypeAmplitudeController',
        'type_hypocenter'           => 'App\Api\v1\Controllers\TypeHypocenterController',
        'type_magnitude'            => 'App\Api\v1\Controllers\TypeMagnitudeController',
        // --PLACEHOLDER-- - Used from 'ingv/script.sh'; DO NOT REMOVE!!!
        ],[
            'parameters' => [],
            'middleware' => [
                'App\Api\Middleware\JsonApiMiddleware'
            ]
        ]);
    Route::resources([
        'event_extended'        => 'App\Api\v1\Controllers\EventExtendedController',
        ],[
            'only'      => [
                'index', 'show'
            ],
            'middleware' => [
                'App\Api\Middleware\JsonApiMiddleware'
            ]
        ]);
});
/* END - EventDB API */

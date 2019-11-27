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

/* EventDB API Tables micro-services */
Route::prefix('eventdb/_table/v1')->group(function() {
    Route::apiResources([
        'hypocenter'                => 'App\Api\v1\Controllers\Tables\HypocenterController',
        'magnitude'                 => 'App\Api\v1\Controllers\Tables\MagnitudeController',
        'type_event'                => 'App\Api\v1\Controllers\Tables\TypeEventController',
        'amplitude'                 => 'App\Api\v1\Controllers\Tables\AmplitudeController',
        'event'                     => 'App\Api\v1\Controllers\Tables\EventController',
        'focalmechanism'            => 'App\Api\v1\Controllers\Tables\FocalmechanismController',
        'hypocenter_region_name'    => 'App\Api\v1\Controllers\Tables\HypocenterRegionNameController',
        'loc_program'               => 'App\Api\v1\Controllers\Tables\LocProgramController',
        'model'                     => 'App\Api\v1\Controllers\Tables\ModelController',
        'phase'                     => 'App\Api\v1\Controllers\Tables\PhaseController',
        'pick'                      => 'App\Api\v1\Controllers\Tables\PickController',
        'pick_ew_coda'              => 'App\Api\v1\Controllers\Tables\PickEwCodaController',
        'provenance'                => 'App\Api\v1\Controllers\Tables\ProvenanceController',
        'scnl'                      => 'App\Api\v1\Controllers\Tables\ScnlController',
        'st_amp_mag'                => 'App\Api\v1\Controllers\Tables\StAmpMagController',
        'st_dur_mag'                => 'App\Api\v1\Controllers\Tables\StDurMagController',
        'strongmotion'              => 'App\Api\v1\Controllers\Tables\StrongmotionController',        
        'strongmotion_alt'          => 'App\Api\v1\Controllers\Tables\StrongmotionAltController',
        'strongmotion_rsa'          => 'App\Api\v1\Controllers\Tables\StrongmotionRsaController',
        'tdmt'                      => 'App\Api\v1\Controllers\Tables\TdmtController',
        'type_amplitude'            => 'App\Api\v1\Controllers\Tables\TypeAmplitudeController',
        'type_hypocenter'           => 'App\Api\v1\Controllers\Tables\TypeHypocenterController',
        'type_magnitude'            => 'App\Api\v1\Controllers\Tables\TypeMagnitudeController',
        'vw_event_extended'         => 'App\Api\v1\Controllers\Tables\VwEventExtendedController',
        'vw_event_pref'             => 'App\Api\v1\Controllers\Tables\VwEventPrefController',
        'instance'                  => 'App\Api\v1\Controllers\Tables\InstanceController',
        // --PLACEHOLDER-- - Used from 'ingv/script.sh'; DO NOT REMOVE!!!
        ],[
            'parameters' => [],
            'middleware' => [
                'App\Api\Middleware\JsonApiMiddleware'
            ]
        ]);
    Route::resources([
        'event_extended'        => 'App\Api\v1\Controllers\Tables\EventExtendedController',
        ],[
            'only'      => [
                'index', 'show'
            ],
            'middleware' => [
                'App\Api\Middleware\JsonApiMiddleware'
            ]
        ]);
});

/* EventDB API services */
Route::prefix('eventdb/v1')->group(function() {
    Route::post('pick',         'App\Api\v1\Controllers\InsertController@processRequestToInsert')->name('insert_pick.store');
    Route::post('event',        'App\Api\v1\Controllers\InsertController@processRequestToInsert')->name('insert_event.store');
    Route::post('strongmotion', 'App\Api\v1\Controllers\InsertController@processRequestToInsert')->name('insert_strongmotion.store');
});

/* EventDB for Earthworm API services */
Route::prefix('eventdb/ew/v1')->group(function() {
    Route::post('quake2k',      'App\Api\v1\Controllers\InsertEwController@quake2k')->name('insert_ew_quake2k.store');
    Route::post('magnitude',   'App\Api\v1\Controllers\InsertEwController@magnitude')->name('insert_ew_magnitude.store');    
    Route::post('pick_scnl',    'App\Api\v1\Controllers\InsertEwController@pick_scnl')->name('insert_ew_pick_scnl.store');
    Route::post('hyp2000arc',   'App\Api\v1\Controllers\InsertEwController@hyp2000arc')->name('insert_ew_hyp2000arc.store');
});

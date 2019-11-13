<?php

namespace App\Api\v1\Tests\Feature\Tables;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class AmplitudeControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/amplitude';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'time1' => '2019-11-07 11:35:52.680',
        'amp1' => '-0.0615',
        'period1' => '-1',
        'time2' => '2019-11-07 11:35:51.920',
        'amp2' => '0.0451',
        'period2' => '-1',
        'fk_type_amplitude' => '2',
        'fk_provenance' => '1785',
        'fk_scnl' => '985'
    ];   
    protected $inputParametersForUpdate = [
		'fk_scnl' => '985'
    ];
    protected $data = [
        'id',
        'time1',
        'amp1',
        'period1',
        'time2',
        'amp2',
        'period2',
        'fk_type_amplitude',
        'fk_provenance',
        'fk_scnl',
        'modified',
        'inserted'
    ]; 
}

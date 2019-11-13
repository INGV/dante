<?php

namespace App\Api\v1\Tests\Feature\Tables;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class ScnlControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/scnl';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'net'   => 'G',
        'sta'   => 'ROCAM',
        'cha'   => 'BHZ',
        'loc'   => '00',
        'lat'   => '-19.755530',
        'lon'   => '63.370140',
        'elev'  => '52.000000'
    ];   
    protected $inputParametersForUpdate = [
		'elev'  => '48'
    ];
    protected $data = [
        'id',
        'net',
        'sta',
        'cha',
        'loc',
        'lat',
        'lon',
        'elev',
        'modified',
        'inserted'
    ]; 
}

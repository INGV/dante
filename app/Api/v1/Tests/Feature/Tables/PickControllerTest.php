<?php

namespace App\Api\v1\Tests\Feature\Tables;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class PickControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/pick';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'id_picker'         => 23456789,
        'weight'            => 2,
        'arrival_time'      => '2017-04-12 08:46:30.930000',
        'err_arrival_time'  => null,
        'firstmotion'       => 'D',
        'emersio'           => null,
        'pamp'              => '283',
        'fk_provenance'     => '13',
        'fk_scnl'           => '45'
    ];   
    protected $inputParametersForUpdate = [
		'firstmotion'       => 'U'
    ];
    protected $data = [
        'id',
        'id_picker',
        'weight',
        'arrival_time',
        'err_arrival_time',
        'firstmotion',
        'emersio',
        'pamp',
        'fk_provenance',
        'modified',
        'inserted',
        'fk_scnl'
    ];
    
    public function setUp(): void 
    {
        parent::setUp();
        
        $this->inputParameters['arrival_time'] = date("Y-m-d H:i:s");
    }
}

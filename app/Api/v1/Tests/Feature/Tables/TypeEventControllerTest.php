<?php

namespace App\Api\v1\Tests\Feature\Tables;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class TypeEventControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/type_event';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'name'          => 'earthquakeTest55',
        'short'         => null,
        'description'   => null
    ];   
    protected $inputParametersForUpdate = [
		'description'   => 'earthquakeTest56'
    ];
    protected $data = [
        'id',
        'name',
        'short',
        'description',
        'modified',
        'inserted'
    ]; 
}

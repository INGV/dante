<?php

namespace App\Api\v1\Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class InstanceControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/instance';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'name'          => 'PHPUnit',
        'description'   => 'PHPUnit test'
    ];   
    protected $inputParametersForUpdate = [
		'description'   => 'PHPUnit test update'
    ];
    protected $data = [
        'id',
        'name',
        'description',
        'modified',
        'inserted'
    ]; 
}

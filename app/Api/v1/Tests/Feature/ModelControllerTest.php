<?php

namespace App\Api\v1\Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class ModelControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/model';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'name'      => 'UNKNOWN_PHPUnit',
        'author'    => null,
        'note'      => 'When the model is unknown'
    ];   
    protected $inputParametersForUpdate = [
		'note'      => 'test'
    ];
    protected $data = [
        'id',
        'name',
        'author',
        'note',
        'modified',
        'inserted'
    ]; 
}

<?php

namespace App\Api\v1\Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class TypeMagnitudeControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/type_magnitude';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'name' => 'typePhpUnitTest',
        'priority' => '0',
        'remark' => 'remarkPhpUnitTest',
        'remark_en' => 'remark_enPhpUnitTest'
    ];   
    protected $inputParametersForUpdate = [
        'priority' => '1'
    ];
    protected $data = [
        'id',
        'name',
        'priority',
        'remark',
        'remark_en',
        'modified',
        'inserted'
    ]; 
}

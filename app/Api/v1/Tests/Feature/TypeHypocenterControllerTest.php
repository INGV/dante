<?php

namespace App\Api\v1\Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class TypeHypocenterControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/type_hypocenter';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
		'value'			=> 4,
        'name'          => 'PHPUnit name',
        'description'   => 'PHPUnit description',
        'priority'      => 1000
    ];   
    protected $inputParametersForUpdate = [
        'name'          => 'PHPUnit name2',
        'description'   => 'PHPUnit description2',
    ];
    protected $data = [
        'id',
        'value',
        'name',
        'description',
        'priority',
        'modified',
        'inserted'
    ]; 
}

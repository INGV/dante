<?php

namespace App\Api\v1\Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class LocProgramControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/loc_program';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'name' => 'IPO-EW__tdmt_invc.c_PHPUnit'
    ];   
    protected $inputParametersForUpdate = [
		'name' => 'IPO-EW__tdmt_invc.c_PHPUnit2'
    ];
    protected $data = [
        'id',
        'name',
        'modified',
        'inserted'
    ]; 
}

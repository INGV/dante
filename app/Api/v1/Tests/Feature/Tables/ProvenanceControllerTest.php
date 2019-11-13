<?php

namespace App\Api\v1\Tests\Feature\Tables;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class ProvenanceControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/provenance';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'name'			=> 'INGV',
        'instance'		=> 'PHPUnit_instance',
        'softwarename'	=> 'BULLETIN-INGV-A',
        'username'		=> 'PHPUnit_user',
        'hostname'		=> 'albus.int.ingv.it',
        'description'	=> 'PHPUnit_description2',
		'priority'		=> 0
    ];   
    protected $inputParametersForUpdate = [
		'description'	=> 'PHPUnit_descriptionUpdate'
    ];
    protected $data = [
        'id',
        'name',
        'instance',
        'softwarename',
        'username',
        'hostname',
        'description',
        'priority',
        'modified',
        'inserted'
    ]; 
}

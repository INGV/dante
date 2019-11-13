<?php

namespace App\Api\v1\Tests\Feature\Tables;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class TypeAmplitudeControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/type_amplitude';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'type'      => 'Acceleration',
        'priority'  => '0',
        'remark'    => null,
        'remark_en' => null
    ];   
    protected $inputParametersForUpdate = [
		'priority' => '2'
    ];
    protected $data = [
        'id',
        'type',
        'priority',
        'remark',
        'remark_en',
        'modified',
        'inserted'
    ];
    
    public function setUp(): void 
    {
        parent::setUp();
        
        $this->inputParameters['type'] = 'phpunit-'.$this->inputParameters['type'].'-'.date("Y-m-d\TH:i:s");
    }
}

<?php

namespace App\Api\v1\Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class StrongmotionAltControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/strongmotion_alt';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'fk_strongmotion'	=> '',
        't_alt_dt'			=> '2012-05-02 14:32:37.083',
        'altcode'			=> '5'
    ];   
    protected $inputParametersForUpdate = [
		'altcode'			=> '4'
    ];
    protected $data = [
        'id',
        'fk_strongmotion',
        't_alt_dt',
        'altcode',
        'modified',
        'inserted'
    ];
    
    public function setUp(): void 
    {
        parent::setUp();
		
        // Get a valid 'strongmotion' for test
        $responseStrongmotion = $this->get($this->uriStrongmotion.'?limit=1');
        $dataStrongmotion = json_decode($responseStrongmotion->getContent());
        $strongmotion_Id = $dataStrongmotion->data[0]->id;
        
        // Set 'strongmotion' into '$inputParameters'
        $this->inputParameters['fk_strongmotion']			= $strongmotion_Id; 
	}
}

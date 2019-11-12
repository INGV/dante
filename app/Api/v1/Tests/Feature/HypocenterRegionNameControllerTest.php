<?php

namespace App\Api\v1\Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class HypocenterRegionNameControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/hypocenter_region_name';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'fk_hypocenter' => '',
        'region' => 'PHPUnit test region'
    ];   
    protected $inputParametersForUpdate = [
		'region' => 'PHPUnit test region update'
    ];
    protected $data = [
        'id',
        'fk_hypocenter',
        'region',
        'modified',
        'inserted'
    ]; 
    
    public function setUp(): void {
        parent::setUp();
		
		// Get a valid 'hypocenter'
        $response__getHypocenter			= $this->get($this->uriHypocenter.'?limit=1');
        $data__getHypocenter				= json_decode($response__getHypocenter->getContent());

		// Insert a new hypocenter
		$response__postHypocenter	= $this->json('POST', $this->uriHypocenter, (array)$data__getHypocenter->data[0]);
		$this->assertContains($response__postHypocenter->status(), [201], $response__postHypocenter->content());
		
		// Get the 'id' of new hypocenter inserted
		$dataNewHypocenter__id = json_decode($response__postHypocenter->getContent(), true)['id'];
		
		// Set the 'id' of new hypocenter into '$inputParameters'
		$this->inputParameters['fk_hypocenter']	= $dataNewHypocenter__id;
    }
	
	public function tearDown()
	{
        // Remove hypocenter inserted for test
        $this->delete($this->uriHypocenter.'/'.$this->inputParameters['fk_hypocenter'])->assertStatus(204);
	}
}

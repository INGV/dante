<?php

namespace App\Api\v1\Tests;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DanteBaseTest extends TestCase
{
    protected $uri					= "";
    protected $uriScnl				= '/api/eventdb/_table/v1/scnl';
    protected $uriPick				= '/api/eventdb/_table/v1/pick';
    protected $uriEvent				= '/api/eventdb/_table/v1/event';
    protected $uriPhase				= '/api/eventdb/_table/v1/phase';
	protected $uriModel				= '/api/eventdb/_table/v1/model';
    protected $uriAmplitude			= '/api/eventdb/_table/v1/amplitude';
    protected $uriMagnitude			= '/api/eventdb/_table/v1/magnitude';
    protected $uriHypocenter		= '/api/eventdb/_table/v1/hypocenter';
	protected $uriProvenance		= '/api/eventdb/_table/v1/provenance';
	protected $uriLocProgram		= '/api/eventdb/_table/v1/loc_program';
	protected $uriStrongmotion		= '/api/eventdb/_table/v1/strongmotion';	
    protected $uriTypeMagnitude		= '/api/eventdb/_table/v1/type_magnitude';
	protected $uriFocalmechanism	= '/api/eventdb/_table/v1/focalmechanism';
	    
    /* do not insert 'id' (that is autoincremte) or 'modified' (that is auto-generated */
    protected $inputParameters = [];
    protected $inputParametersForUpdate = [];
    protected $data = [];
    
    public function getArrayStructure($var) {
        $ret = null;
        if(is_array($var)) {
            foreach ($var as $k=>$v) {
                if (is_array($var[$k])) {
                    if(is_null($ret)){
                        $ret = array();
                    }
                    $ret[$k] = $this->getArrayStructure($v);
                } else {
                    $ret[] = $k;
                }
            }
        } else {
            // Only if the first time is not an array.
            $ret = $var;
        }
        return $ret;
    }    

    public function test_index_status_code_should_be_200()
    {
        $response = $this->get($this->uri);
        //$response->assertStatus(200);
        $this->assertContains($response->status(), [200], $response->content());
    }

    public function test_index_json() 
    {
        //$this->markTestIncomplete('This test is incomplete');
        $response = $this->getJson($this->uri);
        $this->assertContains($response->status(), [200], $response->content());
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => $this->data
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total'
        ]);
    }
    
    public function test_store_json() 
    {
		$response = $this->json('POST', $this->uri, $this->inputParameters);
        $this->assertContains($response->status(), [201], $response->content());
        
        $data = json_decode($response->getContent(), true);
        
        $buildOutputJsonToCheck = [];
        foreach ($this->data as $value) {
            $buildOutputJsonToCheck[$value] = $data[$value];
        }
        
        $response->assertExactJson($buildOutputJsonToCheck);
        
        // Remove temp record
        $this->delete($this->uri.'/'.$data['id']);
    }

    public function test_show_json() 
    {
        // Insert temp record
		$response = $this->json('POST', $this->uri, $this->inputParameters);
        
        // Check inserted
        $this->assertContains($response->status(), [201], $response->content());
        
        // Get returned value from inserted record
        $data = json_decode($response->getContent(), true);

        // Get output request to get single record
        $response = $this->get($this->uri.'/'.$data['id']);
        
        // Get status for previous request
        $this->assertContains($response->status(), [200], $response->content());
                
		// Convert 'response' to array keys
		$responseArrayKeys = array_keys(json_decode($response->content(), true));

		// Check array keys 'response' with the expected '$data' array keys
		foreach ($responseArrayKeys as $value) {
			$this->assertContains($value, $this->data, " the GET response, contains \"".$value."\" that is not present into expected array \"\$data\".");
		}
		
		// Check expected array keys '$data' with the 'response' array keys
		foreach ($this->data as $value) {
			$this->assertContains($value, $responseArrayKeys, " the expected array \"\$data\", contains \"".$value."\" that is not present into response.");
		}
        
        // Remove temp record
        $this->delete($this->uri.'/'.$data['id']);        
    }

    public function test_update_json() 
    {   
        //$this->markTestSkipped('WIP - I need to change somethings in the "inputParameters" to perfom a valid update.');
        // Insert temp record
		$response = $this->json('POST', $this->uri, $this->inputParameters);
        
        // Check inserted
        $this->assertContains($response->status(), [201], $response->content());
        
        // Get returned value from inserted record
        $data = json_decode($response->getContent(), true);
        $data__id = $data['id'];

        // Update record
		$this->json('PUT', $this->uri.'/'.$data__id, $this->inputParametersForUpdate)->assertStatus(200);
        
        // Remove record
        $this->delete($this->uri.'/'.$data__id)->assertStatus(204);
    }
    
    public function test_destroy_json() 
    {
        // Insert temp record
		$response = $this->json('POST', $this->uri, $this->inputParameters);
        
        // Check inserted
        $this->assertContains($response->status(), [201], $response->content());
        
        // Get returned value from inserted record
        $data = json_decode($response->getContent(), true);
        $data__id = $data['id'];
                
        // Remove record
        $this->delete($this->uri.'/'.$data__id)->assertStatus(204);
    }
}
<?php

namespace App\Api\v1\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class InsertEwQuake2kControllerTest extends TestCase
{
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $input_quake2k_json = '{
        "data": {
          "ewLogo": {
            "type": "TYPE_QUAKE2K",
            "module": "MOD_BINDER_EW",
            "installation": "INST_INGV",
            "user": "PHPUnit_user",
            "hostname": "hew10_phpunit",
            "instance": "hew10_mole_phpunit"
          },
          "ewMessage": {
            "quakeId": 182491,
            "originTime": "2016-06-22 16:52:06.260000",
            "latitude": 44.813667,
            "longitude": 9.9325,
            "depth": 0.02,
            "rms": 0,
            "dmin": 0,
            "ravg": 0,
            "gap": 0,
            "nph": 10
          }
        }
      }';  

    /* Output structure expected */
    protected $output_json = '{
        "event": {
            "id": 21968613,
            "id_locator": 182492,
            "fk_pref_hyp": null,
            "fk_pref_mag": null,
            "fk_events_group": 0,
            "type_group": 0,
            "fk_type_event": 1,
            "fk_provenance": 1820,
            "modified": "2019-11-26 15:40:11",
            "inserted": "2019-11-26 15:40:11"
        }
    }';
    
    public function setInputParameters() 
    {
        /* Set a new valid 'quakeId' value */
        $input_quake2k_json__decoded = json_decode($this->input_quake2k_json, true);
        $input_quake2k_json__decoded['data']['ewMessage']['quakeId'] = rand(2000000, 2999999);
        
        return $input_quake2k_json__decoded;
    }
    
    public function setUp(): void 
    {
        parent::setUp();
        
        $this->input_quake2k = $this->setInputParameters();
    }

    public function test_store_json() 
    {        
        $response = $this->post(route('insert_ew_quake2k.store', $this->input_quake2k));
        $response->assertStatus(201);
        
        /* Get output data */
        $this->output_quake2k_decoded = json_decode($response->getContent(), true);

        /* Check JSON structure */
        $output_json__decoded   = json_decode($this->output_json, true);    
        $output_json__structure = (new DanteBaseTest)->getArrayStructure($output_json__decoded);
        $response->assertJsonStructure($output_json__structure);
    }
    
    public function tearDown(): void 
    {
        $this->delete(route('event.destroy', $this->output_quake2k_decoded['event']['id']))->assertStatus(204);
        parent::tearDown();
    }
}

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
    protected $inputParameters_json = '{
        "data": {
          "ewLogo": {
            "type": "TYPE_QUAKE2K",
            "module": "MOD_BINDER_EW",
            "installation": "INST_INGV",
            "user": "PHPUnit_user",
            "hostname": "albus.int.ingv.it",
            "instance": "PHPUnit_instance"
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
    protected $data_json = '{
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
    
    public function setUp(): void 
    {
        parent::setUp();
        
        /* Init class */
        $DanteBaseTest = new DanteBaseTest();
                
        /* Set '$inputParameters' using '$inputParameters_json' */
        $inputParameters_json__decoded = json_decode($this->inputParameters_json, true);
        $this->inputParameters = $inputParameters_json__decoded;
        
        /* Set JSON data structure into $this->data */
        $data_json__decoded = json_decode($this->data_json, true);    
        $data_json__structure = $DanteBaseTest->getArrayStructure($data_json__decoded);
        $this->data = $data_json__structure;
        
        /* Set a new valid 'quakeId' value */
        $this->inputParameters['data']['ewMessage']['quakeId'] = rand(2000000, 2999999);
    }
    
    public function test_store_json() 
    {
        /* Init class */
        $DanteBaseTest = new DanteBaseTest();
        
        
        $response = $this->post(route('insert_ew_quake2k.store', $this->inputParameters));
        $response->assertStatus(201);
        
        /* Get output data */
        $data = json_decode($response->getContent(), true);
        print_r($data);

        // Check JSON structure
        $response->assertJsonStructure($this->data);
        
        /* Remove temp record */
        $this->delete(route('event.destroy', $data['event']['id']))->assertStatus(204);
    }
}

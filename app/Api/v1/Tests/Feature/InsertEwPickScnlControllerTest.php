<?php

namespace App\Api\v1\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class InsertEwPickScnlControllerTest extends TestCase
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
            "pickId": 182491,
            "network": "G",
            "station": "ROCAM",
            "component": "BHZ",
            "location": "00",
            "firstMotion": "D",
            "pickWeight": 2,
            "timeOfPick": "2017-04-12 08:46:30.930000",
            "pAmplitude": [
              109,
              101,
              122
            ]
          }
        }
      }';

    /* Output structure expected */
    protected $data_json = '{
        "picks": [
            {
                "arrival_time": "2017-04-12 08:46:30.930",
                "fk_scnl": 173021,
                "fk_provenance": 1820,
                "id_picker": 182491,
                "firstmotion": "D",
                "modified": "2019-11-26 16:06:23",
                "inserted": "2019-11-26 16:06:23",
                "id": 468718074
            }
        ]
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
    }
    
    public function test_store_json() 
    {       
        $response = $this->post(route('insert_ew_pick_scnl.store', $this->inputParameters));
        $response->assertStatus(201);
        
        /* Get output data */
        $data = json_decode($response->getContent(), true);

        /* Check JSON structure */
        $response->assertJsonStructure($this->data);
        
        /* Remove all inserted picks */
        foreach ($data['picks'] as $pick) {
            $this->delete(route('pick.destroy', $pick['id']))->assertStatus(204);
        }
    }
}

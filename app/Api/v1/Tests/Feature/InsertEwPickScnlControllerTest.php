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
    protected $input_pick_scnl_json = '{
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
    protected $output_json = '{
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

        /* Set '$input_pick_scnl' using '$input_pick_scnl_json' */
        $input_pick_scnl_json__decoded = json_decode($this->input_pick_scnl_json, true);
        $this->input_pick_scnl = $input_pick_scnl_json__decoded;
    }
    
    public function test_store_json() 
    {       
        $response = $this->post(route('insert_ew_pick_scnl.store', $this->input_pick_scnl));
        $response->assertStatus(201);
        
        /* Get output data */
        $this->output_pick_scnl_decoded = json_decode($response->getContent(), true);

        /* Check JSON structure */
        $output_json__decoded   = json_decode($this->output_json, true);    
        $output_json__structure = (new DanteBaseTest)->getArrayStructure($output_json__decoded);
        $response->assertJsonStructure($output_json__structure);
    }
    
    public function tearDown(): void 
    {
        /* Remove all picks */
        foreach ($this->output_pick_scnl_decoded['picks'] as $pick) {
            $this->delete(route('pick.destroy', $pick['id']))->assertStatus(204);
        }
        
        parent::tearDown();
    }
}
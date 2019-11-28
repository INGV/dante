<?php

namespace App\Api\v1\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class InsertPickControllerTest extends TestCase
{
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $input_pick_json = '{
        "data": {
          "picks": [
            {
              "id_picker": "123456",
              "weight": 3,
              "arrival_time": "2017-04-12 08:47:06.990",
              "err_arrival_time": 0.034,
              "firstmotion": "U",
              "emersio": "e",
              "pamp": 99.99,
              "scnl_net": "IV",
              "scnl_sta": "ACER",
              "scnl_cha": "HHE",
              "scnl_loc": "00",
              "provenance_name": "INGV",
              "provenance_instance": "valeTest4",
              "provenance_softwarename": "PHPUnit",
              "provenance_username": "valentino",
              "provenance_hostname": "localhost",
              "provenance_description": "Auto added"
            },
            {
              "id_picker": "123457",
              "weight": 2,
              "arrival_time": "2017-04-12 08:47:07.990",
              "err_arrival_time": 0.036,
              "firstmotion": "U",
              "emersio": "e",
              "pamp": 98.99,
              "scnl_net": "IV",
              "scnl_sta": "ACER",
              "scnl_cha": "HHZ",
              "scnl_loc": "00",
              "provenance_name": "INGV",
              "provenance_instance": "valeTest4",
              "provenance_softwarename": "PHPUnit",
              "provenance_username": "valentino",
              "provenance_hostname": "localhost",
              "provenance_description": "Auto added"
            }
          ]
        }
      }';  

    /* Output structure expected */
    protected $output_json = '{
        "picks": [
            {
                "arrival_time": "2017-04-12 08:47:06.990",
                "pamp": 99.99,
                "fk_scnl": 1388618,
                "fk_provenance": 2907,
                "id_picker": "123456",
                "weight": 3,
                "err_arrival_time": 0.034,
                "firstmotion": "U",
                "emersio": "e",
                "modified": "2019-11-26 10:30:48",
                "inserted": "2019-11-26 10:30:48",
                "id": 468674628
            }
        ]
      }';
    
    public function setUp(): void 
    {
        parent::setUp();
                
        /* Set '$input_pick' using '$input_pick_json' */
        $input_pick_json__decoded = json_decode($this->input_pick_json, true);
        $this->input_pick = $input_pick_json__decoded;
    }
    
    public function test_store_json() 
    {
        /* Init class */
        $DanteBaseTest = new DanteBaseTest();
        
        $response = $this->post(route('insert_pick.store', $this->input_pick));
        $response->assertStatus(201);

        /* Get output data */
        $this->output_pick_decoded = json_decode($response->getContent(), true);

        /* Check JSON structure */
        $output_json__decoded = json_decode($this->output_json, true);    
        $output_json__structure = (new DanteBaseTest)->getArrayStructure($output_json__decoded);
        $response->assertJsonStructure($output_json__structure);
    }
    
    public function tearDown(): void 
    {
        /* Remove all picks */
        foreach ($this->output_pick_decoded['picks'] as $pick) {
            $this->delete(route('pick.destroy', $pick['id']))->assertStatus(204);
        }
        
        parent::tearDown();
    }
}

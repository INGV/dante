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
    protected $inputParameters_json = '{
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
    protected $data_json = '{
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
        
        /* Init class */
        $DanteBaseTest = new DanteBaseTest();
                
        /* Set '$inputParameters' using '$inputParameters_json' */
        $inputParameters_json__decoded = json_decode($this->inputParameters_json, true);
        $this->inputParameters = $inputParameters_json__decoded;
        
        /* set JSON data structure into $this->data */
        $data_json__decoded = json_decode($this->data_json, true);    
        $data_json__structure = $DanteBaseTest->getArrayStructure($data_json__decoded);
        $this->data = $data_json__structure;
    }
    
    public function test_store_json() 
    {
        /* Init class */
        $DanteBaseTest = new DanteBaseTest();
        
        $response = $this->post(route('insert_pick.store', $this->inputParameters));
        $response->assertStatus(201);

        /* Get output data */
        $data = json_decode($response->getContent(), true);
        //print_r($data);

        /* Check JSON structure */
        $response->assertJsonStructure($this->data);
        
        /* Remove all inserted picks */
        foreach ($data['picks'] as $value) {
            $pick_id = $value['id'];
            $this->delete(route('pick.destroy', $pick_id))->assertStatus(204);
        }
    }
}

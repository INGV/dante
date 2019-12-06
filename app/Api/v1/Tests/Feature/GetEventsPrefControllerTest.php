<?php

namespace App\Api\v1\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class GetEventsPrefControllerTest extends TestCase
{
    /* Output structure expected */
    protected $output_json = '{
        "current_page": "1",
        "first_page_url": "http://localhost:8087/api/eventdb/v1/events_pref/?page=2",
        "from": "1",
        "last_page": "967",
        "last_page_url": "http://localhost:8087/api/eventdb/v1/events_pref/?page=4",
        "next_page_url": "http://localhost:8087/api/eventdb/v1/events_pref/?page=3",
        "path": "http://localhost:8087",
        "per_page": "4000",
        "prev_page_url": "http://localhost:8087/api/eventdb/v1/events_pref/?page=1",
        "to": "4000",
        "total": "50",
        "data": [
          {
            "fk_events_group": 0,
            "id": 14932631,
            "id_locator": 182491,
            "inserted": "2017-04-18 15:04:42",
            "modified": "2017-04-18 15:04:42",
            "type_event": "earthquake",
            "hypocenter": {
              "depth": 0.02,
              "err_h": 0,
              "err_lat": 0,
              "err_lon": 0,
              "err_ot": 0,
              "err_z": 0,
              "id": 14932631,
              "inserted": "2017-04-18 15:04:42",
              "lat": 45.492599,
              "lon": 9.19289,
              "modified": "2017-04-18 15:04:42",
              "ot": "2016-06-22 16:52:06.260",
              "provenance": {
                "instance": "hew1_mole",
                "name": "INGV",
                "softwarename": "software"
              },
              "quality": "AB",
              "region": "Norcia",
              "type": "ew prelim",
              "value": "10"
            },
            "magnitude": {
              "id": 14932631,
              "mag_quality": "BB",
              "name": "ML-VAX",
              "provenance": {
                "instance": "hew1_mole",
                "name": "INGV",
                "softwarename": "software"
              },
              "quality": 0,
              "value": 3.01
            },
            "provenance": {
              "instance": "hew1_mole",
              "name": "INGV",
              "softwarename": "software"
            }
          }
        ]
      }';
    
    public function test_get_event() 
    {
        // Get output request to get single record
        $response = $this->get(route('get_events_pref.index'));
        $response->assertStatus(200);
        
        /* Check JSON structure */
        $output_json__decoded = json_decode($this->output_json, true);    
        $output_json__structure = (new DanteBaseTest)->getArrayStructure($output_json__decoded); 
        $response->assertJsonStructure($output_json__structure);        
    }
}

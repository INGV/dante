<?php

namespace App\Api\v1\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class GetEventControllerTest extends TestCase
{
    /* Output structure expected */
    protected $output_json = '{
        "data": {
          "event": {
            "provenance_description": "Auto added",
            "provenance_hostname": "hew10.ingv.it",
            "provenance_instance": "hew1_mole",
            "provenance_name": "INGV",
            "provenance_softwarename": "software",
            "provenance_username": "ew",
            "hypocenters": [
              {
                "provenance_description": "Auto added",
                "provenance_hostname": "hew10.ingv.it",
                "provenance_instance": "hew1_mole",
                "provenance_name": "INGV",
                "provenance_softwarename": "software",
                "provenance_username": "ew",
                "azim_gap": 0,
                "confidence_lev": 68.3,
                "depth": 0.02,
                "e0": 0,
                "e0_az": 0,
                "e0_dip": 0,
                "e1": 0,
                "e1_az": 0,
                "e1_dip": 0,
                "e2": 0,
                "e2_az": 0,
                "e2_dip": 0,
                "err_depth": 0,
                "err_h": 0,
                "err_lat": 0,
                "err_lon": 0,
                "err_ot": 0,
                "err_z": 0,
                "fix_depth": 0,
                "is_centroid": 0,
                "lat": 45.492599,
                "loc_program": "IPO-EW__tdmt_invc.c",
                "lon": 9.19289,
                "magnitudes": [
                  {
                    "provenance_description": "Auto added",
                    "provenance_hostname": "hew10.ingv.it",
                    "provenance_instance": "hew1_mole",
                    "provenance_name": "INGV",
                    "provenance_softwarename": "software",
                    "provenance_username": "ew",
                    "amplitudes": [
                      {
                        "provenance_description": "Auto added",
                        "provenance_hostname": "hew10.ingv.it",
                        "provenance_instance": "hew1_mole",
                        "provenance_name": "INGV",
                        "provenance_softwarename": "software",
                        "provenance_username": "ew",
                        "amp1": -0.358,
                        "amp2": 0.292,
                        "azimut": 161,
                        "ep_distance": 694,
                        "err_mag": 0,
                        "hyp_distance": 0,
                        "is_used": 0,
                        "mag": 3.04,
                        "mag_correction": 0,
                        "period1": -1,
                        "period2": -1,
                        "scnl_cha": "HHZ",
                        "scnl_loc": "00",
                        "scnl_net": "IV",
                        "scnl_sta": "ACER",
                        "time1": "2017-04-28 06:34:15.350",
                        "time2": "2017-04-28 06:34:15.850",
                        "type_amplitude": "Acceleration",
                        "type_magnitude": "ML-VAX"
                      }
                    ],
                    "azimut": 0,
                    "err": 2.01,
                    "mag": 3.01,
                    "mag_quality": "BB",
                    "min_dist": 0,
                    "ncha": 0,
                    "nsta": 0,
                    "nsta_used": 0,
                    "quality": 0,
                    "type_magnitude": "ML-VAX"
                  }
                ],
                "max_distance": 0,
                "min_distance": 0,
                "model": "prem",
                "nph": 0,
                "nph_fm": 0,
                "nph_s": 0,
                "nph_tot": 0,
                "ot": "2016-06-22 16:52:06.260",
                "phases": [
                  {
                    "provenance_description": "Auto added",
                    "provenance_hostname": "hew10.ingv.it",
                    "provenance_instance": "hew1_mole",
                    "provenance_name": "INGV",
                    "provenance_softwarename": "software",
                    "provenance_username": "ew",
                    "arr_time_is_used": 0,
                    "arrival_time": "2017-04-12 08:46:30.930",
                    "azimut": 161,
                    "emersio": null,
                    "ep_distance": 694,
                    "err_arrival_time": 0,
                    "firstmotion": "D",
                    "hyp_distance": 0,
                    "isc_code": "S",
                    "pamp": 283,
                    "polarity_is_used": 0,
                    "residual": 6.19,
                    "scnl_cha": "HHZ",
                    "scnl_loc": "00",
                    "scnl_net": "IV",
                    "scnl_sta": "ACER",
                    "std_error": 0,
                    "take_off": 94,
                    "teo_travel_time": "2017-04-12 08:47:10",
                    "weight_phase_a_priori": 4,
                    "weight_phase_localization": 0.88,
                    "weight_picker": 2
                  }
                ],
                "quality": "AB",
                "region": "Norcia",
                "rms": 0,
                "sec_azim_gap": 0,
                "type_hypocenter": 14932631,
                "w_rms": 0
              }
            ],
            "id_locator": 182491,
            "type_event": 0
          }
        }
      }';
    
    public function test_get_event() 
    {
        // Get output request to get single record
        $response = $this->get(route('get_event.index', ['eventid' => 21320301]));
        $response->assertStatus(200);
        
        /* Check JSON structure */
        $output_json__decoded = json_decode($this->output_json, true);    
        $output_json__structure = (new DanteBaseTest)->getArrayStructure($output_json__decoded); 
        $response->assertJsonStructure($output_json__structure);        
    }
}

<?php

namespace App\Api\v1\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class InsertEwHyp2000arcControllerTest extends TestCase
{
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $input_hyp2000arc_json = '{
        "data" : {
          "ewMessage" : {
            "Md" : 0,
            "mdwt" : 0,
            "phases" : [
              {
                "Md" : 0,
                "Pat" : "2019-11-27 00:04:23.410000",
                "Slabel" : null,
                "caav" : [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "Pqual" : 0,
                "datasrc" : "W",
                "Ponset" : "P",
                "Pwt" : 1.28,
                "Sfm" : null,
                "Pres" : -0.11,
                "codawt" : 4,
                "sta" : "GSCL",
                "azm" : 226,
                "Sonset" : null,
                "net" : "GU",
                "takeoff" : 155,
                "Pfm" : "D",
                "dist" : 9.4000000000000004,
                "ccntr" : [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "comp" : "HHZ",
                "Sat" : "2019-11-27 00:04:00.000000",
                "pamp" : 3280,
                "Plabel" : null,
                "loc" : "--",
                "Squal" : 0,
                "codalen" : 0,
                "Swt" : 0,
                "Sres" : 0,
                "codalenObs" : 0
              },
              {
                "Md" : 0,
                "Pat" : "2019-11-27 00:04:25.000000",
                "Slabel" : null,
                "caav" : [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "Pqual" : 0,
                "datasrc" : "W",
                "Ponset" : "P",
                "Pwt" : 1.28,
                "Sfm" : null,
                "Pres" : 0.19,
                "codawt" : 4,
                "sta" : "ZCCA",
                "azm" : 105,
                "Sonset" : null,
                "net" : "IV",
                "takeoff" : 119,
                "Pfm" : "D",
                "dist" : 25.100000000000001,
                "ccntr" : [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "comp" : "HHZ",
                "Sat" : "2019-11-27 00:04:00.000000",
                "pamp" : 932,
                "Plabel" : null,
                "loc" : "--",
                "Squal" : 0,
                "codalen" : 0,
                "Swt" : 0,
                "Sres" : 0,
                "codalenObs" : 0
              },
              {
                "Md" : 0,
                "Pat" : "2019-11-27 00:04:26.240000",
                "Slabel" : null,
                "caav" : [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "Pqual" : 0,
                "datasrc" : "W",
                "Ponset" : "P",
                "Pwt" : 1.28,
                "Sfm" : null,
                "Pres" : 0.14999999999999999,
                "codawt" : 4,
                "sta" : "VLC",
                "azm" : 220,
                "Sonset" : null,
                "net" : "MN",
                "takeoff" : 98,
                "Pfm" : "D",
                "dist" : 36,
                "ccntr" : [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "comp" : "HHZ",
                "Sat" : "2019-11-27 00:04:00.000000",
                "pamp" : 422,
                "Plabel" : null,
                "loc" : "--",
                "Squal" : 0,
                "codalen" : 0,
                "Swt" : 0,
                "Sres" : 0,
                "codalenObs" : 0
              },
              {
                "Md" : 0,
                "Pat" : "2019-11-27 00:04:26.650000",
                "Slabel" : null,
                "caav" : [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "Pqual" : 0,
                "datasrc" : "W",
                "Ponset" : "P",
                "Pwt" : 1.28,
                "Sfm" : null,
                "Pres" : -0.059999999999999998,
                "codawt" : 4,
                "sta" : "POPM",
                "azm" : 170,
                "Sonset" : null,
                "net" : "GU",
                "takeoff" : 95,
                "Pfm" : "D",
                "dist" : 41.100000000000001,
                "ccntr" : [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "comp" : "HHZ",
                "Sat" : "2019-11-27 00:04:00.000000",
                "pamp" : 447,
                "Plabel" : null,
                "loc" : "--",
                "Squal" : 0,
                "codalen" : 0,
                "Swt" : 0,
                "Sres" : 0,
                "codalenObs" : 0
              },
              {
                "Md" : 0,
                "Pat" : "2019-11-27 00:04:27.040001",
                "Slabel" : null,
                "caav" : [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "Pqual" : 0,
                "datasrc" : "W",
                "Ponset" : "P",
                "Pwt" : 1.28,
                "Sfm" : null,
                "Pres" : -0.17999999999999999,
                "codawt" : 4,
                "sta" : "CARD",
                "azm" : 200,
                "Sonset" : null,
                "net" : "GU",
                "takeoff" : 93,
                "Pfm" : "D",
                "dist" : 45.299999999999997,
                "ccntr" : [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "comp" : "HHZ",
                "Sat" : "2019-11-27 00:04:00.000000",
                "pamp" : 366,
                "Plabel" : null,
                "loc" : "--",
                "Squal" : 0,
                "codalen" : 0,
                "Swt" : 0,
                "Sres" : 0,
                "codalenObs" : 0
              },
              {
                "Md" : 0,
                "Pat" : "2019-11-27 00:04:28.120001",
                "Slabel" : null,
                "caav" : [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "Pqual" : 2,
                "datasrc" : "W",
                "Ponset" : "P",
                "Pwt" : 0.64000000000000001,
                "Sfm" : null,
                "Pres" : 0.41999999999999998,
                "codawt" : 4,
                "sta" : "GRAM",
                "azm" : 281,
                "Sonset" : null,
                "net" : "GU",
                "takeoff" : 93,
                "Pfm" : "D",
                "dist" : 49.100000000000001,
                "ccntr" : [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "comp" : "HHZ",
                "Sat" : "2019-11-27 00:04:00.000000",
                "pamp" : 84,
                "Plabel" : null,
                "loc" : "--",
                "Squal" : 0,
                "codalen" : 0,
                "Swt" : 0,
                "Sres" : 0,
                "codalenObs" : 0
              },
              {
                "Md" : 0,
                "Pat" : "2019-11-27 00:04:28.200001",
                "Slabel" : null,
                "caav" : [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "Pqual" : 2,
                "datasrc" : "W",
                "Ponset" : "P",
                "Pwt" : 0.64000000000000001,
                "Sfm" : null,
                "Pres" : -0.47999999999999998,
                "codawt" : 4,
                "sta" : "CAVE",
                "azm" : 27,
                "Sonset" : null,
                "net" : "IV",
                "takeoff" : 92,
                "Pfm" : "U",
                "dist" : 57.100000000000001,
                "ccntr" : [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "comp" : "HHZ",
                "Sat" : "2019-11-27 00:04:00.000000",
                "pamp" : 227,
                "Plabel" : null,
                "loc" : "--",
                "Squal" : 0,
                "codalen" : 0,
                "Swt" : 0,
                "Sres" : 0,
                "codalenObs" : 0
              },
              {
                "Md" : 0,
                "Pat" : "2019-11-27 00:04:29.459999",
                "Slabel" : null,
                "caav" : [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "Pqual" : 3,
                "datasrc" : "W",
                "Ponset" : "P",
                "Pwt" : 0.32000000000000001,
                "Sfm" : null,
                "Pres" : 0.34000000000000002,
                "codawt" : 4,
                "sta" : "MOCL",
                "azm" : 137,
                "Sonset" : null,
                "net" : "IV",
                "takeoff" : 91,
                "Pfm" : "D",
                "dist" : 60.600000000000001,
                "ccntr" : [
                  0,
                  0,
                  0,
                  0,
                  0,
                  0
                ],
                "comp" : "EHZ",
                "Sat" : "2019-11-27 00:04:00.000000",
                "pamp" : 17,
                "Plabel" : null,
                "loc" : "--",
                "Squal" : 0,
                "codalen" : 0,
                "Swt" : 0,
                "Sres" : 0,
                "codalenObs" : 0
              }
            ],
            "depth" : 31.030000000000001,
            "gap" : 106,
            "e0az" : 359,
            "e2" : 1.01,
            "dmin" : 9,
            "erz" : 1.9299999999999999,
            "mdtype" : "D",
            "erh" : 1.29,
            "mdmad" : 0,
            "e1az" : 144,
            "latitude" : 44.409832000000002,
            "ingvQuality" : "AB",
            "version" : "ew prelim",
            "nph" : 8,
            "originTime" : "2019-11-27 00:04:17.820000",
            "Mpref" : 0,
            "quakeId" : 2053406,
            "longitude" : 10.672667000000001,
            "e0dp" : 68,
            "nphS" : 0,
            "nphtot" : 8,
            "labelpref" : null,
            "wtpref" : 0,
            "e0" : 2.0800000000000001,
            "e1dp" : 17,
            "rms" : 0.20000000000000001,
            "nPfm" : 8,
            "reg" : null,
            "e1" : 1.3500000000000001
          },
          "ewLogo" : {
            "user" : "ew",
            "instance" : "hew10_mole_phpunit",
            "module" : "MOD_EQASSEMBLE",
            "type" : "TYPE_HYP2000ARC",
            "hostname" : "hew10_phpunit",
            "installation" : "INST_INGV"
          }
        }
      }';

    /* Output structure expected */
    protected $output_json = '{
        "event": {
            "id": 21968864,
            "id_locator": 205340,
            "fk_pref_hyp": 66642946,
            "fk_pref_mag": 68844610,
            "fk_events_group": 21968862,
            "type_group": 0,
            "fk_type_event": 1,
            "fk_provenance": 1778,
            "modified": "2019-11-27 14:08:17",
            "inserted": "2019-11-27 00:04:39",
            "hypocenters": [
                {
                    "id": 66643766,
                    "ot": "2019-11-27 00:04:17.820",
                    "lat": 44.409832,
                    "lon": 10.672667,
                    "depth": 31.03,
                    "geom": "POINT(10.672667,44.409832)",
                    "err_ot": null,
                    "err_lat": null,
                    "err_lon": null,
                    "err_depth": null,
                    "err_h": 1.29,
                    "err_z": 1.93,
                    "confidence_lev": 68.3,
                    "e0_az": 359,
                    "e0_dip": 68,
                    "e0": 2.08,
                    "e1_az": 144,
                    "e1_dip": 17,
                    "e1": 1.35,
                    "e2_az": null,
                    "e2_dip": null,
                    "e2": 1.01,
                    "fix_depth": 0,
                    "min_distance": 9,
                    "max_distance": null,
                    "azim_gap": 106,
                    "sec_azim_gap": null,
                    "rms": 0.2,
                    "w_rms": null,
                    "is_centroid": 0,
                    "nph": 8,
                    "nph_s": 0,
                    "nph_tot": 8,
                    "nph_fm": 8,
                    "quality": "AB",
                    "modified": "2019-11-27 14:08:17",
                    "inserted": "2019-11-27 14:08:17",
                    "fk_provenance": 1778,
                    "fk_type_hypocenter": 1001,
                    "fk_event": 21968864,
                    "fk_model": 145,
                    "fk_loc_program": 306,
                    "phases": [
                        {
                            "id": 1221062548,
                            "isc_code": "P",
                            "fk_hypocenter": 66643766,
                            "fk_pick": 468759982,
                            "ep_distance": 9.4,
                            "hyp_distance": null,
                            "azimut": 226,
                            "take_off": 155,
                            "polarity_is_used": null,
                            "arr_time_is_used": null,
                            "residual": -0.11,
                            "teo_travel_time": null,
                            "weight_in": null,
                            "weight_out": null,
                            "std_error": null,
                            "modified": "2019-11-27 14:08:17",
                            "inserted": "2019-11-27 14:08:17",
                            "pick": {
                                "id": 468759982,
                                "id_picker": 0,
                                "weight": null,
                                "arrival_time": "2019-11-27 00:04:23.410",
                                "err_arrival_time": null,
                                "firstmotion": "D",
                                "emersio": null,
                                "pamp": 3280,
                                "fk_provenance": 1778,
                                "modified": "2019-11-27 00:04:43",
                                "inserted": "2019-11-27 00:04:43",
                                "fk_scnl": 51521
                            }
                        },
                        {
                            "id": 1221062549,
                            "isc_code": "P",
                            "fk_hypocenter": 66643766,
                            "fk_pick": 468759984,
                            "ep_distance": 25.1,
                            "hyp_distance": null,
                            "azimut": 105,
                            "take_off": 119,
                            "polarity_is_used": null,
                            "arr_time_is_used": null,
                            "residual": 0.19,
                            "teo_travel_time": null,
                            "weight_in": null,
                            "weight_out": null,
                            "std_error": null,
                            "modified": "2019-11-27 14:08:17",
                            "inserted": "2019-11-27 14:08:17",
                            "pick": {
                                "id": 468759984,
                                "id_picker": 0,
                                "weight": null,
                                "arrival_time": "2019-11-27 00:04:25.000",
                                "err_arrival_time": null,
                                "firstmotion": "D",
                                "emersio": null,
                                "pamp": 932,
                                "fk_provenance": 1778,
                                "modified": "2019-11-27 00:04:43",
                                "inserted": "2019-11-27 00:04:43",
                                "fk_scnl": 185
                            }
                        },
                        {
                            "id": 1221062550,
                            "isc_code": "P",
                            "fk_hypocenter": 66643766,
                            "fk_pick": 468759985,
                            "ep_distance": 36,
                            "hyp_distance": null,
                            "azimut": 220,
                            "take_off": 98,
                            "polarity_is_used": null,
                            "arr_time_is_used": null,
                            "residual": 0.15,
                            "teo_travel_time": null,
                            "weight_in": null,
                            "weight_out": null,
                            "std_error": null,
                            "modified": "2019-11-27 14:08:17",
                            "inserted": "2019-11-27 14:08:17",
                            "pick": {
                                "id": 468759985,
                                "id_picker": 0,
                                "weight": null,
                                "arrival_time": "2019-11-27 00:04:26.240",
                                "err_arrival_time": null,
                                "firstmotion": "D",
                                "emersio": null,
                                "pamp": 422,
                                "fk_provenance": 1778,
                                "modified": "2019-11-27 00:04:43",
                                "inserted": "2019-11-27 00:04:43",
                                "fk_scnl": 191
                            }
                        },
                        {
                            "id": 1221062551,
                            "isc_code": "P",
                            "fk_hypocenter": 66643766,
                            "fk_pick": 468759986,
                            "ep_distance": 41.1,
                            "hyp_distance": null,
                            "azimut": 170,
                            "take_off": 95,
                            "polarity_is_used": null,
                            "arr_time_is_used": null,
                            "residual": -0.06,
                            "teo_travel_time": null,
                            "weight_in": null,
                            "weight_out": null,
                            "std_error": null,
                            "modified": "2019-11-27 14:08:17",
                            "inserted": "2019-11-27 14:08:17",
                            "pick": {
                                "id": 468759986,
                                "id_picker": 0,
                                "weight": null,
                                "arrival_time": "2019-11-27 00:04:26.650",
                                "err_arrival_time": null,
                                "firstmotion": "D",
                                "emersio": null,
                                "pamp": 447,
                                "fk_provenance": 1778,
                                "modified": "2019-11-27 00:04:43",
                                "inserted": "2019-11-27 00:04:43",
                                "fk_scnl": 759
                            }
                        },
                        {
                            "id": 1221062552,
                            "isc_code": "P",
                            "fk_hypocenter": 66643766,
                            "fk_pick": 468759987,
                            "ep_distance": 45.3,
                            "hyp_distance": null,
                            "azimut": 200,
                            "take_off": 93,
                            "polarity_is_used": null,
                            "arr_time_is_used": null,
                            "residual": -0.18,
                            "teo_travel_time": null,
                            "weight_in": null,
                            "weight_out": null,
                            "std_error": null,
                            "modified": "2019-11-27 14:08:17",
                            "inserted": "2019-11-27 14:08:17",
                            "pick": {
                                "id": 468759987,
                                "id_picker": 0,
                                "weight": null,
                                "arrival_time": "2019-11-27 00:04:27.040",
                                "err_arrival_time": null,
                                "firstmotion": "D",
                                "emersio": null,
                                "pamp": 366,
                                "fk_provenance": 1778,
                                "modified": "2019-11-27 00:04:43",
                                "inserted": "2019-11-27 00:04:43",
                                "fk_scnl": 7931
                            }
                        },
                        {
                            "id": 1221062553,
                            "isc_code": "P",
                            "fk_hypocenter": 66643766,
                            "fk_pick": 468759988,
                            "ep_distance": 49.1,
                            "hyp_distance": null,
                            "azimut": 281,
                            "take_off": 93,
                            "polarity_is_used": null,
                            "arr_time_is_used": null,
                            "residual": 0.42,
                            "teo_travel_time": null,
                            "weight_in": null,
                            "weight_out": null,
                            "std_error": null,
                            "modified": "2019-11-27 14:08:17",
                            "inserted": "2019-11-27 14:08:17",
                            "pick": {
                                "id": 468759988,
                                "id_picker": 0,
                                "weight": 2,
                                "arrival_time": "2019-11-27 00:04:28.120",
                                "err_arrival_time": null,
                                "firstmotion": "D",
                                "emersio": null,
                                "pamp": 84,
                                "fk_provenance": 1778,
                                "modified": "2019-11-27 00:04:43",
                                "inserted": "2019-11-27 00:04:43",
                                "fk_scnl": 7801
                            }
                        },
                        {
                            "id": 1221062554,
                            "isc_code": "P",
                            "fk_hypocenter": 66643766,
                            "fk_pick": 468759990,
                            "ep_distance": 57.1,
                            "hyp_distance": null,
                            "azimut": 27,
                            "take_off": 92,
                            "polarity_is_used": null,
                            "arr_time_is_used": null,
                            "residual": -0.48,
                            "teo_travel_time": null,
                            "weight_in": null,
                            "weight_out": null,
                            "std_error": null,
                            "modified": "2019-11-27 14:08:17",
                            "inserted": "2019-11-27 14:08:17",
                            "pick": {
                                "id": 468759990,
                                "id_picker": 0,
                                "weight": 2,
                                "arrival_time": "2019-11-27 00:04:28.200",
                                "err_arrival_time": null,
                                "firstmotion": "U",
                                "emersio": null,
                                "pamp": 227,
                                "fk_provenance": 1778,
                                "modified": "2019-11-27 00:04:43",
                                "inserted": "2019-11-27 00:04:43",
                                "fk_scnl": 7371
                            }
                        },
                        {
                            "id": 1221062555,
                            "isc_code": "P",
                            "fk_hypocenter": 66643766,
                            "fk_pick": 468759991,
                            "ep_distance": 60.6,
                            "hyp_distance": null,
                            "azimut": 137,
                            "take_off": 91,
                            "polarity_is_used": null,
                            "arr_time_is_used": null,
                            "residual": 0.34,
                            "teo_travel_time": null,
                            "weight_in": null,
                            "weight_out": null,
                            "std_error": null,
                            "modified": "2019-11-27 14:08:17",
                            "inserted": "2019-11-27 14:08:17",
                            "pick": {
                                "id": 468759991,
                                "id_picker": 0,
                                "weight": 3,
                                "arrival_time": "2019-11-27 00:04:29.459",
                                "err_arrival_time": null,
                                "firstmotion": "D",
                                "emersio": null,
                                "pamp": 17,
                                "fk_provenance": 1778,
                                "modified": "2019-11-27 00:04:43",
                                "inserted": "2019-11-27 00:04:43",
                                "fk_scnl": 172051
                            }
                        }
                    ]
                }
            ]
        }
    }';
    
    public function setInputParameters() 
    {
        /* Set a valid 'Pat' value */
        $input_hyp2000arc_json__decoded = json_decode($this->input_hyp2000arc_json, true);
        foreach ($input_hyp2000arc_json__decoded['data']['ewMessage']['phases'] as &$value) {
            $value['Pat'] = date("Y-m-d H:i:s").'.'.rand(100, 999);
        }
        
        return $input_hyp2000arc_json__decoded;
    }
        
    public function setUp(): void 
    {
        parent::setUp();
        
        $this->input_hyp2000arc = $this->setInputParameters();
    }

    public function test_store_hyp2000arc() 
    {
        $response = $this->post(route('insert_ew_hyp2000arc.store', $this->input_hyp2000arc));
        $response->assertStatus(201);
        
        /* Get output data */
        $this->output_hyp2000arc_decoded = json_decode($response->getContent(), true);

        /* Check JSON structure */
        $output_json__decoded = json_decode($this->output_json, true);    
        $output_json__structure = (new DanteBaseTest)->getArrayStructure($output_json__decoded);
        $response->assertJsonStructure($output_json__structure);
    }
    
    public function tearDown(): void {
        /* Remove 'hypocenters' */
        foreach ($this->output_hyp2000arc_decoded['event']['hypocenters'] as $hypocenter) {
            /* Remove 'phases' */
            foreach ($hypocenter['phases'] as $phase) {
                /* Remove 'phase' */
                $this->delete(route('phase.destroy', $phase['id']))->assertStatus(204);
                /* Remove 'pick' */
                $this->delete(route('pick.destroy', $phase['pick']['id']))->assertStatus(204);
            }            
            $this->delete(route('hypocenter.destroy', $hypocenter['id']))->assertStatus(204);
        }
        /* Remove 'event' */
        $this->delete(route('event.destroy', $this->output_hyp2000arc_decoded['event']['id']))->assertStatus(204);
        
        parent::tearDown();
    }
}

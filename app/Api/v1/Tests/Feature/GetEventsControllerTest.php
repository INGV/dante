<?php

namespace App\Api\v1\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;
use App\Api\v1\Tests\Feature\InsertEwQuake2kControllerTest;

class InsertEwStrongmotioniiControllerTest extends TestCase
{
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $input_strongmotionii_json = '{
        "data": {
            "ewLogo": {
                "type": "TYPE_STRONGMOTIONII",
                "module": "MOD_GMEW",
                "installation": "INST_INGV",
                "instance": "hew10_mole_phpunit",
                "user": "ew",
                "hostname": "hew10_phpunit"
            },
            "ewMessage": {
                "quakeId": 205341,
                "station": "SLCN",
                "component": "HHZ",
                "network": "IV",
                "location": "--",
                "qAuthor": "014101073:029111073",
                "time": "2019-11-27 00:08:09.727000",
                "alternateTime": "1970-01-01 00:00:00.000000",
                "alternateCode": 0,
                "pga": 0.028422,
                "pgaTime": "2019-11-27 00:08:18.907000",
                "pgv": 0.00022,
                "pgvTime": "2019-11-27 00:08:18.897000",
                "pgd": 0.000028,
                "pgdTime": "2019-11-27 00:08:09.727000",
                "RSA": [
                    {
                        "period": 0.3,
                        "value": 0.001781
                    },
                    {
                        "period": 1,
                        "value": 0.000204
                    },
                    {
                        "period": 3,
                        "value": 0.000252
                    }
                ]
            }
        }
    }';

    /* Output structure expected */
    protected $output_json = '{
        "strongmotions": [
            {
                "id": 1456214244,
                "t_dt": "2019-11-27 00:08:09.727",
                "pga": 0.028422,
                "tpga_dt": "2019-11-27 00:08:18.907",
                "pgv": 0.00022,
                "tpgv_dt": "2019-11-27 00:08:18.897",
                "pgd": 2.8e-5,
                "tpgd_dt": "2019-11-27 00:08:09.727",
                "rsa_030": 0.001781,
                "rsa_100": 0.000204,
                "rsa_300": 0.000252,
                "fk_event": 21968866,
                "fk_scnl": 755,
                "fk_provenance": 1788,
                "modified": "2019-11-27 00:10:09",
                "inserted": "2019-11-27 00:10:09",
                "strongmotion_rsas": [
                    "period": null
                    "value": null
                ],
                "strongmotion_alts": [
                    "t_alt_dt": null
                    "altcode": null
                ]
            }
        ]
    }';
    
    public function setUp(): void 
    {
        parent::setUp();
        
        /* Insert a 'quke2k' (event) to attach the strongmotionii */
        $input_quake2k                      = (new InsertEwQuake2kControllerTest)->setInputParameters();
        $response                           = $this->post(route('insert_ew_quake2k.store', $input_quake2k));
        $this->output_quake2k_decoded       = json_decode($response->getContent(), true);
        $quake2k_ewLogo_instance            = $input_quake2k['data']['ewLogo']['instance'];
        $quake2k_ewMessage_quakeId          = $input_quake2k['data']['ewMessage']['quakeId'];

        /* Set '$input_strongmotionii' using '$input_strongmotionii_json' */
        $input_strongmotionii_json__decoded = json_decode($this->input_strongmotionii_json, true);
        $input_strongmotionii_json__decoded['data']['ewLogo']['instance']   = $quake2k_ewLogo_instance;
        $input_strongmotionii_json__decoded['data']['ewMessage']['quakeId'] = $quake2k_ewMessage_quakeId;
        $this->input_strongmotionii = $input_strongmotionii_json__decoded;
    }
    
    public function test_store_strongmotionii() 
    {
        $response = $this->post(route('insert_ew_strongmotionii.store', $this->input_strongmotionii));
        $response->assertStatus(201);
        
        /* Get output data */
        $this->output_strongmotionii_decoded = json_decode($response->getContent(), true);
        
        /* Check JSON structure */
        $output_json__decoded   = json_decode($this->output_json, true);
        $output_json__structure = (new DanteBaseTest)->getArrayStructure($output_json__decoded);
        print_r($output_json__structure);
        $response->assertJsonStructure($output_json__structure);
    }
    
    public function tearDown(): void 
    {
        /* Remove magnitude */
        foreach ($this->output_strongmotionii_decoded['strongmotions'] as $strongmotion) {
            $this->delete(route('strongmotion.destroy', $strongmotion['id']))->assertStatus(204);
        }
        /* Remove 'event' */
        $this->delete(route('event.destroy', $this->output_quake2k_decoded['event']['id']))->assertStatus(204);
        
        parent::tearDown();
    }
}

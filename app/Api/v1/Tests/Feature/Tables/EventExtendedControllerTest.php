<?php

namespace App\Api\v1\Tests\Feature\Tables;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class EventExtendedControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/event_extended';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'id_locator' => '194259',
        'fk_pref_hyp' => '66629294',
        'fk_pref_mag' => '68834810',
        'fk_events_group' => '21959513',
        'type_group' => '0',
        'fk_type_event' => '1',
        'fk_provenance' => '1786',
        'event_provenance_name' => 'INST_INGV',
        'event_provenance_instance' => 'hew20_mole',
        'event_provenance_softwarename' => 'MOD_EQASSEMBLE',
        'hyp_ot' => '2019-11-07 14:24:31.820',
        'hyp_lat' => '39.792168',
        'hyp_lon' => '15.622167',
        'hyp_depth' => '7.64',
        'hyp_err_ot' => null,
        'hyp_err_lat' => null,
        'hyp_err_lon' => null,
        'hyp_err_depth' => null,
        'hyp_err_h' => '8.84',
        'hyp_err_z' => '9.54',
        'hyp_geom' => '0000000MK',
        'hyp_fk_type_hypocenter' => '',
        'hyp_quality' => '',
        'hyp_fk_provenance' => '',
        'hyp_modified' => '',
        'hyp_inserted' => '',
        'hyp_provenance_name' => '',
        'hyp_provenance_instance' => '',
        'hyp_provenance_softwarename' => '',
        'mag_mag' => '',
        'mag_err' => '',
        'mag_quality' => '',
        'mag_mag_quality' => '',
        'mag_fk_hypocenter' => '',
        'mag_fk_type_magnitude' => '',
        'mag_fk_provenance' => '',
        'mag_provenance_name' => '',
        'mag_provenance_instance' => '',
        'mag_provenance_softwarename' => '',
        'type_event_name' => '',
        'type_hypocenter_value' => '',
        'type_hypocenter_name' => '',
        'type_magnitude_name' => ''
    ];   
    protected $inputParametersForUpdate = [
		'type_magnitude_name' => ''
    ];
    protected $data = [
        'id',
        'id_locator',
        'fk_pref_hyp',
        'fk_pref_mag',
        'fk_events_group',
        'type_group',
        'fk_type_event',
        'fk_provenance',
        'modified',
        'inserted',
        'event_provenance_name',
        'event_provenance_instance',
        'event_provenance_softwarename',
        'hyp_ot',
        'hyp_lat',
        'hyp_lon',
        'hyp_depth',
        'hyp_err_ot',
        'hyp_err_lat',
        'hyp_err_lon',
        'hyp_err_depth',
        'hyp_err_h',
        'hyp_err_z',
        'hyp_geom',
        'hyp_fk_type_hypocenter',
        'hyp_quality',
        'hyp_fk_provenance',
        'hyp_modified',
        'hyp_inserted',
        'hyp_provenance_name',
        'hyp_provenance_instance',
        'hyp_provenance_softwarename',
        'mag_mag',
        'mag_err',
        'mag_quality',
        'mag_mag_quality',
        'mag_fk_hypocenter',
        'mag_fk_type_magnitude',
        'mag_fk_provenance',
        'mag_provenance_name',
        'mag_provenance_instance',
        'mag_provenance_softwarename',
        'type_event_name',
        'type_hypocenter_value',
        'type_hypocenter_name',
        'type_magnitude_name'
    ];
    
    public function test_store_json()
    {
        $this->markTestSkipped(
          'This test is skipped beacause this table is a materialized view.'
        );
    }

    public function test_update_json()
    {
        $this->markTestSkipped(
          'This test is skipped beacause this table is a materialized view.'
        );
    }

    public function test_destroy_json()
    {
        $this->markTestSkipped(
          'This test is skipped beacause this table is a materialized view.'
        );
    }
    
    public function test_show_json()
    {
        // get a valid id
        $response       = $this->get($this->uri);
        $data           = json_decode($response->getContent());
        $data__id       = $data->data[0]->id;

        // Get output request to get single record
        $response = $this->get($this->uri.'/'.$data__id);

        // Get status for previous request
        $this->assertContains($response->status(), [200], $response->content());

        // Check json structure
        $response->assertJsonStructure(
            $this->data
        );
    }
}

<?php

namespace App\Api\v1\Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class VwEventPrefControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/vw_event_pref';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'id_locator' => '195182',
        'fk_pref_hyp' => null,
        'fk_pref_mag' => null,
        'fk_events_group' => '0',
        'type_group' => '0',
        'fk_type_event' => '431',
        'fk_provenance' => '1782',
        'event_provenance_name' => 'INST_INGV',
        'event_provenance_instance' => 'hew20_mole',
        'event_provenance_softwarename' => 'MOD_BINDER_EW',
        'hyp_ot' => null,
        'hyp_lat' => null,
        'hyp_lon' => null,
        'hyp_depth' => null,
        'hyp_err_ot' => null,
        'hyp_err_lat' => null,
        'hyp_err_lon' => null,
        'hyp_err_depth' => null,
        'hyp_err_h' => null,
        'hyp_err_z' => null,
        'hyp_geom' => null,
        'hyp_fk_type_hypocenter' => null,
        'hyp_quality' => null,
        'hyp_fk_provenance' => null,
        'hyp_modified' => '2019-11-12 09:30:52',
        'hyp_inserted' => '2019-11-12 09:30:24',
        'hyp_provenance_name' => null,
        'hyp_provenance_instance' => null,
        'hyp_provenance_softwarename' => null,
        'mag_mag' => null,
        'mag_err' => null,
        'mag_quality' => null,
        'mag_mag_quality' => null,
        'mag_fk_hypocenter' => null,
        'mag_fk_type_magnitude' => null,
        'mag_fk_provenance' => null,
        'mag_provenance_name' => null,
        'mag_provenance_instance' => null,
        'mag_provenance_softwarename' => null,
        'type_event_name' => 'not existing',
        'type_hypocenter_value' => null,
        'type_hypocenter_name' => null,
        'type_magnitude_name' => null,
        'region' => null
    ];   
    protected $inputParametersForUpdate = [
		'region' => null
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
        'type_magnitude_name',
        'region'
    ];
    
    public function setUp(): void {
        parent::setUp();

        // get a valid event_id
        $response       = $this->get($this->uriEvent.'?limit=1');
        $data           = json_decode($response->getContent());
        $event__id		= $data->data[0]->id;

        // set uri
        $this->event_id = $event__id;
    }

    public function test_store_json()
    {
        $this->markTestSkipped(
          'This test is skipped beacause this is a View.'
        );
    }

    public function test_update_json()
    {
        $this->markTestSkipped(
          'This test is skipped beacause this is a View.'
        );
    }

    public function test_destroy_json()
    {
        $this->markTestSkipped(
          'This test is skipped beacause this is a View.'
        );
    }

    public function test_show_json()
    {
        // Get output request to get single record
        $response = $this->get($this->uri.'/'.$this->event_id);

        // Get status for previous request
        $this->assertContains($response->status(), [200], $response->content());
		
		// Convert 'response' to array keys
		$responseArrayKeys = array_keys(json_decode($response->content(), true));

		// Check array keys 'response' with the expected '$data' array keys
		foreach ($responseArrayKeys as $value) {
			$this->assertContains($value, $this->data, " the GET response, contains \"".$value."\" that is not present into expected array \"\$data\".");
		}
		
		// Check expected array keys '$data' with the 'response' array keys
		foreach ($this->data as $value) {
			$this->assertContains($value, $responseArrayKeys, " the expected array \"\$data\", contains \"".$value."\" that is not present into response.");
		}
    }
}

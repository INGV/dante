<?php

namespace App\Api\v1\Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class HypocenterControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/hypocenter';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'ot' => '2016-06-22 16:52:06.260000',
        'lat' => '44.813667',
        'lon' => '9.9325',
        'depth' => '0.02',
        'err_ot' => null,
        'err_lat' => null,
        'err_lon' => null,
        'err_depth' => null,
        'err_h' => null,
        'err_z' => null,
        'confidence_lev' => '68.3',
        'e0_az' => null,
        'e0_dip' => null,
        'e0' => null,
        'e1_az' => null,
        'e1_dip' => null,
        'e1' => null,
        'e2_az' => null,
        'e2_dip' => null,
        'e2' => null,
        'fix_depth' => 0,
        'min_distance' => null,
        'max_distance' => null,
        'azim_gap' => null,
        'sec_azim_gap' => null,
        'rms' => null,
        'w_rms' => null,
        'is_centroid' => '0',
        'nph' => null,
        'nph_s' => null,
        'nph_tot' => null,
        'nph_fm' => null,
        'quality' => null,
        'fk_provenance' => '1',
        'fk_type_hypocenter' => '0',
        'fk_event' => '14715517',
        'fk_model' => '51',
        'fk_loc_program' => '1'
    ];   
    protected $inputParametersForUpdate = [
		'depth' => '10'
    ];
    protected $data = [
        'id',
        'ot',
        'lat',
        'lon',
        'depth',
        'geom',
        'err_ot',
        'err_lat',
        'err_lon',
        'err_depth',
        'err_h',
        'err_z',
        'confidence_lev',
        'e0_az',
        'e0_dip',
        'e0',
        'e1_az',
        'e1_dip',
        'e1',
        'e2_az',
        'e2_dip',
        'e2',
        'fix_depth',
        'min_distance',
        'max_distance',
        'azim_gap',
        'sec_azim_gap',
        'rms',
        'w_rms',
        'is_centroid',
        'nph',
        'nph_s',
        'nph_tot',
        'nph_fm',
        'quality',
        'modified',
        'inserted',
        'fk_provenance',
        'fk_type_hypocenter',
        'fk_event',
        'fk_model',
        'fk_loc_program'
    ]; 
    
    public function setUp(): void 
    {
        parent::setUp();
        
		// Get a valid 'eventid' and update it into '$inputParameters'
        $response__event                                = $this->get($this->uriEvent.'?limit=1');
        $data__event                                    = json_decode($response__event->getContent());
        $event__id                                      = $data__event->data[0]->id;
		$this->inputParameters['fk_event']              = $event__id;
		
        // Get a valid 'type_hypocenter' and update it into '$inputParameters'
        $response__type_hypocenter						= $this->get($this->uri__type_hypocenter.'?limit=1');
        $data__type_hypocenter							= json_decode($response__type_hypocenter->getContent());
        $type_hypocenter__id							= $data__type_hypocenter->data[0]->id;
        $this->inputParameters['fk_type_hypocenter']	= $type_hypocenter__id;
    }
}

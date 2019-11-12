<?php

namespace App\Api\v1\Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class PhaseControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/phase';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'fk_hypocenter'         => '43759801',
        'fk_pick'               => '283362021',
        'isc_code'              => 'Pg',
        'ep_distance'           => '81.099998',
        'hyp_distance'          => null,
        'azimut'                => '146',
        'take_off'              => '91',
        'polarity_is_used'      => null,
        'arr_time_is_used'      => null,
        'residual'              => '0.81',
        'teo_travel_time'       => null,
        'weight_in'             => 3,
        'weight_out'            => 1.03,
        'std_error'             => null
    ];   
    protected $inputParametersForUpdate = [
		'azimut'                => '10'
    ];
    protected $data = [
        'id',
        'isc_code',
        'fk_hypocenter',
        'fk_pick',
        'ep_distance',
        'hyp_distance',
        'azimut',
        'take_off',
        'polarity_is_used',
        'arr_time_is_used',
        'residual',
        'teo_travel_time',
        'weight_in',
        'weight_out',
        'std_error',
        'modified',
        'inserted'
    ];
    
    public function setUp(): void {
        parent::setUp();
        
        // Get a valid 'hypocenter' for test
        $responseHypocenter = $this->get($this->uriHypocenter.'?limit=1&orderby=id-asc');
        $dataHypocenter = json_decode($responseHypocenter->getContent());
        $hypocenter_Id = $dataHypocenter->data[0]->id;
        
        // Get a valid 'pick' for test
        $responsePick = $this->get($this->uriPick.'?limit=1&orderby=id-desc');
        $dataPick = json_decode($responsePick->getContent());
        $pick_Id = $dataPick->data[0]->id;
        
        // Set 'hypocenter' and 'pick' into '$inputParameters'
        $this->inputParameters['fk_hypocenter'] = $hypocenter_Id; 
        $this->inputParameters['fk_pick'] = $pick_Id;
    }
}

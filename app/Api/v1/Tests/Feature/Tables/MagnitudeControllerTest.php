<?php

namespace App\Api\v1\Tests\Feature\Tables;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class MagnitudeControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/magnitude';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'mag' => '3.01',
        'err' => '2.01',
        'quality' => null,
        'min_dist' => null,
        'azimut' => null,
        'nsta' => null,
        'ncha' => null,
        'nsta_used' => null,
        'mag_quality' => null,
        'fk_hypocenter' => '34609',
        'fk_type_magnitude' => '1',
        'fk_provenance' => '1'
    ];   
    protected $inputParametersForUpdate = [
		'nsta' => '5'
    ];
    protected $data = [
        'id',
        'mag',
        'err',
        'quality',
        'min_dist',
        'azimut',
        'nsta',
        'ncha',
        'nsta_used',
        'mag_quality',
        'modified',
        'inserted',
        'fk_hypocenter',
        'fk_type_magnitude',
        'fk_provenance'
    ];
    
    public function setUp(): void 
    {
        parent::setUp();
        
		// Get a valid 'hypocenter' and update it into '$inputParameters'
        $response = $this->get($this->uriHypocenter.'?limit=1');
        $data = json_decode($response->getContent());
        $hypocenter_id = $data->data[0]->id;

        $this->inputParameters['fk_hypocenter'] = $hypocenter_id;
    }
}

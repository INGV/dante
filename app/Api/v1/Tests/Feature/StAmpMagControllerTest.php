<?php

namespace App\Api\v1\Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class StAmpMagControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/st_amp_mag';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'fk_magnitude'      => '58305789',
        'fk_amplitude'      => '374773141',
        'ep_distance'       => '84.1',
        'hyp_distance'      => null,
        'azimut'            => null,
        'mag'               => '1.55',
        'err_mag'           => null,
        'mag_correction'    => null,
        'is_used'           => null,
        'fk_type_magnitude' => '5'
    ];   
    protected $inputParametersForUpdate = [
		'fk_type_magnitude' => '5'
    ];
    protected $data = [
        'id',
        'fk_magnitude',
        'fk_amplitude',
        'ep_distance',
        'hyp_distance',
        'azimut',
        'mag',
        'err_mag',
        'mag_correction',
        'is_used',
        'fk_type_magnitude',
        'modified',
        'inserted'
    ]; 
    
    public function setUp(): void 
    {
        parent::setUp();
        
        // Get a valid 'magnitude' for test
        $responseMagnitude = $this->get($this->uriMagnitude.'?limit=1');
        $dataMagnitude = json_decode($responseMagnitude->getContent());
        $magnitude_Id = $dataMagnitude->data[0]->id;
        
        // Get a valid 'amplitude' for test
        $responseAmplitude = $this->get($this->uriAmplitude.'?limit=1');
        $dataAmplitude = json_decode($responseAmplitude->getContent());
        $amplitude_Id = $dataAmplitude->data[0]->id;
		
        // Get a valid 'type_magnitude' for test
        $responseTypeMagnitude = $this->get($this->uriTypeMagnitude.'?limit=1');
        $dataTypeMagnitude = json_decode($responseTypeMagnitude->getContent());
        $type_magnitude_Id = $dataTypeMagnitude->data[0]->id;
        
        // Set 'magnitude' and 'amplitude' into '$inputParameters'
        $this->inputParameters['fk_magnitude']		= $magnitude_Id; 
        $this->inputParameters['fk_amplitude']		= $amplitude_Id;
		$this->inputParameters['fk_type_magnitude'] = $type_magnitude_Id;
	}
}

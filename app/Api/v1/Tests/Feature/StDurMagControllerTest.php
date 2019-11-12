<?php

namespace App\Api\v1\Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class StDurMagControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/st_dur_mag';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'fk_magnitude'      => '',
        'fk_scnl'           => '',
        'ep_distance'       => '',
        'hyp_distance'      => '',
        'azimut'            => '',
        'dur'               => '',
        'mag'               => '1.5',
        'err_mag'           => '',
        'fk_type_magnitude' => '',
        'is_used'           => ''
    ];   
    protected $inputParametersForUpdate = [
		'mag'               => '2'
    ];
    protected $data = [
        'id',
        'fk_magnitude',
        'fk_scnl',
        'ep_distance',
        'hyp_distance',
        'azimut',
        'dur',
        'mag',
        'err_mag',
        'fk_type_magnitude',
        'is_used',
        'modified',
        'inserted'
    ];
    
    public function setUp(): void {
        parent::setUp();
        
        // Get a valid 'magnitude' for test
        $responseMagnitude = $this->get($this->uriMagnitude.'?limit=1');
        $dataMagnitude = json_decode($responseMagnitude->getContent());
        $magnitude_Id = $dataMagnitude->data[0]->id;
        
        // Get a valid 'scnl' for test
        $responseScnl = $this->get($this->uriScnl.'?limit=1');
        $dataScnl = json_decode($responseScnl->getContent());
        $scnl_Id = $dataScnl->data[0]->id;
        
        // Get a valid 'type_magnitude' for test
        $responseTypeMagnitude	= $this->get($this->uriTypeMagnitude.'?limit=1');
        $dataTypeMagnitude		= json_decode($responseTypeMagnitude->getContent());
        $typeMagnitude_Id		= $dataTypeMagnitude->data[0]->id;        
        
        // Set 'magnitude', 'type_magnitude' and 'scnl' into '$inputParameters'
        $this->inputParameters['fk_scnl'] = $scnl_Id;
        $this->inputParameters['fk_type_magnitude'] = $typeMagnitude_Id;
        $this->inputParameters['fk_magnitude'] = $magnitude_Id;
    }
}

<?php

namespace App\Api\v1\Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class StrongmotionControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/strongmotion';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        't_dt'          => '2018-05-25 04:58:33.067123',
        'pga'           => '0.003098',
        'tpga_dt'       => '2018-05-25 04:58:33.067456',
        'pgv'           => '0.000083',
        'tpgv_dt'       => '2018-05-25 04:58:48.017789',
        'pgd'           => '0.000056',
        'tpgd_dt'       => '2018-05-25 04:58:43.647',
        'rsa_030'       => '0.000714',
        'rsa_100'       => '0.000202',
        'rsa_300'       => '0.000373',
        'fk_scnl'       => '283',
        'fk_event'      => '57201311',
        'fk_provenance' => '141'
    ];   
    protected $inputParametersForUpdate = [
		'fk_provenance' => '1787'
    ];
    protected $data = [
        'id',
        't_dt',
        'pga',
        'tpga_dt',
        'pgv',
        'tpgv_dt',
        'pgd',
        'tpgd_dt',
        'rsa_030',
        'rsa_100',
        'rsa_300',
        'fk_event',
        'fk_scnl',
        'fk_provenance',
        'modified',
        'inserted'
    ];
    
    public function setUp(): void {
        parent::setUp();
		
        // Get a valid 'scnl' for test
        $responseScnl = $this->get($this->uriScnl.'?limit=1');
        $dataScnl = json_decode($responseScnl->getContent());
        $scnl_Id = $dataScnl->data[0]->id;
        
        // Get a valid 'provenance' for test
        $responseProvenance = $this->get($this->uriProvenance.'?limit=1');
        $dataProvenance = json_decode($responseProvenance->getContent());
        $provenance_Id = $dataProvenance->data[0]->id;
		
        // Get a valid 'event' for test
        $responseEvent = $this->get($this->uriEvent.'?limit=1');
        $dataEvent = json_decode($responseEvent->getContent());
        $event_Id = $dataEvent->data[0]->id;
        
        // Set 'scnl', 'provenance' and 'event' into '$inputParameters'
        $this->inputParameters['fk_scnl']			= $scnl_Id; 
        $this->inputParameters['fk_provenance']		= $provenance_Id;
		$this->inputParameters['fk_event']			= $event_Id;
	}
}

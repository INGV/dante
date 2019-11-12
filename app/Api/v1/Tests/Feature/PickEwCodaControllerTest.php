<?php

namespace App\Api\v1\Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class PickEwCodaControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/pick_ew_coda';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'fk_pick'   => '289164871',
        'ccntr_0'   => '1',
        'ccntr_1'   => '2',
        'ccntr_2'   => '3',
        'ccntr_3'   => '4',
        'ccntr_4'   => '5',
        'ccntr_5'   => '6',
        'caav_0'    => '7',
        'caav_1'    => '8',
        'caav_2'    => '9',
        'caav_3'    => '10',
        'caav_4'    => '11',
        'caav_5'    => '12'
    ];   
    protected $inputParametersForUpdate = [
		'caav_0'    => '10'
    ];
    protected $data = [
        'id',
        'fk_pick',
        'ccntr_0',
        'ccntr_1',
        'ccntr_2',
        'ccntr_3',
        'ccntr_4',
        'ccntr_5',
        'caav_0',
        'caav_1',
        'caav_2',
        'caav_3',
        'caav_4',
        'caav_5',
        'modified',
        'inserted'
    ];
    
    public function setUp(): void 
    {
        //$this->markTestSkipped(
        //  ' waiting the table \'pick_ew_code\' will be changed with \'fk_pick\'.'
        //);
        
        parent::setUp();
        
        $response = $this->get($this->uriPick.'?limit=1');
        $data = json_decode($response->getContent());
        $pick_id = $data->data[0]->id;
        
        $this->inputParameters['fk_pick'] = $pick_id;
    }
}

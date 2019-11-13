<?php

namespace App\Api\v1\Tests\Feature\Tables;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class TdmtControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/tdmt';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'depth'                 => '',
        'mw'                    => '',
        'm0'                    => '',
        'e0'                    => '',
        'quality'               => '',
        'mxx'                   => '',
        'mxy'                   => '',
        'mxz'                   => '',
        'myy'                   => '',
        'myz'                   => '',
        'mzz'                   => '',
        'url'                   => '',
        'data_url'              => '',
        'varred'                => '',
        'pdc'                   => '',
        'pclvd'                 => '',
        'piso'                  => '',
        'fk_focalmechanism'     => '',
        'fk_hypocenter'         => '',
        'fk_hypocenter_out'     => '',
        'fk_provenance'         => '',
        'fk_model'              => '',
        'loc_ot'                => '',
        'loc_lat'               => '',
        'loc_lon'               => '',
        'loc_depth'             => ''
    ];   
    protected $inputParametersForUpdate = [
		'loc_depth'             => ''
    ];
    protected $data = [
        'id',
        'depth',
        'mw',
        'm0',
        'e0',
        'quality',
        'mxx',
        'mxy',
        'mxz',
        'myy',
        'myz',
        'mzz',
        'url',
        'data_url',
        'varred',
        'pdc',
        'pclvd',
        'piso',
        'fk_focalmechanism',
        'fk_hypocenter',
        'fk_hypocenter_out',
        'fk_provenance',
        'fk_model',
        'loc_ot',
        'loc_lat',
        'loc_lon',
        'loc_depth',
        'modified',
        'inserted'
    ];
    
    public function setUp(): void 
    {
        parent::setUp();
        
		/* Get a valid hypocenter */
        $responseHypocenter = $this->get($this->uriHypocenter.'?limit=1');
        $dataHypocenter = json_decode($responseHypocenter->getContent());
        $hypocenter_id = $dataHypocenter->data[0]->id;
        $this->inputParameters['fk_hypocenter'] = $hypocenter_id;
		
		/* Get a valid focalmechanism */
        $responseFocalmechanism = $this->get($this->uriFocalmechanism.'?limit=1');
        $dataFocalmechanism = json_decode($responseFocalmechanism->getContent());
        $focalmechanism_id = $dataFocalmechanism->data[0]->id;
        $this->inputParameters['fk_focalmechanism'] = $focalmechanism_id;
    }
}

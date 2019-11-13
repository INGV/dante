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
        'depth'                 => '3',
        'mw'                    => '3.37',
        'm0'                    => '1.4249',
        'e0'                    => '21',
        'quality'               => 'Ca',
        'mxx'                   => '13.382',
        'mxy'                   => '4.201',
        'mxz'                   => '-2.911',
        'myy'                   => '-0.079',
        'myz'                   => '0.059',
        'mzz'                   => '-13.303',
        'url'                   => '56441691_15_tdmt_auto_solution.pdf',
        'data_url'              => 'http://webservices.ingv.it/webservices/ingv_ws_data/data/matteo.quintiliani_at_ingv.it/2018/05/03/190202_2407216/2407216_data.tgz',
        'varred'                => '24.33',
        'pdc'                   => '84',
        'pclvd'                 => '16',
        'piso'                  => '0',
        'fk_focalmechanism'     => '6970006',
        'fk_hypocenter'         => '56441691',
        'fk_hypocenter_out'     => null,
        'fk_provenance'         => '71',
        'fk_model'              => '21',
        'loc_ot'                => '2018-05-03 18:46:05.670',
        'loc_lat'               => '44.0582',
        'loc_lon'               => '11.7222',
        'loc_depth'             => '6.4'
    ];   
    protected $inputParametersForUpdate = [
		'loc_depth'             => '7.2'
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
        $responseHypocenter = $this->get($this->uriHypocenter);
        $dataHypocenter = json_decode($responseHypocenter->getContent());
        $hypocenter_id = $dataHypocenter->data[0]->id;
        $this->inputParameters['fk_hypocenter'] = $hypocenter_id;
		
		/* Get a valid focalmechanism */
        $responseFocalmechanism = $this->get($this->uriFocalmechanism);
        $dataFocalmechanism = json_decode($responseFocalmechanism->getContent());
        $focalmechanism_id = $dataFocalmechanism->data[0]->id;
        $this->inputParameters['fk_focalmechanism'] = $focalmechanism_id;
    }
}
<?php

namespace App\Api\v1\Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class EventControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/event';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'id_locator'        => 0,
        'fk_pref_hyp'       => null,
        'fk_pref_mag'       => null,        
        'fk_events_group'   => 0,
		'type_group'		=> 0,
        'fk_type_event'     => 1,
        'fk_provenance'     => '1'
    ];   
    protected $inputParametersForUpdate = [
		'fk_events_group' => '10'
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
        'inserted'
    ]; 
}

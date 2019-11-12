<?php

namespace App\Api\v1\Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Api\v1\Tests\DanteBaseTest;

class FocalmechanismControllerTest extends DanteBaseTest
{
    protected $uri = '/api/eventdb/_table/v1/focalmechanism';
    /* 
     * Do not insert all fields that are auto generated; for example:
     *  - 'id' (that is autoincremente) 
     *  - 'inserted' (that is auto-generated)
     *  - 'modified' (that is auto-generated) 
     */
    protected $inputParameters = [
        'strike1'			=> '73',
        'dip1'				=> '58',
        'rake1'				=> '40',
        'strike2'			=> '319',
        'dip2'				=> '57',
        'rake2'				=> '141',
        'azim_gap'			=> null,
        'nsta_polarity'		=> null,
        'misfit'			=> null,
        'stdr'				=> null,
        'rmsAngDiffAccPref' => null,
        'fracAcc30degPref'	=> null,
        'quality'			=> 'Aa',
        'url'				=> null,
        'fk_hypocenter'		=> '999999',
        'fk_provenance'		=> '999999',
        'fk_model'			=> '999999',
        'fk_loc_program'	=> '999999'
    ];   
    protected $inputParametersForUpdate = [
		'strike1' => '37'
    ];
    protected $data = [
        'id',
        'strike1',
        'dip1',
        'rake1',
        'strike2',
        'dip2',
        'rake2',
        'azim_gap',
        'nsta_polarity',
        'misfit',
        'stdr',
        'rmsAngDiffAccPref',
        'fracAcc30degPref',
        'quality',
        'url',
        'fk_hypocenter',
        'fk_provenance',
        'fk_model',
        'fk_loc_program',
        'modified',
        'inserted'
    ];
    
    public function setUp(): void {
        parent::setUp();

		// Get a valid 'hypocenter' and update it into '$inputParameters'
        $response = $this->get($this->uriHypocenter.'?limit=1');
        $data = json_decode($response->getContent());
        $hypocenter__id = $data->data[0]->id;        
        $this->inputParameters['fk_hypocenter'] = $hypocenter__id;
		
		// Get a valid 'loc_program' and update it into '$inputParameters'
        $response = $this->get($this->uriLocProgram.'?limit=1');
        $data = json_decode($response->getContent());
        $loc_program__id = $data->data[0]->id;        
        $this->inputParameters['fk_loc_program'] = $loc_program__id;
		
		// Get a valid 'provenance' and update it into '$inputParameters'
        $response = $this->get($this->uriProvenance.'?limit=1');
        $data = json_decode($response->getContent());
        $provenance__id = $data->data[0]->id;        
        $this->inputParameters['fk_provenance'] = $provenance__id;
		
		// Get a valid 'model' and update it into '$inputParameters'
        $response = $this->get($this->uriModel.'?limit=1');
        $data = json_decode($response->getContent());
        $model__id = $data->data[0]->id;        
        $this->inputParameters['fk_model'] = $model__id;
	}
}

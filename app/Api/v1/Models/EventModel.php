<?php

namespace App\Api\v1\Models;

use App\Api\v1\Models\DanteBaseModel;

class EventModel extends DanteBaseModel
{
    /* protected $connection = 'mysql_hdbrm_eventdb_ro'; */ /* set other DB connection */
    protected $table = 'event';
    
    /**
     * This array is used, from "__construct" to:
     * - build 'fillable' array (attributes that are mass assignable - 'id' and 'modified' are auto-generated)
     * 
     * And is also used from 'getValidatorRulesForStore' (that is in the 'IngvModel') and 'getValidatorRulesForUpdate', to:
     * - centralize the Validator rules used in the Controller;
     *
     * @var array
     */    
    protected $baseArray = [
        'id_locator'            => 'integer',
        'fk_pref_hyp'           => 'nullable|integer',
        'fk_pref_mag'           => 'nullable|integer',
		'fk_events_group'       => 'integer',
		'type_group'			=> 'integer',
		'fk_type_event'         => 'required|integer|exists:type_event,id',
        'fk_provenance'         => 'required|integer|exists:provenance,id'
    ];   
    
    public function __construct(array $attributes = []) {
        parent::updateBaseArray();
        parent::setFillableFromBaseArray();
        parent::__construct($attributes);
    }
    
    /**
     * Get the provenance record associated with the event.
     */
    public function provenance()
    {
        return $this->hasOne('App\Api\V1\WSs\Eventdb\Models\IngvProvenanceModel', 'id', 'fk_provenance');
    }
	
    /**
     * Get the provenance record associated with the event.
     */
    public function type_event()
    {
        return $this->hasOne('App\Api\V1\WSs\Eventdb\Models\IngvTypeEventModel', 'id', 'fk_type_event');
    }
    
    /**
     * Get the hypocenters for the event.
     */
    public function hypocenters()
    {
        return $this->hasMany('App\Api\V1\WSs\Eventdb\Models\IngvHypocenterModel', 'fk_event', 'id');
    }
}

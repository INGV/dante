<?php

namespace App\Api\v1\Models;

use App\Api\v1\Models\DanteBaseModel;

class EventModel extends DanteBaseModel
{
    protected $table = 'event';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_locator',
        'fk_pref_hyp',
        'fk_pref_mag',
        'fk_events_group',
        'type_group',
        'fk_type_event',
        'fk_provenance'
    ];  

    /**
     * Get the hypocenters for the event.
     */
    public function hypocenters()
    {
        return $this->hasMany('App\Api\v1\Models\HypocenterModel', 'fk_event', 'id');
    }
}

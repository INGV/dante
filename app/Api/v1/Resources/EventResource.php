<?php

namespace App\Api\v1\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        //dd($this->hypocenters);
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        //return parent::toArray($request);
        return [
            'id'                => $this->id,
            'id_locator'        => $this->name,
            'fk_pref_hyp'       => $this->note,
            'fk_pref_mag'       => $this->author,
            'fk_events_group'   => $this->fk_events_group,
            'type_group'        => $this->type_group,
            'fk_type_event'     => $this->fk_type_event,
            'fk_provenance'     => $this->fk_provenance,
            'hypocenters'       => $this->hypocenters,
            'inserted'          => (string)$this->inserted,
            'modified'          => (string)$this->modified
        ];        
    }
}

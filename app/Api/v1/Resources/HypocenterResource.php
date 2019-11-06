<?php

namespace App\Api\v1\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HypocenterResource extends JsonResource
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
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        //return parent::toArray($request);
        return [
            'id'                => $this->id,
            'ot'                => $this->ot,
            'lat'               => $this->lat,
            'lon'               => $this->lon,
            'geom'              => $this->geom,
            'depth'             => $this->depth,
            'fk_event'          => $this->fk_event,
            'fk_provenance'     => $this->fk_provenance,
            'pluto'             => $this->event,
            'inserted'          => (string)$this->inserted,
            'modified'          => (string)$this->modified
        ];        
    }
}

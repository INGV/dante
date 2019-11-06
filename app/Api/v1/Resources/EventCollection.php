<?php

namespace App\Api\v1\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EventResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        \Log::debug("START_2 - ".__CLASS__.' -> '.__FUNCTION__);
        return [
            'data' => $this->collection,
            'links' => [
                'self' => 'link-value',
            ],
        ];        
        \Log::debug("END_2 - ".__CLASS__.' -> '.__FUNCTION__);
        return parent::toArray($request);
    }
}

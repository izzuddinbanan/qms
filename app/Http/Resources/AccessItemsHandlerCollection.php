<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseCollection;

class AccessItemsHandlerCollection extends BaseCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return AccessItemsHandlerResource::collection($this->collection);
    }
}

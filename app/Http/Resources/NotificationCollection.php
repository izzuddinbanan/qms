<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseCollection;

class NotificationCollection extends BaseCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return NotificationResource::collection($this->collection);
    }
}

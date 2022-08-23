<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class DrawingSetCollection extends BaseResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return DrawingSetResource::collection($this->collection);
    }
}

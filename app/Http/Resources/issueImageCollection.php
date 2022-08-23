<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseCollection;

class issueImageCollection extends BaseCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return issueImageResource::collection($this->collection);

    }
}

<?php

namespace App\Http\Resources;


class FormCollection extends BaseCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return FormResource::collection($this->collection);
    }
}

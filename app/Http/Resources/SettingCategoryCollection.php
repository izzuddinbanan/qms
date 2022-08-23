<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseCollection;

class SettingCategoryCollection extends BaseCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return SettingCategoryResource::collection($this->collection);
    }
}

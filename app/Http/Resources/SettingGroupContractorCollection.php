<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseCollection;

class SettingGroupContractorCollection extends BaseCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return SettingGroupContractorResource::collection($this->collection);
    }
}

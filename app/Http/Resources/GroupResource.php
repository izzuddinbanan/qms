<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class GroupResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $group = [
            "group_id"                => $this->id,
            "name"                    => $this->display_name,
            "Abbrieviation"           => $this->abbreviation_name,
        ];
        
        return convert_null_to_string($group);
    }
}

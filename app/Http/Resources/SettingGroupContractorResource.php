<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class SettingGroupContractorResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $groupCon = [
            "contractor_id"             => $this->groupDetails->id,
            "name"                      => $this->groupDetails->display_name,
            "abbreviation_name"         => $this->groupDetails->abbreviation_name,
            "description"               => $this->groupDetails->description,
        ];
        return convert_null_to_string($groupCon);
    }
}

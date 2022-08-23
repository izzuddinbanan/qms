<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class GeneralResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $general = [
            "category"              => $this->category,
            "priority"              => $this->priority,
            "contractor"            => $this->contractor,
            "new_issue_status"      => $this->new,
            "start_work_status"     => $this->start_work,
            "sub_contractor"        => $this->subContractor,
            "location_status"       => $this->location_status,
        ];

        $general["not_me"] = $this->not_me[0]["internal_color"];
        return convert_null_to_string($general);
    }
}

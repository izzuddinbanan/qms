<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class DrillResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $drill = [
            "id"             => $this->id,
            "link_plan_id"   => $this->to_drawing_plan_id,
            "pos_x"          => $this->position_x,
            "pos_y"          => $this->position_y,
        ];
        return convert_null_to_string($drill);
    }
}

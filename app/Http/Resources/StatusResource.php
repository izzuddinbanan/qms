<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class StatusResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $status = [
            "id"                     => $this->id,
            "status_external"        => $this->external,
            "status_ex_color"        => $this->external_color,
            "status_internal"        => $this->internal,
            "status_in_color"        => $this->internal_color,
        ];


        // if($status["id"] == 3){
        //     $status["external"] = "Accept";
        // }

        // if($status["id"] == 5){
        //     $status["external"] = "Work in Progress";
        // }


        return convert_null_to_string($status);
    }
}

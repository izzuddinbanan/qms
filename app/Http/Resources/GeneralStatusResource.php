<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;


class GeneralStatusResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $generalStatus = [
            "id"                => $this->id,
            "name"              => $this->name,
            "color"             => $this->color,
        ];

        return convert_null_to_string($generalStatus);
    }
}

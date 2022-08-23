<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class FormGroupStatusResource extends BaseResource
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
            "id"                => $this->id,
            "name"              => $this->name,
            "color"             => $this->color_code,
        ];

        return convert_null_to_string($status);
    }
}

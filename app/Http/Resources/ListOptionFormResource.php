<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class ListOptionFormResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $form = [
            "id"    => $this->id,
            "name"  => $this->name,
        ];

        return convert_null_to_string($form);
    }
}

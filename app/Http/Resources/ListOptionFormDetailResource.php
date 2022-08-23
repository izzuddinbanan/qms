<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class ListOptionFormDetailResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            "updated_at"    => $this->updated_at,
            "form"          => $this->form,
        ];

        return convert_null_to_string($data);
    }
}

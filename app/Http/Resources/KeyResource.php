<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class KeyResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $item_submmitted = [
            "id"        => $this->id,
            "name"      => $this->name,
            "code"      => $this->code,
            "possessor" => $this->possessor,
        ];
        return convert_null_to_string($item_submmitted);
    }
}

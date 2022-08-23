<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class LanguageResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $language = [
            "id"                => $this->id,
            "name"              => $this->name,
            "abv_name"          => $this->abbreviation_name,
        ];

        return convert_null_to_string($language);
    }
}

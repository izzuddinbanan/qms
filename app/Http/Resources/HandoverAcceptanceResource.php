<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class HandoverAcceptanceResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $termsConditions = [
            "id"                            => $this->id,
            "designation"                   => $this->designation,
            "termsConditions"               => $this->termsConditions,
        ];
        
        return convert_null_to_string($termsConditions);
    }
}

<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class AccessItemsManagementResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $plan = [
            "id"                => $this->id,
            "name"              => $this->name,
            "item_submitted"   => new KeyCollection($this->itemManagementSubmit),
        ];
        
        return convert_null_to_string($plan);
    }
}

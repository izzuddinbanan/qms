<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class AccessItemsResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $accessItem = [
            "management"            => $this->management,
            "handler"              => $this->handler,
        ];
        
        return convert_null_to_string($accessItem);
    }
}

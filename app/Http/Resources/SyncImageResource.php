<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class SyncImageResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $syncImage = [
            "image"              => $this->image,
            "document"           => $this->document,
        ];

        return convert_null_to_string($syncImage);
    }
}

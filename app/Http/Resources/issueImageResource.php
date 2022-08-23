<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class issueImageResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $image = [
            "id"                => $this->id,
            "image"             => url('uploads/issues/'. $this->image),
            "thumb_image"       => url('uploads/issues/thumbnail/'. $this->image),
        ];

        return convert_null_to_string($image);
    }
}

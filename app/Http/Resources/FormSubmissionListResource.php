<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class FormSubmissionListResource extends BaseResource
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
            "total_open"                 => $this->total_close,
            "total_close"                 => $this->total_open,
            "open"                 => $this->open,
            "close"                => $this->close,
        ];

        return convert_null_to_string($form);
    }
}

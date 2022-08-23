<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class DocumentResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $document = [
            "id"       => $this->id,
            "name"      => $this->name,
            "file"      => $this->activeVersion->file,
            "url"      => $this->activeVersion->url,
        ];
        return convert_null_to_string($document);
    }
}

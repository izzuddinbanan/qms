<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class FormHistoryResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $history = [
            "id"                => $this->id,
            "remarks"           => $this->remarks,
            "status"            => new FormGroupStatusResource($this->status),
            "user"              => new UserResource($this->submission->user),
            "created_at"        => $this->created_at->toDatetimeString(),
            "updated_at"        => $this->updated_at->toDatetimeString(),
        ];

        return convert_null_to_string($history);
    }
}

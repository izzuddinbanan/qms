<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class CustomerHistoryIssueResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $issue = [
            "id"                => $this->id,
            "temp_reference"    => $this->temp_reference,
            "image"             => isset($this->image) ? url('uploads/issues/'. $this->image) : "",
            "image"             => new HistoryImageCollection($this->images),
            // "image"             => isset($this->image) ? url('uploads/issues/'. $this->image) : url('assets/images/no_image.png'),
            "remarks"           => $this->remarks,
            "date_post"         => $this->created_at->format('d M Y, h:i a'),
            "status_id"         => $this->status_id,
            "status_external"   => $this->status->external,
            "status_ex_color"   => $this->status->external_color,
            "status_internal"   => $this->status->internal,
            "status_in_color"   => $this->status->internal_color,
            "user"              => new UserResource($this->user),
        ];
        return convert_null_to_string($issue);
    }
}

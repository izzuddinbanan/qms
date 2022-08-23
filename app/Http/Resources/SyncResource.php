<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class SyncResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $sync = [
            "total_success"        => $this->total_success,
            "total_failed"         => $this->total_failed,
            "success_message"      => $this->success_message,
            "failed_message"       => $this->fail_message,
        ];

        return convert_null_to_string($sync);
    }
}

<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class RoleResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $role = [
            "id"                => $this->roles->id,
            "name"              => $this->roles->name,
            "display_name"      => $this->roles->display_name,
        ];

        return convert_null_to_string($role);

    }
}

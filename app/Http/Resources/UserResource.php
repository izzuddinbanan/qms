<?php

namespace App\Http\Resources;

// use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BaseResource;
use App\Entity\RoleUser;

class UserResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = [
            "id"            => $this->id,
            'user_id'       => $this->buyer_id,
            "name"          => $this->name,
            "email"         => $this->email,
            "contact"       => $this->contact,
            "avatar"        => isset($this->avatar) ? url('uploads/avatars/'. $this->avatar) : url('assets/images/no_image.png'),
            "current_role"  => $this->current_role,
        ];

        if($this->current_role == 7){
            $role_id = RoleUser::where('user_id', $this->id)->where('role_id', 7)->get();
        }
        else{
            $role_id = RoleUser::where('user_id', $this->id)->where('project_id', 0)->get();    
        }
        
        $user["roles"] = new RoleCollection($role_id);

        isset($this->token) ? $user['token'] = $this->token : '';
        return convert_null_to_string($user);
    }
}

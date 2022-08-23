<?php

namespace App\Http\Resources;

use Auth;
use App\Entity\SettingType;
use App\Entity\SettingIssue;
use App\Http\Resources\BaseResource;

class SettingCategoryResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $user = Auth::user();
        
        $category = [
            "cat_id"       => $this->id,
            "cat_name"     => $this->name,
            "type"         => $this->type,       
            // "type"         => new SettingTypeCollection($this->hasTypes),
            // "setting"       => \App\Entity\SettingPriority::where('client_id', $this->client_id)->get(),
            
        ];
        return convert_null_to_string($category);
    }
}

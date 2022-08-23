<?php

namespace App\Http\Resources;

use Auth;
use App\Entity\Language;
use App\Http\Resources\BaseResource;

class SettingIssueResource extends BaseResource
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
        

        if($user->language_id != 1){
            
            $this->data_lang = (array) json_decode($this->data_lang);
            $lang = Language::find($user->language_id);

            if(isset($this->data_lang[$lang->abbreviation_name])){
                
                if($this->data_lang[$lang->abbreviation_name]->name != "")
                    $this->name = $this->data_lang[$lang->abbreviation_name]->name;
            }
        }

        $project = [
            "group_id"      => $this->group_id,
            "issue_id"      => $this->id,
            "type_id"       => $this->type_id,
            "issue_name"          => $this->name,
        ];
        return convert_null_to_string($project);
    }
}

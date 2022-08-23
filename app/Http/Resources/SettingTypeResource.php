<?php

namespace App\Http\Resources;

use Auth;
use App\Entity\Language;
use App\Http\Resources\BaseResource;

class SettingTypeResource extends BaseResource
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

        $type = [
            "type_id"       => $this->id,
            "cat_id"        => $this->category_id,
            "type_name"     => $this->name,
            "issue"         => new SettingIssueCollection($this->issue),
            
        ];
        return convert_null_to_string($type);
    }
}

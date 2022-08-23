<?php

namespace App\Http\Resources;

use Auth;
use App\Entity\Language;
use App\Http\Resources\BaseResource;

class SettingPriorityResource extends BaseResource
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

            $lang = Language::find($user->language_id); 
            $this->priority->data_lang = (array) json_decode($this->priority->data_lang);


            if(isset($this->priority->data_lang[$lang->abbreviation_name])){
                
                if($this->priority->data_lang[$lang->abbreviation_name]->name != "")
                    $this->priority->name = $this->priority->data_lang[$lang->abbreviation_name]->name;
            }
        }

        $category = [
            "id"            => $this->priority->id,

            "name"          => $this->priority->name . ' ('. $this->priority->no_of_days .' days)' ,
            "no_of_days"    => $this->priority->no_of_days,
        ];
        return convert_null_to_string($category);
    }
}

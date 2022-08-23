<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class HandoverSurveyResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $survey = [
            "id"                => $this->id,
            "question"          => $this->question,
            "sequence"          => $this->sequence,
            "type"              => $this->type,
            "project_id"        => $this->project_id,
        ];
        
        return convert_null_to_string($survey);
    }
}

<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class DrawingSetResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->handover_form)
        {
            $close_and_handover_form[] = (new AllFormResource($this->close_and_handover_form));
        }

        $DrawingSet = [
            "id"                        => $this->id,
            "name"                      => $this->name,
            "close_and_handover_form"   => $this->handover_form ? $close_and_handover_form : [],
        ];
        return convert_null_to_string($DrawingSet);
    }
}

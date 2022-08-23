<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class AccessItemsTransactionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $item_submmitted = [
            "id"                            => $this->id,
            'owner_name'                    => $this->drawingPlan->unitOwner->name,
            "code"                          => $this->code,
            "possession"                    => $this->possession(),
            "items"                         => $this->items,
            "status"                        => $this->getStatus(),
            "drawing_plan_id"               => $this->drawing_plan_id,
            "signature_receive"             => $this->sign_receive_url,
            "signature_submit"              => $this->sign_submit_url,
            "signature_receive_datetime"    => date('Y/m/d h:i a', strtotime($this->signature_receive_datetime)),
            "signature_submit_datetime"     => date('Y/m/d h:i a', strtotime($this->signature_submit_datetime)),
            "name_receive"                  => $this->name_receive,
            "name_submit"                   => $this->name_submit,
            "remarks"                       => $this->internal_remarks,
            // "external_remarks"              => $this->external_remarks,
            "created_at"                    => $this->created_at->toDateTimeString(),
        ];
        
        return convert_null_to_string($item_submmitted);
    }
}

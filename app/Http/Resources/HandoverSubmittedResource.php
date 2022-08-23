<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class HandoverSubmittedResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $drawing_plan = $this->drawingPlan;

        $submitted_form = [
            'drawing_plan_id'       => $drawing_plan,
            'checklist'             => 'Handover',
            'status'                => 'Submitted',
            'key_submission'        => $this->key_submission,
            'es_submission'         => $this->es_submission,
            'waiver_submission'     => $this->waiver_submission,
            'photo_submission'      => $this->photo_submission,
            'acceptance_submission' => $this->acceptance_submission,
            'survey_submission'     => $this->survey_submission,
            'pdf'                   => $this->pdf_name ? url('uploads/handover-form-submission/' . $this->pdf_name) : '',
            'created_at'            => date('d M Y, H:i A', strtotime($this->created_at)),
        ];

        return convert_null_to_string($submitted_form);
    }
}

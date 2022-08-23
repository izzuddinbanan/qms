<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;
use App\Http\Resources\IssueCollection;
use App\Entity\FormGroup;
use App\Entity\Issue;

class FormSubmissionResource extends BaseResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

            $issue_id_arr = [];
            foreach ($this->linkIssue as $key => $value) {

                $issue_id_arr[] = $value["id"];
            }

            $issue = Issue::whereIn('id', $issue_id_arr)->get();

        $formDetail = FormGroup::with([
                'latestVersion.forms.formAttributes.locations',
                'latestVersion.forms.formAttributes.attribute',
                'latestVersion.forms.formAttributes.roles'
            ])->find($this->form_group_id);
        $form = [
            "id"                => $this->id,
            // "form_name"         => $this->formGroupDetail->name,
            "reference"         => $this->reference_no,
            "user"              => $this->user,
            "status"            => new FormGroupStatusResource($this->status),
            "created_at"        => $this->created_at->format('d M Y, h:i a'),
            "updated_at"        => $this->updated_at->format('d M Y, h:i a'),
            "form_details"      => new FormOldResource($formDetail, $this->location_id),
            "link_issue"        => new IssueCollection($issue)
        ];

        return convert_null_to_string($form);
    }
}

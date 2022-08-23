<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class NotificationResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $logo = $this->issue->location->drawingPlan->drawingSet->project->logo;
        $app_logo = $this->issue->location->drawingPlan->drawingSet->project->app_logo;

        $notification = [
            "id"                    => $this->id,
            "issue_reference_id"    => $this->issue->reference,
            "location_name"         => $this->issue->location->name,
            "project_name"          => $this->issue->location->drawingPlan->drawingSet->project->name,
            "project_id"            => $this->issue->location->drawingPlan->drawingSet->project->id,
            "draw_set_id"           => $this->issue->location->drawingPlan->drawingSet->id,
            "draw_plan_id"          => $this->issue->location->drawingPlan->id,
            "location_id"           => $this->issue->location->id,
            "issue_id"              => $this->issue_id,
            "issue_status"          => (new StatusResource($this->issueStatus)),
            "project_logo"          => isset($logo) ? url('uploads/project_logo/'. $logo) : "",
            "project_app_logo"      => isset($app_logo) ? url('uploads/project_logo/'. $app_logo) : "",
            "message"               => $this->message,
            "type"                  => $this->type,
            "read_status"           => $this->read_status_id,
            "date_time"             => $this->created_at->format('d M Y, h:i a'),
            "cal_date_time"         => $this->created_at->diffForHumans(),
            "push_by"               => (new UserResource($this->pushBy)),
            "issue_created_by"      => (new UserResource($this->issue->createdBy)),
        ];

        return convert_null_to_string($notification);
    }
}

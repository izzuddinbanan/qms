<?php

namespace App\Http\Resources;

// use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BaseResource;


class IssueResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        // $history = \App\Entity\History::where('issue_id', $this->id)->get();
        $issue = [
            "id"                => $this->id,
            "reference"         => $this->reference,
            "temp_reference"    => $this->temp_reference,
            "image"             => count($this->startImage) ? url('uploads/issues/'. $this->startImage[0]['image']) : null,
            "thumb_image"       => count($this->startImage) ? url('uploads/issues/thumbnail/'. $this->startImage[0]['image']) : null,
            "first"             => new issueImageCollection($this->startImage),
            "last"              => new issueImageCollection($this->lastImage),
            "category"          => $this->category->name,
            "category_id"       => $this->category->id,
            "type"              => $this->type->name,
            "type_id"           => $this->type->id,
            "issue"             => $this->issue->name,
            "issue_id"          => $this->issue->id,
            "join_id"           => $this->merge_issue_id,
            "priority"          => $this->priority ? $this->priority->name : null,
            "priority_id"       => $this->priority ? $this->priority->id : null,
            "pos_x"             => $this->position_x,
            "pos_y"             => $this->position_y,
            "remarks"           => $this->remarks,
            "due_by"            => $this->due_by,
            "status_id"         => $this->status->id,
            "status_external"   => $this->status->external,
            "status_ex_color"   => $this->status->external_color,
            "status_internal"   => $this->status->internal,
            "status_in_color"   => $this->status->internal_color,
            "assigned_to"       => $this->assigned_to,
            "assigned_count"    => $this->assigned_count,
            "handover_status"   => $this->handover_status,
            "created_at"        => $this->created_at->format('d/m/Y'),
            "creted_by"         => (new UserResource($this->createdBy) === null ) ?  '' : new UserResource($this->createdBy),
            "inspector"         => (new UserResource($this->inspector) === null ) ? '' : new UserResource($this->inspector) ,
            "owner"             => (new UserResource($this->owner) === null ) ? '' : new UserResource($this->owner)  ,
            "group"             => new GroupResource($this->contractor),
            // "history"           => isset($this->history) ? new HistoryIssueCollection($this->history) : array(),
            "total_history"     => $this->history->count(),
            "history"           => new HistoryIssueCollection($this->historyDescContractorInspector),
            "customer_history"  => new CustomerHistoryIssueCollection($this->historyDescCust),
            "merge_issue"       => new IssueCollection($this->mergeIssue),

        ];

        return convert_null_to_string($issue);
    }
}

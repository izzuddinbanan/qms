<?php

namespace App\Http\Resources;

// use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BaseResource;

class PlanResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {      
        // $location_id = array();
        // foreach($this->location as $key => $val){
        //     $location_id[] = $val->id;
        // }

        // if(!$issue = \App\Entity\Issue::whereIn('location_id', $location_id) ->get()){
        //     $issue = "";
        // }
        // $user= \Auth::user();
        // if($user->current_role == 7){
        //     $plan = [
        //         "id"                => $this->id,
        //         "default"           => $this->default,
        //         "name"              => $this->name,
        //         "image"             => isset($this->file) ? url('uploads/drawings/'. $this->file) : url('assets/images/no_image.png'),
        //         "thumb_image"       => isset($this->file) ? url('uploads/drawings/thumbnail/'. $this->file) : url('assets/images/no_image.png'),
        //         "width"             => $this->width,
        //         "height"            => $this->height,
        //         "type"              => $this->types,
        //         "phase"             => $this->phase,
        //         "block"             => $this->block,
        //         "level"             => $this->level,
        //         "unit"              => $this->unit,
        //         "drawing_set"       => new DrawingSetResource($this->drawingSet),
        //         "location"          => new LocationCollection($this->location),
        //         // "issue"             => new IssueCollection($issue),
        //     ];
        // }
        // else{
        //     $plan = [
        //         "id"                => $this->id,
        //         "default"           => $this->default,
        //         "name"              => $this->name,
        //         "image"             => isset($this->file) ? url('uploads/drawings/'. $this->file) : url('assets/images/no_image.png'),
        //         "thumb_image"       => isset($this->file) ? url('uploads/drawings/thumbnail/'. $this->file) : url('assets/images/no_image.png'),
        //         "width"             => $this->width,
        //         "height"            => $this->height,
        //         "type"              => $this->types,
        //         "phase"             => $this->phase,
        //         "block"             => $this->block,
        //         "level"             => $this->level,
        //         "unit"              => $this->unit,
        //         "drawing_set"       => new DrawingSetResource($this->drawingSet),
        //         "drill"             => new DrillCollection($this->drill),
        //         "location"          => new LocationCollection($this->location),
        //         // "issue"             => new IssueCollection($issue),
        //     ];    
        // }

        $joint_unit_owner_count = count($this->jointOwner);
            
            $plan = [
                "id"                => $this->id,
                "default"           => $this->default,
                "name"              => $this->name,
                "image"             => isset($this->file) ? url('uploads/drawings/'. $this->file) : url('assets/images/no_image.png'),
                "thumb_image"       => isset($this->file) ? url('uploads/drawings/thumbnail/'. $this->file) : url('assets/images/no_image.png'),
                "width"             => $this->width,
                "height"            => $this->height,
                "type"              => $this->types,
                "phase"             => $this->phase,
                "block"             => $this->block,
                "level"             => $this->level,
                "unit"              => $this->unit,
                "handover_status"   => $this->handover_status,
                "ready_to_handover" => $this->ready_to_handover,
                "all_location_ready"=> $this->all_location_ready,
                "drawing_set"       => new DrawingSetResource($this->drawingSet),
                "location"          => new LocationCollection($this->locationNoGeneral),
                "drill"             => new DrillCollection($this->whenLoaded('drill')),
                "item_submitted"    => new KeyCollection($this->itemSubmitted),
                "item_transaction"  => new AccessItemsTransactionCollection($this->ItemSubmittedTransaction),
                "unit_owner"        => $this->unitOwner ? [new UserResource($this->unitOwner)] : [],
                "joint_unit_owner"  => $joint_unit_owner_count > 0 ? [new UserResource($this->jointOwner)] : [],
            ];
            
        return convert_null_to_string($plan);
    }
}

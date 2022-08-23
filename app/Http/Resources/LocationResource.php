<?php

namespace App\Http\Resources;

use Auth;
use App\Entity\RoleUser;
use App\Entity\Issue;
use App\Entity\GroupProject;
use App\Http\Resources\BaseResource;

class LocationResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $points = [];
        $holder = explode(',', $this->points);

        // FOR MARKER GET POINT TOP LEFT
        $top_x = $holder[0];
        $top_y = $holder[1];
        for ($i = 0; $i < count($holder); $i += 2) {
            $t = [];
            $t['x'] = $holder[$i];
            $t['y'] = $holder[$i + 1];
        
            // FOR MARKER GET POINT TOP LEFT
            if($holder[$i] <= $top_x && $holder[$i + 1] <= $top_y){
                $top_x = $holder[$i];
                $top_y = $holder[$i + 1];
            }

            $points[] = $t;
        }

         $location = [
            'id'                => $this->id,
            'reference'         => $this->reference,
            'name'              => $this->name,
            'points'            => $points,
            'color'             => $this->color,
            'pos_x'             => $top_x + 10, 
            'pos_y'             => $top_y + 10,
            'status'            => $this->status,
        ];


        $user = Auth::user();

        ##contractor
        if($user->current_role == 5){

            $drawingSet = $this->drawingPlan->drawingSet;

            $group_id = GroupProject::where('project_id', $drawingSet->project_id )->select('group_id')->get();
            
            if(!$user_project = RoleUser::where('role_id', $user->current_role)->where('user_id', $user->id)->whereIn('group_id', $group_id)->select('group_id')->get())
            {
                $location['issue'] = "";

            }else{

                $issue = Issue::where('location_id', $this->id)->whereIn('group_id', $user_project)->where('status_id', '!=', 4)->get();

                $location['issue'] = new IssueCollection($issue);
            }
        }

        ##Inspector
        if($user->current_role == 4 || $user->current_role == 8){

                $location['issue'] = new IssueCollection($this->issues);
        }

        ##customer
        if($user->current_role == 7){

            $issue = Issue::where('location_id', $this->id)->where('created_by', $user->id)->get();

            $location['issue'] = new IssueCollection($issue);
        }

        $location['total_issues']  = $user->current_role == 7 ? $issue->count() : $this->issues()->count();


        return convert_null_to_string($location);
    }
}

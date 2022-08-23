<?php

namespace App\Http\Resources;

use Auth;
use App\Entity\Issue;
use App\Entity\DrawingSet;
use App\Entity\DrawingPlan;
use App\Entity\Language;
use App\Entity\LocationPoint;
use App\Http\Resources\BaseResource;

class ProjectResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {   
        $role = Auth::user()->current_role;

        if($role != 7) {

            $drawingSet = DrawingSet::where('project_id', $this->project->id)->select('id')->get();
            $drawingPlan = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->select('id')->get();
            $location = LocationPoint::whereIn('drawing_plan_id', $drawingPlan)->select('id')->get();
            if($this->user_id){

                ##inspector
                if($this->role_id == 4 || $this->role_id == 8){


                    $new = Issue::whereIn('location_id', $location)->new()->count();
                    $wip = Issue::whereIn('location_id', $location)->wip()->count();
                    $completed = Issue::whereIn('location_id', $location)->complete()->count();
                    $reject = Issue::whereIn('location_id', $location)->reject()->count();
                }

            }else{
                
                if($this->group_id){    //contractor

                    $new = Issue::whereIn('location_id', $location)->where('group_id', $this->group_id)->new()->count();
                    $wip = Issue::whereIn('location_id', $location)->where('group_id', $this->group_id)->wip()->count();
                    $completed = Issue::whereIn('location_id', $location)->where('group_id', $this->group_id)->complete()->count();
                    $reject = Issue::whereIn('location_id', $location)->where('group_id', $this->group_id)->reject()->count();

                }else{  //subcontractor

                    $user_id = Auth::user()->id;

                    $new        = Issue::whereIn('location_id', $location)->where('subcon_id', $user_id)->new()->count();
                    $wip        = Issue::whereIn('location_id', $location)->where('subcon_id', $user_id)->wip()->count();
                    $completed  = Issue::whereIn('location_id', $location)->where('subcon_id', $user_id)->complete()->count();
                    $reject     = Issue::whereIn('location_id', $location)->where('subcon_id', $user_id)->reject()->count();

                }
            }

        }



        $user = Auth::user();

        if($user->language_id != 1){
            
            $this->project->data_lang = (array) json_decode($this->project->data_lang);
            $lang = Language::find($user->language_id);

            if(isset($this->project->data_lang[$lang->abbreviation_name])){
                
                if($this->project->data_lang[$lang->abbreviation_name]->name != "")
                    $this->project->name = $this->project->data_lang[$lang->abbreviation_name]->name;

                if($this->project->data_lang[$lang->abbreviation_name]->description != "")
                    $this->project->description = $this->project->data_lang[$lang->abbreviation_name]->description;

                if($this->project->data_lang[$lang->abbreviation_name]->contract_no != "")
                    $this->project->contract_no = $this->project->data_lang[$lang->abbreviation_name]->contract_no;

                if($this->project->data_lang[$lang->abbreviation_name]->logo != "")
                    $this->project->logo = $this->project->data_lang[$lang->abbreviation_name]->logo;
                
                if($this->project->data_lang[$lang->abbreviation_name]->app_logo != "")
                    $this->project->app_logo = $this->project->data_lang[$lang->abbreviation_name]->app_logo;


            }
        }
        
        $project = [
            "id"                => $this->project->id,
            "project_name"      => $this->project->name,
            "description"       => $this->project->description,
            "contract_no"       => $this->project->contract_no,
            "logo"              => isset($this->project->logo) ? url('uploads/project_logo/'. $this->project->logo) : url('assets/images/no_image.png'),
            "app_logo"          => isset($this->project->app_logo) ? url('uploads/project_logo/'. $this->project->app_logo) : url('assets/images/no_image.png'),

        ];

        if($role != 7) {

            $project_additional = [
                "new_issue"         => $new,
                "WIP_issue"         => $wip,
                "reject_issue"      => $reject,
                "complete_issue"    => $completed,
                "inpection_open"    => 0,
                "inspection_closed" => 0,
            ];

            $project = array_merge($project, $project_additional);

        }
        return convert_null_to_string($project);
    }
}

<?php

namespace App\Http\Controllers\Traits;

use App\Entity\LocationPoint;
use App\Entity\DrawingSet;
use App\Entity\DrawingPlan;
use App\Entity\Project;
use App\Entity\Issue;
use App\Entity\RoleUser;
use App\Entity\User;
use App\Entity\GroupProject;
use App\Entity\GroupContractor;
use App\Http\Resources\PlanCollection;
use App\Http\Resources\BaseResource;
use App\Http\Resources\AllFormResource;

trait IssueCountDetails
{

    function IssueDetails($user, $data, $request, $project_id){
            
        ##return for issue listing
        $project = Project::find($project_id);

        ##CHECK IF USER IS INVOLVE in thiS PROJECT // inspector
        if($user->current_role == 4 || $user->current_role == 8 || $user->current_role == 7){

            if(!$role_user = RoleUser::where('user_id', $user->id)->where('project_id', $project->id)->where('role_id', $user->current_role)->where('client_id', $project->client_id)->first())
            {
                $status = $this->failedAppData('Cannot access to this project.');
                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);
                return new BaseResource($emptyData);
            }  
        }

        ##CHECK IF USER IS INVOLVE in thiS PROJECT // Contractor
        if($user->current_role == 5){
            $group_id = RoleUser::where("user_id", $user->id)
                        ->groupBy('group_id')
                        ->select('group_id')
                        ->get();

            if(!$project_user  = GroupProject::where('project_id', $project->id)->whereIn("group_id", $group_id)
                        ->get())
            {
                $status = $this->failedAppData('Cannot access to this project.');
                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);
                return new BaseResource($emptyData);
            }
        }


        ##sub-contractor
        if($user->current_role == 6){

            $location_id = Issue::where('subcon_id', $user->id)->select('location_id')->get();

            $drawingPlan = LocationPoint::whereIn('id', $location_id)->select('drawing_plan_id')->get();

            $drawingSet = DrawingPlan::whereIn('id', $drawingPlan)->select('drawing_set_id')->get();

            $project_id = DrawingSet::whereIn('id', $drawingSet)->groupBy('project_id')->pluck('project_id')->toArray();

            if(!in_array($project->id, $project_id)){

                $status = $this->failedAppData('Cannot access to this project.');
                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);
                return new BaseResource($emptyData);

            }
        }

        //not owner
        if($user->current_role != 7){
            $drawingSet = DrawingSet::where('project_id', $project->id)->select('id')->get(); 
            $drawingPlan = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->select('id')->get(); 
            $locationAll = LocationPoint::whereIn('drawing_plan_id', $drawingPlan)->select('id')->get();
            
            $plan = DrawingPlan::with('drill')
                                ->with('drawingSet')
                                ->with('location')
                                ->whereIn('drawing_set_id', $drawingSet)
                                ->get();
        }
        //owner
        else{
            $drawingSet = DrawingSet::where('project_id', $project->id)->select('id')->get(); 
            $drawingPlan = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->select('id')->get();
            $locationAll = LocationPoint::whereIn('drawing_plan_id', $drawingPlan)->select('id')->get(); 
            
            $plan_unit = DrawingPlan::whereIn('drawing_set_id', $drawingSet)
                                ->whereIn('types', ['unit'])->where('user_id', $user->id)
                                ->pluck('id')->toArray();
            $plan_common = DrawingPlan::whereIn('drawing_set_id', $drawingSet)
                                ->whereIn('types', ['common'])
                                ->pluck('id')->toArray();

            $plan_id = array_merge($plan_unit, $plan_common);

            $plan = DrawingPlan::with('drawingSet')
                                ->with('location')
                                ->whereIn('id', $plan_id)
                                ->get();
        }
        
        ##count listing
        ##contractor
        if($user->current_role == 5){
            $all_issue = Issue::whereIn('location_id', $locationAll)->whereIn("group_id", $group_id)->get();
        }

        ##inspector
        if($user->current_role == 4 || $user->current_role == 8){
            $all_issue = Issue::whereIn('location_id', $locationAll)->whereNotIn('status_id', ['4','7'])->get();
        }

        ##subcontractor
        if($user->current_role == 6){
            $all_issue = Issue::whereIn('location_id', $locationAll)->where('subcon_id', $user->id)->get();
        }

        ##customer
        if($user->current_role == 7){
            $all_issue = Issue::whereIn('location_id', $locationAll)->where('owner_id', $user->id)->get();
        }

        if($user->current_role != 7){
            $unit_area = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->where('types', 'unit')->orderBy('seq')->get();
            $common_area = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->where('types', 'common')->orderBy('seq')->get();
            $contractor  = GroupProject::where('project_id', $project->id)->get();
            $project_team_inspector_user_id = RoleUser::where('project_id', $project->id)->whereIn('role_id', [8, 4])->select('user_id')->get();
            $project_team_inspector = User::whereIn('id', $project_team_inspector_user_id)->get();
        }
        else{
            $unit_area = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->where('types', 'unit')->where('user_id', $user->id)->orderBy('seq')->get();
            $common_area = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->where('types', 'common')->orderBy('seq')->get();
            $contractor  = GroupProject::where('project_id', $project->id)->get();   
            $project_team_inspector_user_id = RoleUser::where('project_id', $project->id)->whereIn('role_id', [8, 4])->select('user_id')->get();
            $project_team_inspector = User::whereIn('id', $project_team_inspector_user_id)->get();
        }
        


        ##count listing menu in Issue

        ##contractor
        if($user->current_role == 5){
            $new = Issue::whereIn('location_id', $locationAll)->new()->whereIn("group_id", $group_id)->count();
            $wip = Issue::whereIn('location_id', $locationAll)->wipOnly()->whereIn("group_id", $group_id)->count();
            $wip_redo = Issue::whereIn('location_id', $locationAll)->wipRedo()->whereIn("group_id", $group_id)->count();
            $complete = Issue::whereIn('location_id', $locationAll)->complete()->whereIn("group_id", $group_id)->count();
            $pendingStart = Issue::whereIn('location_id', $locationAll)->pendingstart()->whereIn("group_id", $group_id)->count();
            $closed = Issue::whereIn('location_id', $locationAll)->closed()->whereIn("group_id", $group_id)->count();
            $void = Issue::whereIn('location_id', $locationAll)->reject()->whereIn("group_id", $group_id)->count();
            $notme = Issue::whereIn('location_id', $locationAll)->notme()->whereIn("group_id", $group_id)->count();
            $reassign = Issue::whereIn('location_id', $locationAll)->reassign()->whereIn("group_id", $group_id)->count();
            $lodged = Issue::whereIn('location_id', $locationAll)->lodged()->whereIn("group_id", $group_id)->count();
            $poa = Issue::whereIn('location_id', $locationAll)->poa()->whereIn("group_id", $group_id)->count();
            $decline = Issue::whereIn('location_id', $locationAll)->decline()->whereIn("group_id", $group_id)->count();
            $close_external = Issue::whereIn('location_id', $locationAll)->closeExternal()->whereIn("group_id", $group_id)->count();
            $pending_access = Issue::whereIn('location_id', $locationAll)->pendingAccess()->whereIn("group_id", $group_id)->count();
        }

        ##subcontractor
        if($user->current_role == 6){
            $new = Issue::whereIn('location_id', $locationAll)->new()->where("subcon_id", $user->id)->count();
            $wip = Issue::whereIn('location_id', $locationAll)->wipOnly()->where("subcon_id", $user->id)->count();
            $wip_redo = Issue::whereIn('location_id', $locationAll)->wipRedo()->where("subcon_id", $user->id)->count();
            $complete = Issue::whereIn('location_id', $locationAll)->complete()->where("subcon_id", $user->id)->count();
            $pendingStart = Issue::whereIn('location_id', $locationAll)->pendingstart()->where("subcon_id", $user->id)->count();
            $closed = Issue::whereIn('location_id', $locationAll)->closed()->where("subcon_id", $user->id)->count();
            $void = Issue::whereIn('location_id', $locationAll)->reject()->where("subcon_id", $user->id)->count();

            $reassign = Issue::whereIn('location_id', $locationAll)->reassign()->where("subcon_id", $user->id)->count();
            $notme = Issue::whereIn('location_id', $locationAll)->notme()->where("subcon_id", $user->id)->count();
            $lodged = Issue::whereIn('location_id', $locationAll)->lodged()->where("subcon_id", $user->id)->count();
            $poa = Issue::whereIn('location_id', $locationAll)->poa()->where("subcon_id", $user->id)->count();
            $decline = Issue::whereIn('location_id', $locationAll)->decline()->whereIn("group_id", $group_id)->count();
            $close_external = Issue::whereIn('location_id', $locationAll)->closeExternal()->where("subcon_id", $user->id)->count();
            $pending_access = Issue::whereIn('location_id', $locationAll)->pendingAccess()->where("subcon_id", $user->id)->count();
        }


        ##inspector
        if($user->current_role == 4 || $user->current_role == 8){
            $new = Issue::whereIn('location_id', $locationAll)->new()->count();
            $wip = Issue::whereIn('location_id', $locationAll)->wipOnly()->count();
            $wip_redo = Issue::whereIn('location_id', $locationAll)->wipRedo()->count();
            $complete = Issue::whereIn('location_id', $locationAll)->complete()->count();
            $pendingStart = Issue::whereIn('location_id', $locationAll)->pendingstart()->count();
            $closed = Issue::whereIn('location_id', $locationAll)->closed()->count();
            $void = Issue::whereIn('location_id', $locationAll)->reject()->count();
            
            $reassign = Issue::whereIn('location_id', $locationAll)->reassign()->count();
            $notme = Issue::whereIn('location_id', $locationAll)->notme()->count();
            $lodged = Issue::whereIn('location_id', $locationAll)->lodged()->count();
            $poa = Issue::whereIn('location_id', $locationAll)->poa()->count();
            $decline = Issue::whereIn('location_id', $locationAll)->decline()->count();
            $close_external = Issue::whereIn('location_id', $locationAll)->closeExternal()->count();
            $pending_access = Issue::whereIn('location_id', $locationAll)->pendingAccess()->count();
        }

        ##customer
        if($user->current_role == 7){
            $new = Issue::whereIn('location_id', $locationAll)->new()->where("owner_id", $user->id)->count();
            $wip = Issue::whereIn('location_id', $locationAll)->wipOnly()->where("owner_id", $user->id)->count();
            $wip_redo = Issue::whereIn('location_id', $locationAll)->wipRedo()->where("owner_id", $user->id)->count();
            $complete = Issue::whereIn('location_id', $locationAll)->complete()->where("owner_id", $user->id)->count();
            $pendingStart = Issue::whereIn('location_id', $locationAll)->pendingstart()->where("owner_id", $user->id)->count();
            $closed = Issue::whereIn('location_id', $locationAll)->closed()->where("owner_id", $user->id)->count();
            $void = Issue::whereIn('location_id', $locationAll)->reject()->where("owner_id", $user->id)->count();

            $reassign = Issue::whereIn('location_id', $locationAll)->reassign()->where("owner_id", $user->id)->count();
            $notme = Issue::whereIn('location_id', $locationAll)->notme()->where("owner_id", $user->id)->count();
            $lodged = Issue::whereIn('location_id', $locationAll)->lodged()->where("owner_id", $user->id)->count();
            $poa = Issue::whereIn('location_id', $locationAll)->poa()->where('owner_id', $user->id)->count();
            $decline = Issue::whereIn('location_id', $locationAll)->decline()->where('owner_id', $user->id)->count();
            $close_external = Issue::whereIn('location_id', $locationAll)->closeExternal()->where('owner_id', $user->id)->count();
            $pending_access = Issue::whereIn('location_id', $locationAll)->pendingAccess()->where('owner_id', $user->id)->count();
        }

            $issue_total = array('lodged' => $lodged, 'new' => $new,  'pending_start' => $pendingStart, 'wip' => $wip ,'not_me' => $notme,'wip_redo' => $wip_redo ,'reassign' => $reassign, 'closed' => $closed, 'complete' => $complete, 'void' => $void, 'poa' => $poa, 'decline' => $decline, 'close_external' => $close_external, 'pending_access'=>$pending_access);
            $issue_listing = array("project_id" => $project->id, "project_name" => $project->name,"issue" => $all_issue->count(), "unit" => $unit_area->count(), "common" => $common_area->count(), 'contractor' => $contractor->count(), 'issue_count' =>  $issue_total);

            $project_team_inspector_array = array();
            foreach ($project_team_inspector as $pti)
            {
                $project_team_inspector_array[] = array('id'=>$pti->id, 'name'=>$pti->name);
            }

            ##contractor count issue
            $count_group_array = array();
            foreach ($contractor as $contractors) {

                $group = GroupContractor::find($contractors->group_id);
                $count_group = Issue::whereIn('location_id', $locationAll)->where('group_id', $group->id)->count();

                $count_group_array[] = array('contractor_id' => $group->id, 'contractor_name' => $group->display_name, 'issue' => $count_group ); 


            ##unit count issue
            $count_unit_array = array();
            foreach($unit_area as $unit){

                $location_unit = LocationPoint::where('drawing_plan_id', $unit->id)->select('id')->get();
                ##contractor
                if($user->current_role == 5){
                    $issue_unit_count = Issue::whereIn('location_id', $location_unit)->whereIn("group_id", $group_id)->count();
                }
                ##subcontractor
                if($user->current_role == 6){
                    $issue_unit_count = Issue::whereIn('location_id', $location_unit)->where("subcon_id", $user->id)->count();
                }
                ##inspector
                if($user->current_role == 4 || $user->current_role == 8){
                    $issue_unit_count = Issue::whereIn('location_id', $location_unit)->whereNotIn('status_id', ['4','7'])->count();
                }

                ##customer
                if($user->current_role == 7){
                    $issue_unit_count = Issue::whereIn('location_id', $location_unit)->where('owner_id', $user->id)->count();
                }

                $count_unit_array[] = array('plan_id' => $unit->id, 'plan' => $unit->name,'file' => $unit->file,'block' => $unit->block, 'level' => $unit->level, 'unit' => $unit->unit, 'issue' => $issue_unit_count, 'handover_status' => $unit->handover_status, 'ready_to_handover' => $unit->ready_to_handover, 'all_location_ready' => $unit->all_location_ready);

            }


            ##common count issue
            $count_common_array = array();
            foreach($common_area as $common){

                $location_common = LocationPoint::where('drawing_plan_id', $common->id)->select('id')->get();
                ##contractor
                if($user->current_role == 5){
                    $issue_common_count = Issue::whereIn('location_id', $location_common)->whereIn("group_id", $group_id)->count();
                }
                ##subcontractor
                if($user->current_role == 6){
                    $issue_common_count = Issue::whereIn('location_id', $location_common)->where("subcon_id", $user->id)->count();
                }
                ##inspector
                if($user->current_role == 4 || $user->current_role == 8){
                    $issue_common_count = Issue::whereIn('location_id', $location_common)->whereNotIn('status_id', ['4','7'])->count();
                }

                ##customer
                if($user->current_role == 7){
                    $issue_common_count = Issue::whereIn('location_id', $location_common)->where('owner_id', $user->id)->count();
                }

                $count_common_array[] = array('seq' => $common->seq, 'plan_id' => $common->id, 'plan' => $common->name,'file' => $common->file, 'block' => $common->block, 'level' => $common->level, 'unit' => $common->unit, 'issue' => $issue_common_count);

            }    
        }

        array_push($data, $plan);
        $appointment_count = [
            'handover'      => 1,
            'inspection'    => 2,
        ];

        $appointment_listing = [
            'appointment_count' => $appointment_count,
        ];

        $plan->appData = $this->prepareAppData($request, $data);
        
        return (new PlanCollection($plan))->additional(['AppData' => $this->appData, 'issue_listing' => $issue_listing, 'appointment_listing' => $appointment_listing, 'contractor' => $count_group_array, 'project_team_inspector' => $project_team_inspector_array, 'unit' => $count_unit_array, 'common' => $count_common_array]);

    }
}

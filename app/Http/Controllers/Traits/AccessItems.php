<?php

namespace App\Http\Controllers\Traits;

use App\Entity\LocationPoint;
use App\Entity\DrawingSet;
use App\Entity\DrawingPlan;
use App\Entity\Project;
use App\Entity\RoleUser;
use App\Entity\GroupProject;
use App\Entity\GroupContractor;
use App\Http\Resources\AccessItemsResource;
use App\Http\Resources\AccessItemsManagementCollection;
use App\Http\Resources\AccessItemsHandlerCollection;
use App\Http\Resources\BaseResource;

trait AccessItems
{
	function AccessItems($user, $data, $request, $project_id)
	{
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

        //not owner
        if($user->current_role != 7){
            $drawingSet = DrawingSet::where('project_id', $project->id)->select('id')->get(); 
            $drawingPlan = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->select('id')->get();
            $locationAll = LocationPoint::whereIn('drawing_plan_id', $drawingPlan)->select('id')->get();
            

            $plan_management = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->whereHas('itemSubmitted', function ($query) {
                return $query->where('possessor', 'management');
            })->get();

            $plan_handler = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->whereHas('itemSubmitted', function ($query) {
                return $query->where('possessor', 'handler');
            })->get();

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

            // $plan = DrawingPlan::query()->whereIn('id', $plan_id);

            $plan_management = DrawingPlan::query()->whereIn('id', $plan_id)->whereHas('itemSubmitted', function ($query) {
            	return $query->where('possessor', 'management');
            });

            $plan_handler = DrawingPlan::query()->whereIn('id', $plan_id)->whereHas('itemSubmitted', function ($query) {
            	return $query->where('possessor', 'handler');
            });

            $plan_management = $plan_management->get();
            $plan_handler =$plan_handler->get();
        }

        $management = (new AccessItemsManagementCollection($plan_management));
        $handler = (new AccessItemsHandlerCollection($plan_handler));

        $returnValue = (object) array(
        	"management"	=> $management,
        	"handler"		=> $handler,
        );

        array_push($data, $returnValue);

        $returnValue->appData = $this->prepareAppData($request, $data);
        
        return (new AccessItemsResource($returnValue));

	}
}
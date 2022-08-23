<?php

namespace App\Http\Controllers\Api\v1\Manage;

use App\Entity\Project;
use App\Entity\RoleUser;
use App\Entity\GroupProject;
use App\Entity\Issue;
use App\Entity\DrawingPlan;
use App\Entity\LocationPoint;
use App\Entity\DrawingSet;
use App\Supports\AppData;
use App\Http\Resources\BaseResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectCollection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\v1\BaseApiController;
use Dingo\Api\Http\Request;
use App\Http\Controllers\Traits\ReturnErrorMessage;


class ProjectController extends BaseApiController
{
	use AppData, ReturnErrorMessage;

    /**
     * @SWG\Post(
     *     path="/project/listProject",
     *     summary="List all project belongs to the user",
     *     method="post",
     *     tags={"Project"},
     *     description="Use this API to retrieve list of all project belongs to user.",
     *     operationId="listProject",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="os",type="string",example="AND:0000"),
     *
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     * @param $string
     */
    public function listProject(Request $request)
    {
        $user = $this->user;
        $data = $this->data;

        ##contractor
        if($user->current_role == 5){
            $group_id = RoleUser::where("user_id", $user->id)
                        ->groupBy('group_id')
                        ->select('group_id')
                        ->get();

            $project  = GroupProject::with('project')
                        ->whereIn("group_id", $group_id)
                        ->groupBy('project_id')
                        ->get();
        }
        
        ##inspector
        if($user->current_role == 4 || $user->current_role == 8){
            $project = RoleUser::where("user_id", $user->id)
                        ->where('role_id', $user->current_role)
                        ->where('project_id', '!=', 0)
                        ->with('project')
                        ->get();
        }

        ##sub-contractor
        if($user->current_role == 6){

            $location_id = Issue::where('subcon_id', $user->id)->select('location_id')->get();

            $drawingPlan = LocationPoint::whereIn('id', $location_id)->select('drawing_plan_id')->get();

            $drawingSet = DrawingPlan::whereIn('id', $drawingPlan)->select('drawing_set_id')->get();

            $project = DrawingSet::whereIn('id', $drawingSet)->with('project')->get();
        }

        ##customer(unit owner)
        if($user->current_role == 7){
            $project_id=[];
        
        $drawingSet = DrawingPlan::where('user_id', $user->id)->select('drawing_set_id')->get();
        $project = DrawingSet::whereIn('id', $drawingSet)->with('project')->get();

        // $project_available = DrawingPlan::where('user_id', $user->id)
        //             ->join('drawing_sets', 'drawing_sets.id', '=', 'drawing_plans.drawing_set_id')
        //             ->join('projects', 'drawing_sets.project_id', 'projects.id')
        //             // ->whereNotNull('drawing_plans.block')
        //             // ->whereNotNull('drawing_plans.level')
        //             // ->whereNotNull('drawing_plans.unit')
        //             // ->whereIn('types', ['unit'])
        //             ->select(['projects.id'])
        //             ->get();
        //     foreach ($project_available as $key => $value) {
        //         array_push($project_id, $value['id']);
        //         // $project_id.push($value['id']);
        //     }                
        //     // return $project_id;
        //     // print_r($project_id);exit();
        //     $project = Project::whereIn('id',$project_id)->get();
        }

        if($project->isEmpty()) {
            return $this->emptyData($request, $data);
        }

        array_push($data, $project);

        $project->appData = $this->prepareAppData($request, $data);

        return (new ProjectCollection($project))->additional(['AppData' => $this->appData]);

    }
}

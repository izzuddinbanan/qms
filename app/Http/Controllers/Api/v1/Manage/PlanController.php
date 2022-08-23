<?php

namespace App\Http\Controllers\Api\v1\Manage;

use App\Entity\Project;
use App\Entity\RoleUser;
use App\Entity\GroupProject;
use App\Entity\DrawingPlan;
use App\Entity\DrawingSet;
use App\Entity\GroupContractor;
use App\Entity\LocationPoint;
use App\Entity\Issue;
use App\Supports\AppData;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use App\Http\Resources\PlanResource;
use App\Http\Resources\PlanCollection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\v1\BaseApiController;
use App\Http\Controllers\Traits\IssueCountDetails;


class PlanController extends BaseApiController
{
	use AppData, IssueCountDetails;

    /**
     * @SWG\Post(
     *     path="/plan",
     *     summary="get all Plan to save in mobile storage",
     *     method="POST",
     *     tags={"Plan"},
     *     description="Use this API to retrieve all drawing plan.",
     *     operationId="getAllPlan",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="project_id",type="string",example="1"),
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
    public function getAllPlan(Request $request)
    {
        $user = $this->user;
        $data = $this->data;


        if(!$project = Project::find($request->input('data.project_id'))){
            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, ["message"=>"Project are not exist."]);

            return new BaseResource($emptyData);
        }

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

        ##GET DRAWING SET_ID BELONG To THE PROJECT
        if(!$drawingSet = DrawingSet::where('project_id', $project->id)->select('id')->get()){
            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, ["message"=>"There are no drawing set or drawing plan in this project."]);

            return new BaseResource($emptyData);
        }

        return $this->IssueDetails($user, $data, $request, $project->id);
        
    }

    /**
     * @SWG\Post(
     *     path="/plan/search",
     *     summary="Search drawing plan",
     *     method="post",
     *     tags={"Plan"},
     *     description="Use this API to search drawing plan.",
     *     operationId="filter",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="filter",type="string",example="string"),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="project_id",required=true,type="string"),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     * @param $string
     */
    public function filter(Request $request)
    {
        $data = $this->data;
        $user = $this->user;

        ##GET DRAWING_SET_ID FOR THIS PROJECT
        if(!$drawing_set = DrawingSet::where('project_id', $request->project_id)->select('id')->get()){
            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data,  ["message" => "No results found."]);

            return new BaseResource($emptyData);
        }

        $drawing_plan = DrawingPlan::with('drill')
                                    ->with('drawingSet')
                                    ->with('location')
                                    ->whereIn('drawing_set_id', $drawing_set)
                                    ->where('name', 'like', '%' . $request->input('data.filter') . '%')
                                    ->get();


        if ($drawing_plan->isEmpty()) {

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, ["message" => "No results found."]);

            return new BaseResource($emptyData);
        }

        array_push($data, $drawing_plan);

        $drawing_plan->appData = $this->prepareAppData($request, $data);

        return (new PlanCollection($drawing_plan))->additional(['AppData' => $this->appData]);
    }
	
}

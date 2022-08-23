<?php

namespace App\Http\Controllers\Api\v1\Manage;

use Validator;
use App\Supports\AppData;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use App\Http\Controllers\Controller;
use App\Entity\LocationPoint;
use App\Entity\DrawingSet;
use App\Entity\DrawingPlan;
use App\Entity\GeneralStatus;
use App\Entity\Project;
use App\Entity\Issue;
use App\Entity\GroupProject;
use App\Entity\GroupContractor;
use App\Http\Resources\PlanCollection;
use App\Http\Controllers\Api\v1\BaseApiController;
use App\Http\Controllers\Traits\IssueCountDetails;


class LocationController extends BaseApiController
{
	use AppData, IssueCountDetails;

	public function __construct()
    {
        parent::__construct();
    }

    /**
     * @SWG\Post(
     *     path="/location/updateStatus",
     *     summary="To update status locatio / ready for inspection/ close and handover",
     *     method="post",
     *     tags={"Location"},
     *     description="This Api will update location status. ",
     *     operationId="updateStatus",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="location_id",type="string",example="1"),
     *                      @SWG\Property(property="status_id",type="string",example="1"),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function updateStatus(Request $request)
    {
        $data = $this->data;
        $user = $this->user;

        $rules = [
            'location_id'         => 'required|exists:locations,id',
            'status_id'           => 'required|exists:general_status,id',
        ];

        $message = [];

        $validator = Validator::make($request->input('data'), $rules, $message);

        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if(!$location = LocationPoint::where('id', $request->input('data.location_id'))->first()){

            $status = $this->failedAppData('Location not found');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if(!$status = GeneralStatus::where('id', $request->input('data.status_id'))->where('type', 'location')->first()){

        	$status = $this->failedAppData('Status not found');
            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        //not ready to handover
        if($request->input('data.status_id') == 1)
        {
            $location->update(['status_id' => $request->input('data.status_id')]);

            $location = LocationPoint::where('id', $location->id)->with('drawingPlan.drawingSet')->first();

            $drawing_plan = $location->drawingPlan;

            $drawing_plan->update([
                'all_location_ready'    => 0,
            ]);

            $project_id = $location->drawingPlan->drawingSet->project_id;
            
            return $this->IssueDetails($user, $data, $request, $project_id);
        }
        else{
            $count_not_completed_issue = Issue::where('location_id', $request->input('data.location_id'))->where('status_id','!=',10)->count();
            if($count_not_completed_issue>0)
            {
                $issue = Issue::where('location_id', $request->input('data.location_id'))->first();
                $status = $this->failedAppData($location->name.' is NOT Ready For Inspection. Please make sure all defect is Close Int.');

                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }
            else{
                $location->update(['status_id' => $request->input('data.status_id')]);

                $location = LocationPoint::where('id', $location->id)->with('drawingPlan.drawingSet')->first();

                $drawing_plan = $location->drawingPlan;

                $all_locations = LocationPoint::where('drawing_plan_id', $drawing_plan->id)->get();

                foreach($all_locations as $all_location)
                {
                    if($all_location->status_id != 2)
                    {
                        $project_id = $location->drawingPlan->drawingSet->project_id;
                
                        return $this->IssueDetails($user, $data, $request, $project_id); 
                    }
                }

                $drawing_plan->update([
                    'all_location_ready' => 1,
                ]);

                $project_id = $location->drawingPlan->drawingSet->project_id;
                return $this->IssueDetails($user, $data, $request, $project_id);
            }
        }
    }
}

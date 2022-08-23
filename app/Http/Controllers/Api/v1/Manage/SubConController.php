<?php

namespace App\Http\Controllers\Api\v1\Manage;

use Validator;
use App\Entity\User;
use App\Entity\Project;
use App\Entity\RoleUser;
use App\Entity\GroupProject;
use App\Entity\Issue;
use App\Supports\AppData;
use Dingo\Api\Http\Request;
use App\Http\Resources\BaseResource;
use App\Http\Controllers\Api\v1\BaseApiController;
use App\Http\Controllers\Traits\ReturnErrorMessage;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Traits\IssueCountDetails;


class SubConController extends BaseApiController
{

    use AppData, ReturnErrorMessage, IssueCountDetails;

    /**
     * @SWG\Post(
     *     path="/subcontractor/add",
     *     summary="Add new subcontractor by contractor",
     *     method="post",
     *     tags={"SubContractor"},
     *     description="Use this API to add new subcontractor.",
     *     operationId="addSubContractor",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="project_id",type="string",example="2"),
     *                      @SWG\Property(property="name",type="string",example="subcontractor"),
     *                      @SWG\Property(property="email",type="string",example="subcontractor@qms.com"),
     *                      @SWG\Property(property="password",type="string",example="123456"),
     *                      @SWG\Property(property="contact",type="string",example="012-5145897"),
     *                      @SWG\Property(property="os",type="string",example="AND:0000"),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     * @param $string
     */
    public function addSubContractor(Request $request)
    {
        $user = $this->user;
        $data = $this->data;

        ##only contractor can add subcon
        if($user->current_role != 5){
            return $this->failData($request, $data, "You are not allowed to do this action.");
        }

        $rules = [
            'project_id'    => 'required',
            'name'          => 'required|max:255',
            'email'         => 'required|max:255',
            'password'      => 'required',
            'contact'       => 'required|max:15',
        ];

        $message = [
            'data.project_id.required'          => "Project is required.",
            'data.name.required'                => "Name is required.",
            'data.email.required'               => "Email is required.",
            'data.password.required'            => "Password is required.",
        ];


        $validator = Validator::make($request->input('data'), $rules, $message);
        if ($validator->fails()) {
            return $this->failValidation($request, $validator, $data);
        }

        if(!$checkUser = User::where('email', $request->input('data.email'))->first()){

            $checkUser = User::create([

                'name'      => $request->input('data.name'),
                'email'     => $request->input('data.email'),
                'password'  => bcrypt($request->input('data.password')),
                'contact'   => $request->input('data.contact'),
            ]);
        }

        if(!$project = Project::find($request->input('data.project_id'))){
            return $this->failData($request, $data, "Project not exist.");
        }

        if($checkRole = RoleUser::where('user_id', $checkUser->id)->whereIn('role_id', [1, 2, 3, 4, 7, 8])->first()){
            return $this->failData($request, $data, "This email cannot be used.");
        }

        if($checkSubCon = RoleUser::where('user_id', $checkUser->id)->where('role_id', 6)->where('client_id', $project->client_id)->first()){
            return $this->failData($request, $data, "User already exist.");
        }

        $groupCon = RoleUser::where('user_id', $user->id)->where('role_id', 5)->select('group_id')->get();

        if(!$groupProject = GroupProject::whereIn('group_id', $groupCon)->where('project_id', $project->id)->first()){
            return $this->failData($request, $data, "You dont have access to this project.");
        }

        $newSubCon = RoleUser::create([
            'user_id'       => $checkUser->id,
            'role_id'       => 6,
            'project_id'    => 0,
            'group_id'      => 0,
            'client_id'     => $project->client_id,
        ]);

        array_push($data, $checkUser);

        $checkUser->appData = $this->prepareAppData($request, $data);

        return new UserResource($checkUser);
    }

    /**
     * @SWG\Post(
     *     path="/subcontractor/assign",
     *     summary="Assign Issue to subcontractor",
     *     method="post",
     *     tags={"SubContractor"},
     *     description="Use this API to assign issue to subcontractor.",
     *     operationId="assignSubContractor",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="issue_id",type="string",example="2"),
     *                      @SWG\Property(property="subcontractor_id",type="string",example="2"),
     *                      @SWG\Property(property="os",type="string",example="AND:0000"),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     * @param $string
     */
    public function assignSubContractor(Request $request)
    {
        $user = $this->user;
        $data = $this->data;

        ##only contractor can add subcon
        if($user->current_role != 5){
            return $this->failData($request, $data, "You are not allowed to do this action.");
        }

        if(!$issue = Issue::find($request->input('data.issue_id'))){
            return $this->failData($request, $data, "Issue not found");
        }

        if($issue->subcon_id != null){
            return $this->failData($request, $data, "Subcontractor already assign to this issue.");
        }

        $project = Project::find($issue->location->drawingPlan->drawingSet->project_id);

        
        $listGroup = RoleUser::where('user_id', $user->id)->where('role_id', 5)->select('group_id')->get();

        if(!$checkOwnIssue = Issue::where('id', $issue->id)->whereIn('group_id', $listGroup)->first()){
            return $this->failData($request, $data, "Issue not found.");
        }

        if(!$subcon = User::find($request->input('data.subcontractor_id'))){
            return $this->failData($request, $data, "Sub-Contractor not found.");
        }

        if(!$checkSubCon = RoleUser::where('user_id', $subcon->id)->where('role_id', 6)->where('client_id', $project->client_id)->first()){
            return $this->failData($request, $data, "Sub-Contractor not found.");
        }

        if($checkOwnIssue->subcon_id != null){
            return $this->failData($request, $data, "Issue already assign to Sub-Contractor");
        }

        $checkOwnIssue->update([
            'subcon_id'  => $subcon->id,
        ]);

        $history = History::create([
            'user_id'               => $user->id,
            'issue_id'              => $checkOwnIssue->id,
            'status_id'             => $checkOwnIssue->status_id,
            'remarks'               => "assign a subcontractor",
        ]);

        return $this->IssueDetails($user, $data, $request, $project->id);

    }
}

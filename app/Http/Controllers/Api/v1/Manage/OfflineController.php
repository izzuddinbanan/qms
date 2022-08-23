<?php

namespace App\Http\Controllers\Api\v1\Manage;

use Helper;
use Validator;
use App\Entity\Project;
use App\Entity\RoleUser;
use App\Entity\GroupProject;
use App\Entity\User;
use App\Entity\Issue;
use App\Entity\IssueImage;
use App\Entity\HistoryImage;
use App\Entity\LocationPoint;
use App\Entity\SettingIssue;
use App\Entity\SettingPriority;
use App\Entity\DrawingSet;
use App\Entity\DrawingPlan;
use App\Entity\History;
use App\Supports\AppData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Http\Resources\SyncResource;
use App\Http\Resources\SyncImageResource;
use App\Http\Controllers\Traits\PushNotification;
use App\Http\Controllers\Api\v1\BaseApiController;

class OfflineController extends BaseApiController
{
    use AppData;
    use PushNotification;

    /**
     * @SWG\Post(
     *     path="/offline/sync",
     *     summary="Sync all data.",
     *     method="post",
     *     tags={"Offline"},
     *     description="Use this API to sync all data.test in postman",
     *     operationId="syncData",
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
     *                      @SWG\Property(property="os",type="string",example="AND:0000"),
     *                      @SWG\Property(property="syncData",type="string",example="   "),
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
    public function syncData(Request $request)
    {
        $user = $this->user;
        $data = $this->data;

        if(!$project = Project::find($request->input('data.project_id'))){

            $message = "Project are not exist.";
            return $this->returnErrorApi($request, $data, $message);
        }

        ##CHECK IF USER IS INVOLVE in thiS PROJECT // inspector
        if($user->current_role == 4 || $user->current_role == 8){

            if(!$role_user = RoleUser::where('user_id', $user->id)->where('project_id', $project->id)->where('role_id', $user->current_role)->where('client_id', $project->client_id)->first()){
                $message = "Cannot access to this project.";
                return $this->returnErrorApi($request, $data, $message);
            }  
        }

        ##CHECK IF USER IS INVOLVE in thiS PROJECT // Contractor
        if($user->current_role == 5){
            $group_id = RoleUser::where("user_id", $user->id)->groupBy('group_id')->select('group_id')->get();

            if(!$project_user  = GroupProject::where('project_id', $project->id)->whereIn("group_id", $group_id)->get()){
                $message = "Cannot access to this project.";
                return $this->returnErrorApi($request, $data, $message);
            }
        }

        $input = json_decode($request->input('data.syncData'), true);

        if(count($input) > 0){
            //grouping all sync data by action
            foreach ($input as $key => $value) {
                $groupData[$value["action"]][] = $value;
            }
        }else{
            $message = "There are no value in syncData.";
            return $this->returnErrorApi($request, $data, $message);
        }

        ##looping to insert image into their issue.
        foreach ($groupData as $keyType => $valueType) {

            foreach ($valueType as $key => $value) {

                ##get image 
                if(isset($value["ref_code"])){
                    if(isset($request->file('data.image')[$value["ref_code"]])){

                        foreach ($request->file('data.image')[$value["ref_code"]] as $keyImage => $valueImage) {
                            $groupData[$keyType][$key]["image"][] = $valueImage;
                        }
                    }
                }
            }
        }

        ##start process data
        $total_success = 0; $total_failed  = 0;
        $dataAlert["success"] = array(); 
        $dataAlert["failed"] = array(); 
        
        ##add Issue
        if(isset($groupData["add_issue"])){
            $addIssue = $this->addIssueOffline($groupData["add_issue"], $data, $user, $request);
            
            $dataAlert = $this->groupResult($dataAlert, $addIssue);
        }

        ##add Info
        if(isset($groupData["add_info"])){
            $addInfo = $this->addHistoryOffline($groupData["add_info"], $data, $user, $request);
            
            $dataAlert = $this->groupResult($dataAlert, $addInfo);

        }

        ##Updated Issue
        if(isset($groupData["update_issue"])){
            $updateIssue = $this->updateIssueOffline($groupData["update_issue"], $data, $user, $request);
            
            $dataAlert = $this->groupResult($dataAlert, $updateIssue);

        }

        ##accept Issue
        if(isset($groupData["accept_issue"])){
            $acceptIssue = $this->acceptIssueOffline($groupData["accept_issue"], $data, $user, $request);

            $dataAlert = $this->groupResult($dataAlert, $acceptIssue);
        }

        ##startk work
        if(isset($groupData["start_work"])){
            
            $startWork = $this->startWorkOffline($groupData["start_work"], $data, $user, $request);

            $dataAlert = $this->groupResult($dataAlert, $startWork);
        }

        ##startk work
        if(isset($groupData["close_issue"])){
            
            $closeIssue = $this->closeIssueOffline($groupData["close_issue"], $data, $user, $request);

            $dataAlert = $this->groupResult($dataAlert, $closeIssue);
        }

        ##void Issue
        if(isset($groupData["void_issue"])){
            
            $voidIssue = $this->voidIssueOffline($groupData["void_issue"], $data, $user, $request);

            $dataAlert = $this->groupResult($dataAlert, $voidIssue);
        }


        ##count all success and error
        $total_success = count($dataAlert["success"]);
        $total_failed = count($dataAlert["failed"]);


        $details =  (object) array('total_success'  => $total_success, 
                            'total_failed'          => $total_failed, 
                            'success_message'       => $dataAlert["success"], 
                            'fail_message'          => $dataAlert["failed"]);

        array_push($data, $details);
        $details->appData = $this->prepareAppData($request, $data);
        return new SyncResource($details);
    }


    public function addIssueOffline($input, $data, $user, $request){
        
        $action = "add_issue";

        $result["success"] = array();
        $result["failed"] = array();
        foreach ($input as $key => $value) {    
            
            $rules = [
                'plan_id'             => 'required',
                'location_id'         => 'required',
                'issue_setting_id'    => 'required',
                'priority_id'         => 'required',
                'contractor_id'       => 'required',
                'due_by'              => 'required|date',
                'position_x'          => 'required',
                'position_y'          => 'required',
            ];

            $message = [];

            $validator = Validator::make($value, $rules, $message);
            
            if ($validator->fails()) {
                $status = $this->failedAppData($validator->errors()->first());

                $result = $this->returnResultArray($result, $value, $status["message"], $status["status"], $action);
                continue;
            }


            if($user->current_role == 5){   //contractor cannt add issue

                $result = $this->returnResultArray($result, $value, "You are not allowed to do this action.", "failed", $action);
                continue;

            }

            if($issues = Issue::where('id', $value["ref_code"])->OrWhere('temp_reference', $value["ref_code"])->first()){

                $result = $this->returnResultArray($result, $value, "The issue already sync to server.", "failed", $action);
                continue;
            }

            if(!$location = LocationPoint::find($value["location_id"])){

                $result = $this->returnResultArray($result, $value, "Location not found.", "failed", $action);
                continue;
            }


            if(!$issueSetting = SettingIssue::find($value["issue_setting_id"])){

                $result = $this->returnResultArray($result, $value, "Issue not found.", "failed", $action);
                continue;
            }

            if(!$priority = SettingPriority::find($value["priority_id"])){

                $result = $this->returnResultArray($result, $value, "Priority not found.", "failed", $action);
                continue;
            }

            $issue = Issue::create([
                'temp_reference'        => $value["ref_code"],
                'location_id'           => $value["location_id"],
                'setting_category_id'   => $issueSetting->category_id,
                'setting_type_id'       => $issueSetting->type_id,
                'setting_issue_id'      => $issueSetting->id,
                'priority_id'           => $value["priority_id"],
                'group_id'              => $value["contractor_id"],
                'due_by'                => $value["due_by"],
                'position_x'            => $value["position_x"],
                'position_y'            => $value["position_y"],
                'remarks'               => $value["comment"],
                'status_id'             => 2,
                'created_by'            => $user->id,

            ]);

            $now = \Carbon\Carbon::now('Asia/Kuala_Lumpur');

            $unique_ref = Helper::generateIssueReferenceByLocationID($value["location_id"]);

            $issue->forcefill(['reference' => $unique_ref])->save();
            $issue->forcefill(['inspector_id' => $user->id])->save();

            $title =  $location->drawingPlan->drawingSet->project->name;
            $message =  'New issue reported('. $issue->reference .')';

            $appData = array('type' => 'new_issue','project_id' => $location->drawingPlan->drawingSet->project_id ,'plan_id' => $location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);
            $contractor = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)->select('user_id')->get();
            $this->FCMnotification($title, $message, $appData, $contractor, $issue->id, $user->id);

            ##notification power user/ admin
            $admin_user_id = RoleUser::whereIn('role_id', [2, 3])->where('user_id', '!=', $issue->created_by)->where('project_id', $issue->location->drawingPlan->drawingSet->project_id)->select('user_id')->get();           
            $this->FCMnotification($title, $message, $appData, $admin_user_id, $issue->id, $user->id);

            $history = History::create([
                'user_id'               => $user->id,
                'issue_id'              => $issue->id,
                'status_id'             => $issue->status_id,
                'remarks'               => $value["comment"],
            ]);

            // get value["image"] dlam grouping
            if(isset($value["image"])){

                foreach ($value["image"] as $keyImage => $valueImage) {
                    $image = \App\Processors\SaveIssueProcessor::make($valueImage)->execute();

                    $seq = null;
                    
                    if($keyImage == 0){
                        $seq = 1;
                    }

                    $issue->images()->create([
                        'image' => $image,
                        'seq'   => $seq,
                        'type'  => 1,
                    ]);

                    $history->images()->create([
                        'image' => $image,
                    ]);
                }
            }

            $result = $this->returnResultArray($result, $value, "", "success", $action, $issue);
        }   

        return $result;
    }

    public function addHistoryOffline($input, $data, $user, $request){

        $action = "add_history";
        $result["success"] = array();
        $result["failed"] = array();

        foreach ($input as $key => $value) {

            $rules = [
                'issue_id'      => 'required',
            ];

            $message = [];  

            $validator = Validator::make($value, $rules, $message);

            if ($validator->fails()) {

                $status = $this->failedAppData($validator->errors()->first());
                $result = $this->returnResultArray($result, $value, $status["message"], $status["status"], $action);
                continue;
            }

            if(!$issue = Issue::where('id', $value["issue_id"])->OrWhere('temp_reference', $value["issue_id"])->first()){

                $result = $this->returnResultArray($result, $value, "Issue not found.", "failed", $action);
                continue;
            }

            if($checkHistory = History::where('temp_reference', $value["ref_code"])->where('issue_id', $issue->id)->first()){

                $result = $this->returnResultArray($result, $value, "This info already sync", "failed", $action);
                continue;
            }

            $history = History::create([
                'user_id'               => $user->id,
                'issue_id'              => $issue->id,
                'status_id'             => $issue->status_id,
                'remarks'               => $value["remarks"],
                'temp_reference'        => $value["ref_code"],
            ]);

            if(isset($value["image"])){
                foreach ($value["image"] as $keyImage => $valueImage) {

                    $image = \App\Processors\SaveIssueProcessor::make($valueImage)->execute();

                    $history->images()->create([
                        'image' => $image,
                    ]);
                }
            }

            $result = $this->returnResultArray($result, $value, "", "success", $action, $history);
        }

        return $result;
    }

    public function updateIssueOffline($input, $data, $user, $request){
        
        $action = "update_issue";
        $result["success"] = array();
        $result["failed"] = array();
        
        foreach ($input as $key => $value) {   

            $rules = [
                'issue_id'             => 'required',
            ];

            $message = [];

            $validator = Validator::make($value, $rules, $message);
            
            if ($validator->fails()) {
                $status = $this->failedAppData($validator->errors()->first());

                $result = $this->returnResultArray($result, $value, $status["message"], $status["status"], $action);
                continue;
            }

            if(!$issue = Issue::where('id', $value["issue_id"])->OrWhere('temp_reference', $value["issue_id"])->first()){

                $result = $this->returnResultArray($result, $value, "Issue not found.", "failed", $action);
                continue;

            }

            if($value["position_x"] == null){

                if(isset($value["issue_setting_id"])){


                    if(!$issueSetting = SettingIssue::find($value["issue_setting_id"])){

                        $status = $this->failedAppData('Issue not found');

                        $result = $this->returnResultArray($result, $value, "Issue not found.", "failed", $action);
                        continue;
                    }

                    $old_issue = Issue::where('id', $request->input('data.issue_id'))->with('category')->with('type')->with('issue')->with('contractor')->with('priority')->first();

                    $issue->update([
                        'setting_category_id'   => $issueSetting->category_id,
                        'setting_type_id'       => $issueSetting->type_id,
                        'setting_issue_id'      => $issueSetting->id,
                        'priority_id'           => $value["priority_id"],
                        'group_id'              => $value["contractor_id"],
                        'due_by'                => $value["due_by"],
                        'remarks'               => $value["comment"],
                    ]);

                    $history = History::create([
                        'user_id'               => $user->id,
                        'issue_id'              => $issue->id,
                        'status_id'             => $issue->status_id,
                        'remarks'               => "updated issue details",
                    ]);

                }else{

                    // reassign contractor
                    $issue->update([
                        'group_id'              => $value["contractor_id"],
                        'status_id'             => 2,
                    ]);


                    $history = History::create([
                        'user_id'               => $user->id,
                        'issue_id'              => $issue->id,
                        'status_id'             => $issue->status_id,
                        'remarks'               => "Reassign issue to a new contractor",
                    ]);

                }

                if (isset($value["remove"])) {
                    foreach ($value["remove"] as $key => $value) {
                        $issue_image = IssueImage::find($value);
                        $issue_image->delete();
                    }
                }

                if (isset($value["new"])) {

                    foreach ($value["new"] as $key => $value) {

                        $image = \App\Processors\SaveIssueProcessor::make($value)->execute();

                        $issue->images()->create([
                            'image' => $image,
                            'type'  => 1,
                        ]);

                        $history->images()->create([
                            'image' => $image,
                        ]);
                    }
                }


                $updated_issue = Issue::where('id', $issue->id)->with('category')->with('type')->with('issue')->with('priority')->with('contractor')->first();


            }else{

                if($user->current_role != 8){

                    $result = $this->returnResultArray($result, $value, "You are not allowed to do this action.", "failed", $action);
                    continue;                }
                
                $issue->update([
                    'position_x'            => $value["position_x"],
                    'position_y'            => $value["position_y"],
                ]);
            }

            $result = $this->returnResultArray($result, $value, "", "success", $action, $issue);
        }

        return $result;
    }

    public function acceptIssueOffline($input, $data, $user, $request){

        $action = "accept_issue";
        $result["success"] = array();
        $result["failed"] = array();

        foreach ($input as $key => $value) {

            $rules = [
                'issue_id'             => 'required',
                'status_id'            => 'required',
                'status_id'            => 'required',
            ];

            $message = [];

            $validator = Validator::make($value, $rules, $message);

            if ($validator->fails()) {

                $status = $this->failedAppData($validator->errors()->first());

                $result = $this->returnResultArray($result, $value, $status["message"], $status["status"], $action);
                continue;
            }

            if(!$issue = Issue::where('id', $value["issue_id"])->OrWhere('temp_reference', $value["issue_id"])->first()){

                $result = $this->returnResultArray($result, $value, "Issue not found.", "failed", $action);
                continue;

            }

            if($value["status_id"] == 3){

                $issue->update([
                    'status_id'            => 3,
                ]);

                History::create([
                    'user_id'       => $user->id,
                    'issue_id'      => $value["issue_id"],
                    'status_id'     => $issue->status_id,
                    'remarks'       => $value["comment"] === "" ? 'Accept the raise issue' : $value["comment"],
                ]);

                $message =  'Issue('. $issue->reference .') is changed to "Pending Start" status.';

                $appData = array('type' => 'accept_issue','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);


            }
            else if($value["status_id"] == 6){

                $issue->update([
                    'status_id'            => 7,
                    'group_id'             => null,
                ]);

                History::create([
                    'user_id'       => $user->id,
                    'issue_id'      => $value["issue_id"],
                    'status_id'     => $issue->status_id,
                    'remarks'       => $value["comment"] === "" ? 'Reject the raise issue. Not my issue' : $value["comment"] ,
                ]);

                $message =  'Issue('. $issue->reference .') needs to reassign.';
                $appData = array('type' => 'reject_issue','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);

            }else{

                $result = $this->returnResultArray($result, $value, "Status not found.", "failed", $action);
                continue;
            }

            $title =  $issue->location->drawingPlan->drawingSet->project->name;

            $inspector = User::where('id', $issue->inspector_id)->select('id as user_id')->get();
            $this->FCMnotification($title, $message, $appData, $inspector, $issue->id, $user->id);

            $contractor = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)->select('user_id')->get();
            $this->FCMnotification($title, $message, $appData, $contractor, $issue->id, $user->id);

            $result = $this->returnResultArray($result, $value, "", "success", $action, $issue);
        }

        return $result;
    }

    public function startWorkOffline($input, $data, $user, $request){

        $action = "start_work";
        $result["success"] = array();
        $result["failed"] = array();

        foreach ($input as $key => $value) {

            $rules = [
                'status_id'         => 'required',
                'start_date'        => 'required',
            ];

            $message = [];

            $validator = Validator::make($value, $rules, $message);
            
            if ($validator->fails()) {
                $status = $this->failedAppData($validator->errors()->first());

                $result = $this->returnResultArray($result, $value, $status["message"], $status["status"], $action);
                continue;
            }


            if(!$issue = Issue::where('id', $value["issue_id"])->OrWhere('temp_reference', $value["issue_id"])->first()){

                $result = $this->returnResultArray($result, $value, "Issue not found.", "failed", $action);
                continue;

            }

            if($value["status_id"] == 5){

                $message =  'Issue('. $issue->reference .') is changed to "W.I.P" status.';
                $remark = 'Start to fix the issue.';

                // $startDate = $value["start_date"];
                // $startDate = explode("/",$startDate);
                // $formatDate = $startDate[2] . '-' . $startDate[1] . '-' . $startDate[0];
                
                $issue->update([
                    'start_date'           => $value["start_date"],
                ]);

            }else if($value["status_id"] == 9){
                $message =  'Issue('. $issue->reference .') is changed to "Redo" status.';
                $remark = 'Redo fix the issue.';
            }else{

                $result = $this->returnResultArray($result, $value, "Status not found.", "failed", $action);
                continue;
            }

            $issue->update([
                'status_id'            => $value["status_id"],
            ]);

            History::create([
                'user_id'       => $user->id,
                'issue_id'      => $value["issue_id"],
                'status_id'     => $issue->status_id,
                'remarks'       => $remark,
            ]);

            $title =  $issue->location->drawingPlan->drawingSet->project->name;
            $appData = array('type' => 'start_fix','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);

            $inspector = User::where('id', $issue->inspector_id)->select('id as user_id')->get();
            $this->FCMnotification($title, $message, $appData, $inspector, $issue->id, $user->id);

            $contractor = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)->select('user_id')->get();
            $this->FCMnotification($title, $message, $appData, $contractor, $issue->id, $user->id);

            $result = $this->returnResultArray($result, $value, "", "success", $action, $issue);
        }

        return $result;
    }

    public function closeIssueOffline($input, $data, $user, $request){

        $action = "close_issue";

        $result["success"] = array();
        $result["failed"] = array();

        foreach ($input as $key => $value) {
      
            $rules = [
                'issue_id'         => 'required',
            ];

            $message = [];

            $validator = Validator::make($value, $rules, $message);
            
            if ($validator->fails()) {
                $status = $this->failedAppData($validator->errors()->first());

                $result = $this->returnResultArray($result, $value, $status["message"], $status["status"], $action);
                continue;
            }

            if(!$issue = Issue::where('id', $value["issue_id"])->OrWhere('temp_reference', $value["issue_id"])->first()){

                $result = $this->returnResultArray($result, $value, "Issue not found.", "failed", $action);
                continue;

            }

            $title =  $issue->location->drawingPlan->drawingSet->project->name;

            // if contractor
            if($user->current_role == 5){

                $history = History::create([
                    'user_id'       => $user->id,
                    'issue_id'      => $issue->id,
                    'status_id'     => 8,
                    'remarks'       => $value["remarks"],
                ]);

                $appData = array('type' => 'complete_issue','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);
            
                $message =  'Issue('. $issue->reference .') is changed to "Completed" status.';

                $inspector = User::where('id', $issue->inspector_id)->select('id as user_id')->get();
                $this->FCMnotification($title, $message, $appData, $inspector, $issue->id, $user->id);

                $contractor = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)->select('user_id')->get();
                $this->FCMnotification($title, $message, $appData, $contractor, $issue->id, $user->id);

                if(isset($value["image"])){

                    foreach ($value["image"] as $keyImage => $valueImage) {

                        $image = \App\Processors\SaveIssueProcessor::make($value)->execute();
                        
                        if($issue->status_id == 9){
                            $issue->lastImage()->delete();
                        }
                        
                        $issue->images()->create([
                            'image' => $image,
                            'type'  => 2,
                        ]);

                        $history->images()->create([
                            'image' => $image,
                        ]);
                    }
                }

                $issue->update(['status_id' => 8]);

            }else{

                //Ispector change status to redo
                if(isset($value["status_id"])){

                    if($value["status_id"] != 9){

                        $result = $this->returnResultArray($result, $value, "Status not found.", "failed", $action);
                        continue;
                    }

                    $issue->update(['status_id' => 9]);

                    $history = History::create([
                        'user_id'               => $user->id,
                        'issue_id'              => $issue->id,
                        'status_id'             => $issue->status_id,
                        'remarks'               => ($value["remarks"] == "") ? 'Redo the work.' : $value["remarks"],
                    ]);

                    $message =  'Issue('. $issue->reference .') is changed to "Redo" status.';

                    $appData = array('type' => 'redo_issue','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);

                    $contractor = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)->select('user_id')->get();
                    $this->FCMnotification($title, $message, $appData, $contractor, $issue->id, $issue->created_by);

                    if(isset($value["image"])){
                    
                        foreach ($value["image"] as $keyImage => $valueImage) {

                            $image = \App\Processors\SaveIssueProcessor::make($value)->execute();

                            $history->images()->create([
                                'image' => $image,
                            ]);
                        }
                    }

                }else{

                    $issue->update(['status_id' => 10]);

                    $history = History::create([
                        'user_id'               => $user->id,
                        'issue_id'              => $issue->id,
                        'status_id'             => $issue->status_id,
                        'remarks'               => $value["remarks"],
                    ]);

                    $message =  'Issue('. $issue->reference .') is changed to "Closed" status.';

                    $appData = array('type' => 'close_issue','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);
                    
                    $contractor = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)->select('user_id')->get();
                    $this->FCMnotification($title, $message, $appData, $contractor, $issue->id, $issue->created_by);


                    if(isset($value["image"])){
                        foreach ($value["image"] as $keyImage => $valueImage) {

                            $image = \App\Processors\SaveIssueProcessor::make($value)->execute();
                            $history->images()->create([
                                'image' => $image,
                            ]);
                        }
                    }
                }

            }

            $result = $this->returnResultArray($result, $value, "", "success", $action, $issue);
        }
        return $result;
    }

    public function voidIssueOffline($input, $data, $user, $request){

        $action = "void_issue";
        $result["success"] = array();
        $result["failed"] = array();

        foreach ($input as $key => $value) {
            
            $rules = [
                'issue_id'            => 'required',
            ];

            $message = [];

            $validator = Validator::make($value, $rules, $message);
            
            if ($validator->fails()) {
                $status = $this->failedAppData($validator->errors()->first());

                $result = $this->returnResultArray($result, $value, $status["message"], $status["status"], $action);
                continue;
            }

            if(!$issue = Issue::where('id', $value["issue_id"])->OrWhere('temp_reference', $value["issue_id"])->first()){

                $result = $this->returnResultArray($result, $value, "Issue not found.", "failed", $action);
                continue;

            }

            if($user->current_role == 5 || $user->current_role == 6){

                $result = $this->returnResultArray($result, $value, "You are not allowed to this action.", "failed", $action);
                continue;
            }


            $issue->forcefill([
                'void_by'   => $user->id,
                'status_id'   => 4,
            ])->save();

            History::create([
                'user_id'       => $user->id,
                'issue_id'      => $issue->id,
                'status_id'     => 4,
                'remarks'       => 'Void the issue.',
            ]);

            $result = $this->returnResultArray($result, $value, "", "success", $action, $issue);

        }
        return $result;
    }



    /**
     * @SWG\Post(
     *     path="/offline/syncImage",
     *     summary="Sync all image.",
     *     method="post",
     *     tags={"Offline"},
     *     description="Use this API to sync all image",
     *     operationId="syncImage",
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
    public function syncImage(Request $request)
    {
        $user = $this->user;
        $data = $this->data;

        $rules = [
            'project_id'             => 'required',
        ];

        $message = [
            'data.project_id.required'             => "Project id is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);


        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if(!$project = Project::find($request->input('data.project_id'))){

            $message = "Project are not exist.";
            return $this->returnErrorApi($request, $data, $message);
        }
        
        ##CHECK IF USER IS INVOLVE in thiS PROJECT // inspector
        if($user->current_role == 4 || $user->current_role == 8){

            if(!$role_user = RoleUser::where('user_id', $user->id)->where('project_id', $project->id)->where('role_id', $user->current_role)->where('client_id', $project->client_id)->first())
            {
                $message = "Cannot access to this project";
                return $this->returnErrorApi($request, $data, $message);
            }  
        }

        ##CHECK IF USER IS INVOLVE in thiS PROJECT // Contractor
        if($user->current_role == 5){
            $group_id = RoleUser::where("user_id", $user->id)->groupBy('group_id')->select('group_id')->get();

            if(!$project_user  = GroupProject::where('project_id', $project->id)->whereIn("group_id", $group_id)->get()){
                $message = "Cannot access to this project";
                return $this->returnErrorApi($request, $data, $message);
            }
        }

        $drawingSet = DrawingSet::where('project_id', $request->input('data.project_id'))->select('id')->get();
        $dataImage = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->select('id','file', 'updated_at')->get();
        
        $plan_id = array();

        foreach ($dataImage as $key => $value) {

            // $array["name"] = $value["name"];
            // $array["link"] = url('uploads/drawings/' . $value["file"]);
            // $array["link_thumb"] = url('uploads/drawings/thumbnail/' . $value["file"]);

            $dataImage[$key]->link = url('uploads/drawings/' . $value["file"]);
            $dataImage[$key]->link_thumb = url('uploads/drawings/thumbnail/' . $value["file"]);

            array_push($plan_id, $value["id"]);

            unset($dataImage[$key]["id"]);
        }



        $location = LocationPoint::whereIn('drawing_plan_id', $plan_id)->select('id')->get();

        if($user->current_role == 4 || $user->current_role == 8){
            $issue = Issue::whereIn('location_id', $location)->select('id')->get();
        }

        if($user->current_role == 5){
            $issue = Issue::whereIn('group_id', $group_id)->select('id')->get();
        }

        $issue_image = IssueImage::whereIn('issue_id', $issue)->get();

        foreach ($issue_image as $key => $value) {

            $array["file"] = $value["image"];
            $array["link"] = url('uploads/issues/' . $value["image"]);
            $array["link_thumb"] = url('uploads/issues/thumbnail/' . $value["image"]);
            $dataImage[] =  (object) $array;
        }

        $history = History::whereIn('issue_id', $issue)->select('id')->get();
        $historyImage = HistoryImage::whereIn('history_id', $history)->get();

        foreach ($historyImage as $key => $value) {

            $array["file"] = $value["image"];
            $array["link"] = url('uploads/issues/' . $value["image"]);
            $array["link_thumb"] = url('uploads/issues/thumbnail/' . $value["image"]);
            // $dataImage[] =  (object) $array;

        }

        $dataDocument = array();
        $details =  (object) array('image' => $dataImage, 'document' => $dataDocument);

        array_push($data, $details);

        $details->appData = $this->prepareAppData($request, $data);

        return new SyncImageResource($details);

    }

    function returnErrorApi($request, $data, $message = ""){

        $status = $this->failedAppData($message);
        $emptyData = collect();
        $emptyData->appData = $this->prepareAppData($request, $data, $status);
        return new BaseResource($emptyData);
    }

    public function returnEmptyApi($request, $data, $message){

        $emptyData = collect();
        $emptyData->appData = $this->prepareAppData($request, $data, ["message" => $message]);

        return new BaseResource($emptyData);    
    }

    public function returnValidatorFails($value, $rules, $message, $request, $data){

        $validator = Validator::make($value, $rules, $message);
        
        if ($validator->fails()) {
            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }
    }

    function returnResultArray($result, $value, $message, $status, $action, $serverData = null){
        
        $resultArray = array(
                    "action"        => $action,
                    "ref_code"      => (isset($value["ref_code"]) ? $value["ref_code"] : $value["issue_id"]),
                    "message"       => $message, 
                );

        // INSERT SERVER ID
        if($status == 'success'){
            $server_arr = array("server_id" => $serverData == null ? "" : $serverData->id);
            $resultArray = array_merge($resultArray, $server_arr);
        }

        array_push($result[$status], $resultArray);

        return $result;
    }

    function groupResult($typeAlert, $action){

        $dataAlert["success"] = array_merge($typeAlert["success"],$action["success"]);
        $dataAlert["failed"] = array_merge($typeAlert["failed"],$action["failed"]);

        return $dataAlert;
    }
}

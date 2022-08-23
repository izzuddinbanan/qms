<?php

namespace App\Http\Controllers\Api\v1\Manage;

use File;
use Validator;
use Helper;
use Carbon\Carbon;
use App\Entity\Project;
use App\Entity\RoleUser;
use App\Entity\Language;
use App\Entity\Issue;
use App\Entity\FormVersion;
use App\Entity\CategoryProject;
use App\Entity\IssueProject;
use App\Entity\LocationPoint;
use App\Entity\GroupProject;
use App\Entity\SettingCategory;
use App\Entity\History;
use App\Entity\DrawingSet;
use App\Entity\DrawingPlan;
use App\Entity\Status;
use App\Entity\SettingIssue;
use App\Entity\User;
use App\Entity\GeneralStatus;
use App\Entity\SettingType;
use App\Entity\IssueImage;
use App\Entity\GroupContractor;
use App\Entity\GroupUser;
use App\Entity\SettingPriority;
use App\Entity\PriorityProject;
use App\Entity\ItemSubmitted;
use App\Entity\FormGroup;
use App\Entity\Submission;
use App\Entity\IssueFormSubmission;
use App\Supports\AppData;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use App\Http\Resources\IssueResource;
use App\Http\Resources\IssueCollection;
use App\Http\Resources\GeneralResource;
use App\Http\Resources\SettingCategoryResource;
use App\Http\Resources\SettingCategoryCollection;
use App\Http\Resources\SettingPriorityResource;
use App\Http\Resources\SettingPriorityCollection;
use App\Http\Resources\SettingTypeCollection;
use App\Http\Resources\HistoryIssueResource;
use App\Http\Resources\GroupResource;
use App\Http\Resources\HistoryIssueCollection;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Http\Resources\SettingGroupContractorCollection;
use App\Http\Resources\SettingGroupContractorResource;
use App\Http\Resources\StatusCollection;
use App\Http\Resources\PlanCollection;
use App\Http\Resources\PlanResource;
use App\Http\Resources\StatusResource;
use App\Http\Resources\GeneralStatusCollection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\v1\BaseApiController;
use App\Http\Controllers\Traits\PushNotification;
use App\Http\Controllers\Traits\IssueCountDetails;
use Intervention\Image\Facades\Image;

class IssueController extends BaseApiController
{
    use PushNotification, IssueCountDetails;
    use AppData;

    /**
     * @SWG\Post(
     *     path="/issue/addIssue",
     *     summary="To add/post issue **Image can only be add using postman",
     *     method="post",
     *     tags={"Issue"},
     *     description="This Api will post the new issue. **Image can only be add using postman",
     *     operationId="addIssue",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="plan_id",type="string",example="1"),
     *                      @SWG\Property(property="location_id",type="string",example="1"),
     *                      @SWG\Property(property="issue_setting_id",type="string",example="1"),
     *                      @SWG\Property(property="contractor_id",type="string",example="1"),
     *                      @SWG\Property(property="priority_id",type="string",example="1"),
     *                      @SWG\Property(property="due_by",type="string",example="2018-08-15"),
     *                      @SWG\Property(property="position_x",type="string",example="137"),
     *                      @SWG\Property(property="position_y",type="string",example="0"),
     *                      @SWG\Property(property="comment",type="string",example="This is a issue."),
     *               ),
     *         ),
     *      ),
     *      @SWG\Parameter(
     *          description="Issue Picture",
     *          in="formData",
     *          name="image",
     *          type="file",
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function addIssue(Request $request)
    {
        $data = $this->data;
        $user = $this->user;
        
        if($user->current_role == 7 ){
            $rules = [
                'plan_id'             => 'required',
                'location_id'         => 'required',
                'issue_setting_id'    => 'required',
                'position_x'          => 'required',
                'position_y'          => 'required',
            ];
        }
        else{
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
        }

        $message = [
            'data.plan_id.required'             => "Plan is required.",
            'data.location_id.required'         => "Location is required.",
            'data.contractor_id.required'       => "Contractor is required.",
            'data.issue_id.required'            => "Issue is required.",
            'data.priority_id.required'         => "Priority is required.",
            'data.due_by.required'              => "Date is required.",
            'data.position_x.required'          => "position x is required.",
            'data.position_x.required'          => "Position y required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);


        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        //if user is contractor and subcontractor
        if($user->current_role ==  5 || $user->current_role == 6 ){
            $status = $this->failedAppData('You are not allowed to do this action.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        //if user is inspector //now inspector can still create issue if the unit is handed over
        // if($user->current_role == 4){
        //     $plan = DrawingPlan::find($request->input('data.plan_id'));
        //     if($plan->user_id != null || $plan->user_id != ""){
        //         // print_r("not null, cannot make issue");
        //         $status = $this->failedAppData('You are not allowed to do this action. This unit is already handed over.');

        //         $emptyData = collect();
        //         $emptyData->appData = $this->prepareAppData($request, $data, $status);

        //         return new BaseResource($emptyData);
        //     }
        // }

        //if user is customer 
        if($user->current_role == 7){
            $user = \Auth::user();
            $plan = DrawingPlan::find($request->input('data.plan_id'));
            //get drawing set of the plan
            $drawing_set = DrawingSet::find($plan->drawing_set_id);
            //get the project of the plan 
            $project = Project::find($drawing_set->project_id);

            //if unit is not belong to the customer
            if($plan->user_id != $user->id){
                //check if the plcae is common area
                $drawingSet = DrawingSet::where('project_id', $project->id)->select('id')->get(); 

                $drawingPlan = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->select('id')->get();
                
                //drawing plan is not a common area || not within the same project
                if(DrawingPlan::where('id', $request->input('data.plan_id'))
                                ->whereIn('id', $drawingPlan)
                                ->whereIn('types', ['common'])
                                ->count()==0){
                    $status = $this->failedAppData('You are not allowed to do this action.');

                    $emptyData = collect();
                    $emptyData->appData = $this->prepareAppData($request, $data, $status);

                    return new BaseResource($emptyData);
                }
            }
        }

        if($user->current_role != 7)
        {
            $plan = DrawingPlan::find($request->input('data.plan_id'));
            if($plan->all_location_ready == 1 && $plan->hanover_status == "not handed over")
            {
                $status = $this->failedAppData('Issue cannot be created as all location is ready.');

                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }
        }

        if(!$location = LocationPoint::find($request->input('data.location_id'))){

            $status = $this->failedAppData('Location not found');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if(!$issueSetting = SettingIssue::find($request->input('data.issue_setting_id'))){

            $status = $this->failedAppData('Issue not found');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if($user->current_role != 7){
            if(!$priority = SettingPriority::find($request->input('data.priority_id'))){

                $status = $this->failedAppData('Priority not found');

                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }    
        }

        $plan = DrawingPlan::find($request->input('data.plan_id'));
        $handover_status = $plan->handover_status;
        $project = $plan->drawingSet->project;
        $contractor_count = GroupProject::where('project_id', $project->id)->whereNull('deleted_at')->count();

        if(!is_null($project->default_project_team_id) && !$project->default_project_team_id )
        {
            $assign_to = $project->default_project_team_id;
        }
        else
        {
            $project_team_count = RoleUser::where('project_id', $project->id)->where('role_id', 8)->count();
            if($project_team_count==0)
            {
                $status =$this->failedAppData('Project team is not available. Please contact administration to resolve this problem.');

                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }
        }

        if($plan->handover_status=="not handed over")
        {
            $status_id = 5; //WIP
        }
        else{
            if($user->current_role == 7) //owner
            {
                if($project->default_project_team_id != null)
                {
                    $assigned_to = $project->default_project_team_id;
                }
                else{
                    $assigned_to = RoleUser::where('project_id', $project->id)->where('role_id', 8)->first()->id;
                } 

                $status_id = 1; //lodged
            }
            //handed over and not owner
            else{
                //item submitted
                if(ItemSubmitted::where('drawing_plan_id', $plan->id)->count()>0)
                {
                    $status_id = 5;
                }
                //item not submitted
                else{
                    $status_id = 13;
                }
            }
        }


        $issue = Issue::create([
            'location_id'           => $request->input('data.location_id'),
            'setting_category_id'   => $issueSetting->category_id,
            'setting_type_id'       => $issueSetting->type_id,
            'setting_issue_id'      => $issueSetting->id,
            'priority_id'           => $user->current_role == 7 ? null : $request->input('data.priority_id'),
            'group_id'              => $user->current_role == 7 ? null : $request->input('data.contractor_id'),
            'due_by'                => $user->current_role == 7 ? null : $request->input('data.due_by'),
            'position_x'            => $request->input('data.position_x'),
            'position_y'            => $request->input('data.position_y'),
            'remarks'               => $request->input('data.comment') ? $request->input('data.comment') : "Owner created an issue.",
            'status_id'             => $status_id, //issue status in new status
            'created_by'            => $user->id,
            'on_behalf_owner'       => $request->input('data.on_behalf_owner') ?? null,
            'submit_source'         => 'QMS Hub',
            'handover_status'       => $handover_status,
            'assigned_to'           => $assigned_to ?? null,
        ]);

        
        ##generate unique reference for issue
        // $last = Issue::where('location_id', $request->input('data.location_id'))->count(); 

        $now = \Carbon\Carbon::now('Asia/Kuala_Lumpur');
        // $format = date("dmy", strtotime($now));
        // $unique_ref = $format . '-L' . $issue->location_id . 'I' . $issue->id . '-R' . $last; 
        $unique_ref = Helper::generateIssueReferenceByLocationID($request->input('data.location_id'));
        
        $issue->forcefill(['reference' => $unique_ref])->save();

        ##if inspector created the issue, auto fill inspector ID by it own id
        if($user->current_role == 4 || $user->current_role == 8){
            $issue->forcefill(['inspector_id' => $user->id])->save();
        }
        else if($user->current_role == 7 ){
            $issue->forcefill(['owner_id' => $user->id])->save();
        }

        $title =  $location->drawingPlan->drawingSet->project->name;
        $message =  'New issue reported('. $issue->reference .')';

        $appData = array('type' => 'new_issue','project_id' => $location->drawingPlan->drawingSet->project_id ,'plan_id' => $location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);
        $contractor = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)->select('user_id')->get();


        if($user->current_role != 7)
        {
            $this->FCMnotification($title, $message, $appData, $contractor, $issue->id, $user->id);
        }

        ##notification power user/ admin
        $admin_user_id = RoleUser::whereIn('role_id', [2, 3])->where('user_id', '!=', $issue->created_by)->where('project_id', $issue->location->drawingPlan->drawingSet->project_id)->select('user_id')->get();           
        $this->FCMnotification($title, $message, $appData, $admin_user_id, $issue->id, $user->id);

        $history = History::create([
            'user_id'               => $user->id,
            'issue_id'              => $issue->id,
            'status_id'             => $issue->status_id,
            'remarks'               => $request->input('data.comment'),
            'customer_view'         => null,
        ]);

        // if (!empty($request->file('data.image'))) {
        //     $defect_image = \App\Processors\SaveIssueProcessor::make($request->file('data.image'))->execute();

        //     $issue->forcefill(['image' => $defect_image])->save();
        //     $history->forcefill(['image' => $defect_image])->save();
        // }

        if (!is_null($request->file('data.image')) && count($request->file('data.image')) > 0) {

            foreach ($request->file('data.image') as $key => $value) {

                $image = \App\Processors\SaveIssueProcessor::make($value)->execute();

                $seq = null;
                
                if($key == 0){
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

        $location = LocationPoint::where('id', $request->input('data.location_id'))->with('drawingPlan.drawingSet')->first();

        $project_id = $location->drawingPlan->drawingSet->project_id;
        return $this->IssueDetails($user, $data, $request, $project_id);
    }

    /**
     * @SWG\Post(
     *     path="/issue/getGeneralSetting",
     *     summary="To retrieve setting issue info. Use at add issue form. get category,type,issue and priority.",
     *     method="POST",
     *     tags={"Issue"},
     *     description="Use this API to retrieve info that used in issue form.",
     *     operationId="getGeneralSetting",
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
    public function getGeneralSetting(Request $request)
    {

        $user = $this->user;
        $data = $this->data;

        // if(!$category_id = CategoryProject::where("project_id", $request->input('data.project_id'))->select('category_setting_id')->get()){
            
        //     $emptyData = collect();
        //     $emptyData->appData = $this->prepareAppData($request, $data);

        //     return new BaseResource($emptyData);
        // }

        $issue_project = IssueProject::where('project_id',$request->input('data.project_id'))->select('issue_setting_id')->get();

        if($user->current_role == 7){
            $issue = SettingIssue::whereIn('id', $issue_project)->where('unit_owner', true)->select('category_id')->get();
        }else {
            $issue = SettingIssue::whereIn('id', $issue_project)->select('category_id')->get();
        }

        $category = SettingCategory::whereIn('id', $issue)->get();

        foreach ($category as $key => $value) {
            
            // $checkCategory = CategoryProject::where('category_setting_id', $value["id"])->where('project_id', $request->input('data.project_id'))->first();


            if($user->language_id != 1){
            
                $value["data_lang"] = (array) json_decode($value["data_lang"]);
               
                $lang = Language::find($user->language_id);

                if(isset($value["data_lang"][$lang->abbreviation_name])){
                    if($value["data_lang"][$lang->abbreviation_name]->name != "")
                       $category[$key]["name"] = $value["data_lang"][$lang->abbreviation_name]->name;
                }
            }
            
            if($user->current_role == 7){

                $typeCheck = SettingIssue::whereIn('id', $issue_project)->where('category_id', $value["id"])->where('unit_owner', true)->select('type_id')->get();
                $type = SettingType::whereIn('id', $typeCheck)->where('unit_owner',true)->get();
            }else {
                $typeCheck = SettingIssue::whereIn('id', $issue_project)->where('category_id', $value["id"])->select('type_id')->get();
                $type = SettingType::whereIn('id', $typeCheck)->get();
            }

            foreach ($type as $typeKey => $typeValue) {

                if($user->current_role == 7){
                
                    $issueType = SettingIssue::whereIn('id', $issue_project)->where('type_id', $typeValue['id'])->where('unit_owner', true)->get();
                }else {
                    $issueType = SettingIssue::whereIn('id', $issue_project)->where('type_id', $typeValue['id'])->get();
                }
                foreach ($issueType as $keyIssueType => $valueIssueType) {

                    $group_id = IssueProject::where('project_id',$request->input('data.project_id'))->where('issue_setting_id', $valueIssueType["id"])->first();

                    $issueType[$keyIssueType]["group_id"] = $group_id["group_id"];
                }

                $type[$typeKey]["issue"] = $issueType;
            }

            $category[$key]["type"] = new SettingTypeCollection($type);

        }


        $project = Project::find($request->input('data.project_id'));
        
        // $priority = SettingPriority::where('client_id', $project->client_id)->get();
        $priority = PriorityProject::where('project_id', $project->id)->get();

        $contractor = GroupProject::where('project_id', $project->id)->get();
        $newIssueStatus = Status::whereIn('id', [3,6])->get();
        $ContractorIssue = Status::whereIn('id', [3,6])->get();
        $startStatus = Status::whereIn('id', [5])->get();
        $notme = Status::whereIn('id', [6])->get();
        $location_status = GeneralStatus::whereIn('id', [1,2,3])->get();

        $userSubCon = RoleUser::where('role_id', 6)->where('client_id', $project->client_id)->select('user_id')->get();
        $subcon = User::whereIn('id', $userSubCon)->get();

        $dummy = User::where('id', 0)->get();

        $category = (new SettingCategoryCollection($category));
        $priority = (new SettingPriorityCollection($priority));
        $groupCon = (new SettingGroupContractorCollection($contractor));
        $new   = (new StatusCollection($newIssueStatus));
        $startWorkStatus   = (new StatusCollection($startStatus));
        $notmeStatus   = (new StatusCollection($notme));
        $data_dummy   = (new UserCollection($subcon));
        $locationStatus   = (new GeneralStatusCollection($location_status));

        $details =  (object) array('category'       => $category, 
                                    'priority'      => $priority, 
                                    'contractor'    => $groupCon,
                                    'new'           => $new,
                                    'start_work'    => $startWorkStatus,
                                    'not_me'       => $notmeStatus,
                                    'subContractor' => $data_dummy,
                                    'location_status' => $locationStatus);
        array_push($data, $details);

        $details->appData = $this->prepareAppData($request, $data);

        return new GeneralResource($details);
    }

    /**
     * @SWG\Post(
     *     path="/issue/addInfo",
     *     summary="To add/post Info of issue **Image can only be add using postman",
     *     method="post",
     *     tags={"Issue"},
     *     description="This Api will post the new Info of issue. ",
     *     operationId="addInfo",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="issue_id",type="string",example="1"),
     *                      @SWG\Property(property="remarks",type="string",example=""),
     *               ),
     *         ),
     *      ),
     *      @SWG\Parameter(
     *          description="Picture",
     *          in="formData",
     *          name="image",
     *          type="file",
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function addInfo(Request $request)
    {
        $data = $this->data;
        $user = $this->user;
  
        $rules = [
            'issue_id'         => 'required',
        ];

        $message = [
            'data.issue_id.required'             => "Issue is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);


        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }
        
        if(!$issue = Issue::find($request->input('data.issue_id'))){

            $status = $this->failedAppData('Issue not found');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        $history = History::create([
            'user_id'               => $user->id,
            'issue_id'              => $request->input('data.issue_id'),
            'status_id'             => $issue->status_id,
            'remarks'               => $request->input('data.remarks'),
            'customer_view'         => null,
        ]);


        /*
         *function for multiple image 
        **/

        if (!is_null($request->file('data.image')) && count($request->file('data.image')) > 0) {
            foreach ($request->file('data.image') as $key => $value) {

                $image = \App\Processors\SaveIssueProcessor::make($value)->execute();

                $history->images()->create([
                    'image' => $image,
                ]);
            }
        }

        $title =  $issue->location->drawingPlan->drawingSet->project->name;
        $message =  'New info for issue('. $issue->reference .')';
        
        ##send to all contractor
        $appData = array('type' => 'new_info','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);
        $contractor = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)->where('user_id', '!=', $history->user_id)->select('user_id')->get();
        $this->FCMnotification($title, $message, $appData, $contractor, $issue->id, $history->user_id);

        ##send to inspector
        if($history->user_id != $issue->inspector_id){
            $inspector = Issue::where('id', $issue->id)->select('inspector_id as user_id')->get();
            $this->FCMnotification($title, $message, $appData, $inspector, $issue->id, $history->user_id); 
        }


        $location = LocationPoint::where('id', $issue->location_id)->with('drawingPlan.drawingSet')->first();

        $project_id = $location->drawingPlan->drawingSet->project_id;
        return $this->IssueDetails($user, $data, $request, $project_id);

    }

    /**
     * @SWG\Post(
     *     path="/issue/updateIssue",
     *     summary="To update details and position of Issue",
     *     method="post",
     *     tags={"Issue"},
     *     description="This Api will update details of issue.",
     *     operationId="updateIssue",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="issue_id",type="string",example="1"),
     *                      @SWG\Property(property="position_x",type="string",example="137"),
     *                      @SWG\Property(property="position_y",type="string",example="0"),
     *                      @SWG\Property(property="issue_setting_id",type="string",example=""),
     *                      @SWG\Property(property="contractor_id",type="string",example=""),
     *                      @SWG\Property(property="priority_id",type="string",example=""),
     *                      @SWG\Property(property="due_by",type="string",example="2018-08-15"),
     *                      @SWG\Property(property="comment",type="string",example=""),
     *               ),
     *         ),
     *      ),
     *      @SWG\Parameter(
     *          description="Issue Picture",
     *          in="formData",
     *          name="image",
     *          type="file",
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function updateIssue(Request $request)
    {
        $data = $this->data;
        $user = $this->user;

        $rules = [
            'issue_id'      => 'required',
        ];

        $message = [
            'issue_id.required'     => "Issue ID is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);


        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if(!$issue = Issue::find($request->input('data.issue_id'))){

            $status = $this->failedAppData('Issue not found');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if($user->current_role == 7){
            $issue = Issue::find($request->input('data.issue_id'));

            if($issue->owner_id != $user->id || $issue->status_id != 1){
                $status = $this->failedAppData('You are not allowed to do this action.');

                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData); 
            }
        }

        ##update issue details only - not position
        if($request->input('data.position_x') == null){

            if($request->input('data.issue_setting_id')){

                if(!$issueSetting = SettingIssue::find($request->input('data.issue_setting_id'))){

                    $status = $this->failedAppData('Issue not found');

                    $emptyData = collect();
                    $emptyData->appData = $this->prepareAppData($request, $data, $status);

                    return new BaseResource($emptyData);
                }

                $old_issue = Issue::where('id', $request->input('data.issue_id'))->with('category')->with('type')->with('issue')->with('contractor')->with('priority')->first();

                if($user->current_role == 7){
                    $issue->update([
                        // 'location_id'           => $request->input('data.location_id'),
                        'setting_category_id'   => $issueSetting->category_id,
                        'setting_type_id'       => $issueSetting->type_id,
                        'setting_issue_id'      => $issueSetting->id,
                        'remarks'               => $request->input('data.comment'),
                    ]);

                    $history = History::create([
                        'user_id'               => $user->id,
                        'issue_id'              => $issue->id,
                        'status_id'             => $issue->status_id,
                        'remarks'               => "owner updated issue details",
                        'customer_view'         => 1,
                    ]);   
                }
                else{
                    $issue->update([
                        // 'location_id'           => $request->input('data.location_id'),
                        'setting_category_id'   => $issueSetting->category_id,
                        'setting_type_id'       => $issueSetting->type_id,
                        'setting_issue_id'      => $issueSetting->id,
                        'priority_id'           => $request->input('data.priority_id'),//
                        'group_id'              => $request->input('data.contractor_id'),//
                        'due_by'                => $request->input('data.due_by'),//
                        'remarks'               => $request->input('data.comment'),
                    ]);

                    $history = History::create([
                        'user_id'               => $user->id,
                        'issue_id'              => $issue->id,
                        'status_id'             => $issue->status_id,
                        'remarks'               => "updated issue details",
                    ]);   
                }
                

            }else{

                // reassign contractor
                $issue->update([
                    'group_id'              => $request->input('data.contractor_id'),
                    'status_id'             => 2,
                ]);


                $history = History::create([
                    'user_id'               => $user->id,
                    'issue_id'              => $issue->id,
                    'status_id'             => $issue->status_id,
                    'remarks'               => "Reassign issue to a new contractor",
                ]);

            }


            // if (!empty($request->file('data.image'))) {
            //     $defect_image = \App\Processors\SaveIssueProcessor::make($request->file('data.image'))->execute();

            //     $issue->forcefill(['image' => $defect_image])->save();
            // }
            
            if (!is_null($request->input('data.remove')) && count($request->input('data.remove')) > 0) {
                foreach ($request->input('data.remove') as $key => $value) {
                    $issue_image = IssueImage::find($value);
                    $issue_image->delete();
               }
            }

            if (!is_null($request->file('data.new')) && count($request->file('data.new')) > 0) {
                foreach ($request->file('data.new') as $key => $value) {

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


            $updated_issue = Issue::where('id', $request->input('data.issue_id'))->with('category')->with('type')->with('issue')->with('priority')->with('contractor')->first();
        }else{

            if($user->current_role != 8 && $user->current_role != 7){

                $status = $this->failedAppData('You are not allowed to do this action.');

                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }
            
            $issue->update([
                'position_x'            => $request->input('data.position_x'),
                'position_y'            => $request->input('data.position_y'),
            ]);
        }

        $location = LocationPoint::where('id', $issue->location_id)->with('drawingPlan.drawingSet')->first();

        $project_id = $location->drawingPlan->drawingSet->project_id;
        return $this->IssueDetails($user, $data, $request, $project_id);
    }


    /**
     * @SWG\Post(
     *     path="/issue/acceptIssue",
     *     summary="To accept/reject issue assign by power user/admin",
     *     method="post",
     *     tags={"Issue"},
     *     description="This Api will use for accept or reject issue. accept = 3 || no = 6",
     *     operationId="acceptIssue",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="issue_id",type="string",example="1"),
     *                      @SWG\Property(property="status_id",type="string",example="1"),
     *                      @SWG\Property(property="comment",type="string",example=""),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function acceptIssue(Request $request)
    {

        $data = $this->data;
        $user = $this->user;    

        $rules = [
            'issue_id'             => 'required',
            'status_id'            => 'required',
        ];

        $message = [
            'issue_id.required'            => "Issue is required.",
            'status_id.required'           => "Status is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);


        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if(!$issue = Issue::find($request->input('data.issue_id'))){

            $status = $this->failedAppData('Issue not found');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        //inspector accept issue
        if($request->input('data.status_id') == 2){
            $issue = Issue::find($request->input('data.issue_id'));
            $issue_project_id = $issue->location->drawingPlan->drawingSet->project_id;

            //not inspector or not inspector of the project
            if($user->current_role != 4 || RoleUser::where('project_id', $issue_project_id)->where('role_id', 4)->count()==0 ){
                $status = $this->failedAppData('You are not allowed to do this action.');

                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }
            else{
                $issue->update([
                    'status_id'     => 2,
                    'inspector_id'  => $user->id,   
                    'group_id'      => $request->input('data.contractor_id'),   
                    'priority_id'   => $request->input('data.priority_id'),
                    'due_by'        => $request->input('data.due_by'),
                ]);

                History::create([
                    'user_id'           => $user->id,
                    'issue_id'          => $request->input('data.issue_id'),
                    'status_id'         => $issue->status_id,
                    'remarks'           => $request->input('data.comment') === "" ? 'Inspector accept the raise issue' : $request->input('data.comment'),
                    'customer_view'     => null,
                ]);

                $title =  $issue->location->drawingPlan->drawingSet->project->name;
                $message =  'New issue reported('. $issue->reference .')';

                $appData = array('type' => 'new_issue','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);
                $contractor = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)->select('user_id')->get();
                $this->FCMnotification($title, $message, $appData, $contractor, $issue->id, $user->id);        

            }

        }
        else if($request->input('data.status_id') == 3){

            $issue->update([
                'status_id'            => 3,
            ]);

            History::create([
                'user_id'       => $user->id,
                'issue_id'      => $request->input('data.issue_id'),
                'status_id'     => $issue->status_id,
                'remarks'       => $request->input('data.comment') === "" ? 'Accept the raise issue' : $request->input('data.comment'),
            ]);

            $message =  'Issue('. $issue->reference .') is changed to "Pending Start" status.';

            $appData = array('type' => 'accept_issue','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);


        }
        else if($request->input('data.status_id') == 6){

            $issue->update([
                'status_id'            => 7,
                'group_id'             => null,
            ]);

            History::create([
                'user_id'       => $user->id,
                'issue_id'      => $request->input('data.issue_id'),
                'status_id'     => $issue->status_id,
                'remarks'       => $request->input('data.comment') === "" ? 'Reject the raise issue. Not my issue' : $request->input('data.comment') ,
            ]);

            // $message =  'Issue('. $issue->reference .') is changed to "Reassign" status.';
            $message =  'Issue('. $issue->reference .') needs to reassign.';
            $appData = array('type' => 'reject_issue','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);

        }else{

            $status = $this->failedAppData('Status not found');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        $title =  $issue->location->drawingPlan->drawingSet->project->name;

        $inspector = User::where('id', $issue->inspector_id)->select('id as user_id')->get();
        $this->FCMnotification($title, $message, $appData, $inspector, $issue->id, $user->id);

        $contractor = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)->select('user_id')->get();
        $this->FCMnotification($title, $message, $appData, $contractor, $issue->id, $user->id);

        $location = LocationPoint::where('id', $issue->location_id)->with('drawingPlan.drawingSet')->first();

        $project_id = $location->drawingPlan->drawingSet->project_id;
        return $this->IssueDetails($user, $data, $request, $project_id);
    }


    /**
     * @SWG\Post(
     *     path="/issue/startWork",
     *     summary="API to use update status issue (start work)",
     *     method="post",
     *     tags={"Issue"},
     *     description="This Api will use for update status issue -- subcontractor not implement yet **just dummy data",
     *     operationId="startWork",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="issue_id",type="string",example=""),
     *                      @SWG\Property(property="subcon_id",type="string",example=""),
     *                      @SWG\Property(property="status_id",type="string",example=""),
     *                      @SWG\Property(property="start_date",type="string",example="2018-08-15"),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function startWork(Request $request)
    {

        $data = $this->data;
        $user = $this->user;    

        $rules = [
            'status_id'            => 'required',
            'start_date'            => 'required',
        ];

        $message = [
            'start_date.required'            => "Start date is required.",
            'status_id.required'             => "Status is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);


        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }


        if(!$issue = Issue::find($request->input('data.issue_id'))){

            $status = $this->failedAppData('Issue not found.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if($request->input('data.status_id') == 5){

            $message =  'Issue('. $issue->reference .') is changed to "W.I.P" status.';
            $remark = 'Start to fix the issue.';

            $startDate = $request->input('data.start_date');
            $startDate = explode("/",$startDate);
            $formatDate = $startDate[2] . '-' . $startDate[1] . '-' . $startDate[0];
            
            $issue->update([
                
                'start_date'           => $formatDate,
            ]);
        }

        if($request->input('data.status_id') == 9){
            $message =  'Issue('. $issue->reference .') is changed to "Redo" status.';
            $remark = 'Redo fix the issue.';
        }

        $issue->update([
            'status_id'            => $request->input('data.status_id'),
        ]);

        History::create([
            'user_id'       => $user->id,
            'issue_id'      => $request->input('data.issue_id'),
            'status_id'     => $issue->status_id,
            'remarks'       => $remark,
        ]);
        

        $title =  $issue->location->drawingPlan->drawingSet->project->name;
        $appData = array('type' => 'start_fix','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);



        $inspector = User::where('id', $issue->inspector_id)->select('id as user_id')->get();
        $this->FCMnotification($title, $message, $appData, $inspector, $issue->id, $user->id);

        $contractor = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)->select('user_id')->get();
        $this->FCMnotification($title, $message, $appData, $contractor, $issue->id, $user->id);

        $location = LocationPoint::where('id', $issue->location_id)->with('drawingPlan.drawingSet')->first();

        $project_id = $location->drawingPlan->drawingSet->project_id;
        return $this->IssueDetails($user, $data, $request, $project_id);
    }


    /**
     * @SWG\Post(
     *     path="/issue/closeIssue",
     *     summary="To close issue **Image can only be add using postman",
     *     method="post",
     *     tags={"Issue"},
     *     description="This Api will update status issue to complete(close issue). ",
     *     operationId="closeIssue",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="issue_id",type="string",example="1"),
     *                      @SWG\Property(property="remarks",type="string",example="Already done fix the issue."),
     *               ),
     *         ),
     *      ),
     *      @SWG\Parameter(
     *          description="Picture",
     *          in="formData",
     *          name="image",
     *          type="file",
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function closeIssue(Request $request)
    {
        $data = $this->data;
        $user = $this->user;

        $rules = [
            'issue_id'         => 'required',
        ];

        $message = [
            'data.issue_id.required'             => "Issue is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);

        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }
        
        if(!$issue = Issue::find($request->input('data.issue_id'))){

            $status = $this->failedAppData('Issue not found');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        $title =  $issue->location->drawingPlan->drawingSet->project->name;

        if($user->current_role != 5 && $user->current_role != 4 && $user->current_role != 8)
        {
            $status = $this->failedAppData('Not authorize to access this feature.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        //contractor
        if($user->current_role == 5){
            // issue is already completed
            if($issue->status_id != 5 && $issue->status_id != 9)
            {
                $status = $this->failedAppData('Not authorize to access this feature.');

                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }

            $issue->update(['status_id' => 8]);

            $history = History::create([
                'user_id'       => $user->id,
                'issue_id'      => $request->input('data.issue_id'),
                'status_id'     => 8,
                'remarks'       => $request->input('data.remarks'),
            ]);

            $appData = array('type' => 'complete_issue','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);
        
            $message =  'Issue('. $issue->reference .') is changed to "Completed" status.';

            $inspector = User::where('id', $issue->inspector_id)->select('id as user_id')->get();
            $this->FCMnotification($title, $message, $appData, $inspector, $issue->id, $user->id);

            $contractor = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)->select('user_id')->get();
            $this->FCMnotification($title, $message, $appData, $contractor, $issue->id, $user->id);


            if (!is_null($request->file('data.image')) && count($request->file('data.image')) > 0) {
                foreach ($request->file('data.image') as $key => $value) {

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
        }
        //inspector or project team
        else if($user->current_role==4 || $user->current_role==8){
            if($issue->status_id != 5 && $issue->status_id != 9 && $issue->status_id != 8 && $issue->status_id != 11)
            {
                $status = $this->failedAppData('Not authorize to access this feature.');

                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }
            //define status
            if($issue->status_id == 8 && $issue->handover_status == "not handed over")
            {
                $status_id = 10; //close internal
                
                $message =  'Issue('. $issue->reference .') is changed to "Close Internal" status.';   

                $appData = array('type' => 'close_internal','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);

                $remark = "Issue status changed to Close Internal.";
            }
            else if($issue->status_id == 8 && $issue->handover_status == "handed over")
            {
                $status_id = 10; //pending owner acceptance

                $message =  'Issue('. $issue->reference .') is changed to "Close Internal" status.';    

                $appData = array('type' => 'close_internal','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);

                $remark = "Issue status changed to Close Internal.";
            }
            else if($issue->status_id == 11 && $issue->handover_status == "handed over")
            {
                $status_id = 11; //pending owner acceptance

                $message =  'Issue('. $issue->reference .') is changed to "Pending Owner Acceptance" status.';   

                $appData = array('type' => 'pending_owner_acceptance','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);

                $remark = "Issue status changed to Pending Owner Acceptance.";    
            }
            else if($issue->status_id == 5 || $issue->status_id == 9)
            {
                $status_id = 8; //complete

                $message =  'Issue('. $issue->reference .') is changed to "Completed" status.';   

                $appData = array('type' => 'completed','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);

                $remark = "Issue status changed to Completed.";
            }
            else if($issue->status_id == 11)
            {
                $status_id = 14; //complete

                $message =  'Issue('. $issue->reference .') is changed to "Close External" status.';   

                $appData = array('type' => 'close_external','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);

                $remark = "Issue status changed to Close External.";   
            }
            
            $issue->update([
                'status_id' => $status_id
            ]);

            $history = History::create([
                'user_id'               => $user->id,
                'issue_id'              => $request->input('data.issue_id'),
                'status_id'             => $issue->status_id,
                'remarks'               => $request->input('data.remarks') ?? $remark,
                'customer_view'         => null,
            ]); 

            if (!is_null($request->file('data.image')) && count($request->file('data.image')) > 0) {
                foreach ($request->file('data.image') as $key => $value) {

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
                
            $contractor = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)->select('user_id')->get();
            $this->FCMnotification($title, $message, $appData, $contractor, $issue->id, $issue->created_by);
                
            if (!is_null($request->file('data.image')) && count($request->file('data.image')) > 0) {
                foreach ($request->file('data.image') as $key => $value) {

                    $image = \App\Processors\SaveIssueProcessor::make($value)->execute();

                    $history->images()->create([
                        'image' => $image,
                    ]);
                }
            }
        }

        $location = LocationPoint::where('id', $issue->location_id)->with('drawingPlan.drawingSet')->first();

        $project_id = $location->drawingPlan->drawingSet->project_id;
        return $this->IssueDetails($user, $data, $request, $project_id);
    }


    /**
     * @SWG\Post(
     *     path="/issue/voidIssue",
     *     summary="API to void/cancel the issue / wrong issue ",
     *     method="post",
     *     tags={"Issue"},
     *     description="This Api will use to void the issue",
     *     operationId="voidIssue",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="issue_id",type="string",example=""),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function voidIssue(Request $request)
    {

        $data = $this->data;
        $user = $this->user;    

        $rules = [
            'issue_id'            => 'required',
        ];

        $message = [
            'issue_id.required'            => "Issue ID is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);


        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }


        if(!$issue = Issue::find($request->input('data.issue_id'))){

            $status = $this->failedAppData('Issue not found.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if($user->current_role != 8){
            $issue = Issue::find($request->input('data.issue_id'));

            if($issue->owner_id != $user->id || $issue->status_id == 1){
                $status = $this->failedAppData('You are not allowed to do this action.');

                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }
        }

        $issue->forcefill([
            'void_by'           => $user->id,
            'status_id'         => 4,
        ])->save();

        History::create([
            'user_id'           => $user->id,
            'issue_id'          => $request->input('data.issue_id'),
            'status_id'         => 4,
            'remarks'           => 'Void the issue.',
            'customer_view'     => null,
        ]);

        $location = LocationPoint::where('id', $issue->location_id)->with('drawingPlan.drawingSet')->first();

        $project_id = $location->drawingPlan->drawingSet->project_id;
        return $this->IssueDetails($user, $data, $request, $project_id);
    }


    /**
     * @SWG\Post(
     *     path="/issue/mergeIssue",
     *     summary="To merge/join the issue",
     *     method="post",
     *     tags={"Issue"},
     *     description="This Api will use to join the issue in 1 location",
     *     operationId="mergeIssue",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="issue_id_move",type="string",example="1"),
     *                      @SWG\Property(property="issue_id_place",type="string",example="5"),

     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function mergeIssue(Request $request)
    {
        $data = $this->data;
        $user = $this->user;
  
        $rules = [
            'issue_id_move'             => 'required',
            'issue_id_place'            => 'required',
        ];

        $message = [
            'data.issue_id_move.required'          => "Issue is required.",
            'data.issue_id_place.required'         => "Issue is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);

        if($user->current_role != 8){

            $status = $this->failedAppData('You are not allowed to do this action.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if(!$issue_move  = Issue::find($request->input('data.issue_id_move'))){

            $status = $this->failedAppData('Issue not found.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);

        }


        if(!$issue_place = Issue::find($request->input('data.issue_id_place'))){

            $status = $this->failedAppData('Issue not found.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if($issue_move->location_id != $issue_place->location_id){

            $status = $this->failedAppData('Merge issue can only available for same location issue only.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        $issue_move->update([
            'merge_issue_id'    => $request->input('data.issue_id_place'),
            'position_x'        => $issue_place->position_x,
            'position_y'        => $issue_place->position_y,
        ]);

        $location = LocationPoint::where('id', $issue_move->location_id)->with('drawingPlan.drawingSet')->first();

        $project_id = $location->drawingPlan->drawingSet->project_id;
        return $this->IssueDetails($user, $data, $request, $project_id);
    }


    /**
     * @SWG\Post(
     *     path="/issue/splitIssue",
     *     summary="To split the issue",
     *     method="post",
     *     tags={"Issue"},
     *     description="This Api will use to split the issue in 1 location",
     *     operationId="splitIssue",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="issue_id",type="string",example="1"),
     *                      @SWG\Property(property="position_x",type="string",example="1"),
     *                      @SWG\Property(property="position_y",type="string",example="1"),

     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function splitIssue(Request $request)
    {
        $data = $this->data;
        $user = $this->user;
  
        $rules = [
            'issue_id'              => 'required',
            'position_x'            => 'required',
            'position_x'            => 'required',
        ];

        $message = [
            'data.issue_id.required'      => "Issue is required.",
        ];

        if($user->current_role != 8){

            $status = $this->failedAppData('You are not allowed to do this action.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        $validator = Validator::make($request->input('data'), $rules, $message);


        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if(!$issue  = Issue::find($request->input('data.issue_id'))){

            $status = $this->failedAppData('Issue not found.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if($merge_issue = Issue::where('merge_issue_id', $request->input('data.issue_id'))->first()){

            $merge_issue->update([
                'position_x'        => $issue->position_x,
                'position_y'        => $issue->position_y,
                'merge_issue_id'    => null,
            ]);

            Issue::where('merge_issue_id', $request->input('data.issue_id'))->update(['merge_issue_id' => $merge_issue->id]);
        }

        $issue->update([
            'merge_issue_id'    => null,
            'position_x'        => $request->input('data.position_x'),
            'position_y'        => $request->input('data.position_y'),
        ]);

        $location = LocationPoint::where('id', $issue->location_id)->with('drawingPlan.drawingSet')->first();

        $project_id = $location->drawingPlan->drawingSet->project_id;
        return $this->IssueDetails($user, $data, $request, $project_id);
    }


    /**
     * @SWG\Post(
     *     path="/issue/assignIssue",
     *     summary="To assign issue",
     *     method="post",
     *     tags={"Issue"},
     *     description="This Api will assign issue status to contractor, inspector or project team. ",
     *     operationId="assignIssue",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="issue_id",type="string",example="1"),
     *                      @SWG\Property(property="user_id",type="string",example="1"),
     *                      @SWG\Property(property="group_id",type="string",example="1"),
     *                      @SWG\Property(property="need_further_inspection",type="string",example="yes"),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function assignIssue(Request $request)
    {
        $data = $this->data;
        $user = $this->user;
  
        $rules = [
            'issue_id'                  => 'required', 
            'need_further_inspection'   => 'required',        
        ];

        $message = [
            'data.issue_id.required'            => "Issue is required.",
            'data.need_further_inspection'      => "Further inspection status is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);

        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }
        
        if(!$issue = Issue::find($request->input('data.issue_id'))){

            $status = $this->failedAppData('Issue not found');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if($issue->assigned_to != $user->id)
        {
            $status = $this->failedAppData('Not authorize to access this feature.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);   
        }

        $title =  $issue->location->drawingPlan->drawingSet->project->name;

        $project = $issue->location->drawingPlan->drawingSet->project;

        if($request->input('data.need_further_inspection') == "yes")
        {
            $user_count = RoleUser::where('project_id', $project->id)->where('user_id', $request->input('data.user_id'))->whereIn('role_id', [4,8])->count();
            //check if user is inside the project && role is inspector or project_team
            if(is_null($request->input('data.user_id')) || !$request->input('data.user_id') || $user_count == 0)
            {
                $status = $this->failedAppData('User not found.');

                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }   
        }
        else if(!is_null($request->input('data.need_further_inspection')) || !$request->input('data.need_further_inspection') || $request->input('data.need_further_inspection')!="yes")
        {
            if(is_null($request->input('data.group_id')) || !$request->input('data.group_id'))
            {
                $status = $this->failedAppData('Contractor not found.');

                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }
        }
        

        $user_role = RoleUser::where('project_id', $project->id)->where('user_id', $request->input('data.user_id'))->first();

        if($issue->assigned_count != 0 && is_null($request->input('data.group_id')))
        {
           $status = $this->failedAppData('This issue cannot be assigned.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData); 
        }

        $appData = array('type' => 'assign_issue','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);

        //contractor
        if($request->input('data.need_further_inspection')!="yes")
        {
            $drawing_plan = $issue->location->drawingPlan;
            if(ItemSubmitted::where('drawing_plan_id', $drawing_plan->id)->count()>0)
            {
                $status_id = 5;
            }
            else{
                $status_id = 13;
            }

            $issue->update([
                'group_id'          => $request->input('data.group_id'),
                'status_id'         => $status_id,
                'assigned_count'    => 1,
            ]);

            $history = History::create([
                'user_id'               => $user->id,
                'issue_id'              => $request->input('data.issue_id'),
                'status_id'             => $issue->status_id,
                'remarks'               => 'Issue assigned to contractor',
                'customer_view'         => null, 
            ]);

            $message = 'New issue assigned ('. $issue->reference .')';

            //send push notification to contractor
            $contractor = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)->select('user_id')->get();
            $this->FCMnotification($title, $message, $appData, $contractor, $issue->id, $user->id);
        }
        //inspector or project team
        else 
        {
            $message =  'New issue assigned ('. $issue->reference .')';

            $issue->update([
                'assigned_to'       => $request->input('data.user_id'),
                'assigned_count'    => 1,
            ]);

            //inspector
            if($user_role->role_id == 4)
            {
                $history = History::create([
                    'user_id'               => $user->id,
                    'issue_id'              => $request->input('data.issue_id'),
                    'status_id'             => $issue->status_id,
                    'remarks'               => 'Issue assigned to inspector',
                    'customer_view'         => null, 
                ]);

                $message =  'New issue assigned ('. $issue->reference .')';
                //send push notification to inspector or project team
                $inspector = Issue::where('id', $issue->id)->select('inspector_id as user_id')->get();
                $this->FCMnotification($title, $message, $appData, $inspector, $issue->id, $history->user_id); 
            }
            //project team
            else if($user_role->role_id ==8)
            {
                $history = History::create([
                    'user_id'               => $user->id,
                    'issue_id'              => $request->input('data.issue_id'),
                    'status_id'             => $issue->status_id,
                    'remarks'               => 'Issue assigned to project team',
                    'customer_view'         => null, 
                ]);

                $message =  'New issue assigned ('. $issue->reference .')';

                //send push notification to inspector or project team
                $project_team = Issue::where('id', $issue->id)->select('assigned_to as user_id')->get();
                $this->FCMnotification($title, $message, $appData, $project_team, $issue->id, $history->user_id); 
            }
        }

        $location = LocationPoint::where('id', $issue->location_id)->with('drawingPlan.drawingSet')->first();

        $project_id = $location->drawingPlan->drawingSet->project_id;
        return $this->IssueDetails($user, $data, $request, $project_id);
    }

    /**
     * @SWG\Post(
     *     path="/issue/declineIssue",
     *     summary="To decline issue **Image can only be add using postman",
     *     method="post",
     *     tags={"Issue"},
     *     description="This Api will change issue status to decline(decline issue). ",
     *     operationId="declineIssue",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="issue_id",type="string",example="1"),
     *                      @SWG\Property(property="comment",type="string",example="Decline this issue."),
     *               ),
     *         ),
     *      ),
     *      @SWG\Parameter(
     *          description="Picture",
     *          in="formData",
     *          name="image",
     *          type="file",
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function declineIssue(Request $request)
    {
        $data = $this->data;
        $user = $this->user;
  
        $rules = [
            'issue_id'      => 'required',     
        ];

        $message = [
            'data.issue_id.required'            => "Issue is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);


        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }
        
        if($user->current_role != 4 && $user->current_role != 8)
        {
            $status = $this->failedAppData('Not authorize to acces this function.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }   

        if(!$issue = Issue::find($request->input('data.issue_id'))){

            $status = $this->failedAppData('Issue not found');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        //if status not lodged
        if($issue->status_id != 1)
        {
            $status = $this->failedAppData('Issue cannot be declined');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        
        $appData = array('type' => 'decline_issue','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);
        
        $message =  'Issue('. $issue->reference .') is being declined.';

        $history = History::create([
            'user_id'               => $user->id,
            'issue_id'              => $request->input('data.issue_id'),
            'status_id'             => $issue->status_id,
            'remarks'               => $request->input('data.comment') ?? 'Issue has being declined.',
            'customer_view'         => null,
        ]);

        //update issues
        $issue->update(['status_id' => 15]);

        if (!is_null($request->file('data.image')) && count($request->file('data.image')) > 0) {
            foreach ($request->file('data.image') as $key => $value) {

                $image = \App\Processors\SaveIssueProcessor::make($value)->execute();
                
                $issue->images()->create([
                    'image'     => $image,
                    // 'type'      => 2, //Question: how the image type defined?
                ]);

                $history->images()->create([
                    'image' => $image,
                ]);
            }
        }

        //notify unit owner
        $title =  $issue->location->drawingPlan->drawingSet->project->name;
        $drawing_plan = $issue->location->drawingPlan;
        $unit_owner = User::where('id', $drawing_plan->user_id)->select('id as user_id')->get();
        $this->FCMnotification($title, $message, $appData, $unit_owner, $issue->id, $user->id);

        $location = LocationPoint::where('id', $issue->location_id)->with('drawingPlan.drawingSet')->first();

        $project_id = $location->drawingPlan->drawingSet->project_id;
        return $this->IssueDetails($user, $data, $request, $project_id);
    }

    /**
     * @SWG\Post(
     *     path="/issue/redoIssue",
     *     summary="To redo issue **Image can only be add using postman",
     *     method="post",
     *     tags={"Issue"},
     *     description="This Api will change issue status to redo(redo issue). ",
     *     operationId="redoIssue",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="issue_id",type="string",example="1"),
     *                      @SWG\Property(property="remarks",type="string",example="remarks"),
     *               ),
     *         ),
     *      ),
     *      @SWG\Parameter(
     *          description="Picture",
     *          in="formData",
     *          name="image",
     *          type="file",
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function redoIssue(Request $request)
    {
        $data = $this->data;
        $user = $this->user;
  
        $rules = [
            'issue_id'      => 'required',     
        ];

        $message = [
            'data.issue_id.required'            => "Issue is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);


        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if($user->current_role != 4 && $user->current_role != 8)
        {   
            $status = $this->failedAppData('Not authorize to access this function.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if(!$issue = Issue::find($request->input('data.issue_id'))){

            $status = $this->failedAppData('Issue not found');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }
        
        if($issue->status_id != 8 && $issue->status_id != 11)
        {
            $status = $this->failedAppData('Issue cannot be asked for redo.');

            $emptyData= collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

    
        $issue = Issue::find($issue['id']);
        
        $issue->update(['status_id' => 9]);

        $project = $issue->location->drawingPlan->drawingSet->project;

        $history = History::create([
            'user_id'               => $user->id,
            'issue_id'              => $issue->id,
            'status_id'             => 9,
            'remarks'               => $request->input('data.remarks') ?? 'Issue status changed to redo.',
            'customer_view'         => null,
        ]);

        $title =  $issue->location->drawingPlan->drawingSet->project->name;
        $message =  'Issue('. $issue->reference .') changed for redo.';
        $appData = array('type' => 'redo','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);

        $contractor = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)->select('user_id')->get();
        $this->FCMnotification($title, $message, $appData, $contractor, $issue->id, $user->id);


        if (!is_null($request->file('data.image')) && count($request->file('data.image')) > 0) {
            foreach ($request->file('data.image') as $key => $value) {

                $image = \App\Processors\SaveIssueProcessor::make($value)->execute();
                
                $issue->images()->create([
                    'image'     => $image,
                    // 'type'      => 2, //Question: how the image type defined?
                ]);

                $history->images()->create([
                    'image' => $image,
                ]);
            }
        }

        return $this->IssueDetails($user, $data, $request, $project->id);
    }

    /**
     * @SWG\Post(
     *     path="/issue/requestOwnerAcceptance",
     *     summary="To request owner acceptance",
     *     method="post",
     *     tags={"Issue"},
     *     description="This Api will change issue status to close and handover unit.",
     *     operationId="requestOwnerAcceptance",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="issue_id",type="string",example="[]"),
     *                      @SWG\Property(property="project_id",type="string",example="1"),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function requestOwnerAcceptance(Request $request)
    {
        $data = $this->data;
        $user = $this->user;
  
        $rules = [
            'project_id'      => 'required',     
        ];

        $message = [
            'data.project_id.required'  => "Project ID is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);


        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        $issues = $request->input('issue_id');
        $project_id = $request->input('project_id');

        if($user->current_role != 4 && $user->current_role != 8)
        {   
            $status = $this->failedAppData('Not authorize to access this function.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        foreach($issues as $issue)
        {
            if(!$issue = Issue::find($issue['id'])){

                $status = $this->failedAppData('Issue not found');

                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }

            if($issue->status_id != 10)
            {
                $status = $this->failedAppData('Cannot request owner acceptance for this issue.');

                $emptyData= collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }
        }

        foreach($issues as $issue)
        {
            $issue = Issue::find($issue['id']);
            
            $issue->update(['status_id' => 11]);

            $history = History::create([
                'user_id'               => $user->id,
                'issue_id'              => $issue->id,
                'status_id'             => 11,
                'remarks'               => 'Request Owner Acceptance.',
                'customer_view'         => null,
            ]);

            $title =  $issue->location->drawingPlan->drawingSet->project->name;
            $message =  'Issue('. $issue->reference .') is asked for redo.';
            $appData = array('type' => 'request_owner_acceptance','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);

            $owner = Issue::where('id', $issue->id)->select('owner_id')->get();
            $this->FCMnotification($title, $message, $appData, $owner, $issue->id, $user->id);
        }

        return $this->IssueDetails($user, $data, $request, $project_id);
    }

    /**
     * @SWG\Post(
     *     path="/issue/oaSignOff",
     *     summary="To request owner acceptance",
     *     method="post",
     *     tags={"Issue"},
     *     description="This Api will change issue status to close and handover unit.",
     *     operationId="oaSignOff",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="plan_id",type="string",example="1"),
     *                      @SWG\Property(property="issue",type="string",example="[]"),
     *                      @SWG\Property(property="signature_owner_name",type="string",example="name"),
     *                      @SWG\Property(property="signature_handler_name",type="string",example="name"),
     *                      @SWG\Property(property="signature_owner_datetime",type="string",example="2019-01-01 10:26:00"),
     *                      @SWG\Property(property="signature_handler_datetime",type="string",example="2019-01-01 10:26:00"),
     *                      @SWG\Property(property="signature_owner",type="string",example=""),
     *                      @SWG\Property(property="signature_handler",type="string",example=""),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function oaSignOff(Request $request)
    {
        $data = $this->data;
        $user = $this->user;
        $submission_type = "OA Sign Off";

        $rules = [
            'plan_id'                               => 'required',
            'signature_owner_name'                  => 'required',
            'signature_handler_name'                => 'required',
            'signature_owner_datetime'              => 'required',
            'signature_handler_datetime'            => 'required',
        ];

        $message = [
            'plan_id.required'                          => "Drawing Plan ID is required.",
            'signature_owner_name.required'             => "Signature owner's name is required.",
            'signature_handler_name.required'           => "Signature handler's name is required.",   
            'signature_owner_datetime.required'         => "Owner's signature is required.",
            'signature_handler_datetime.required'       => "Handler's signature is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);
        
        if ($validator->fails()) 
        {
            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if(!$drawing_plan = DrawingPlan::find($request->input('data.plan_id')))
        {
            $status = $this->failedAppData("Drawing Plan not found.");

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if($request->hasFile('data.signature_owner') && $request->hasFile('data.signature_handler'))
        {
            $signature_owner = $request->file('data')['signature_owner'];
            $signature_handler = $request->file('data')['signature_handler'];
        }
        else{
            $status = $this->failedAppData("Signature is required.");

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }
        

        //setup reference value
        $last = IssueFormSubmission::where('drawing_plan_id', $request->input('data.plan_id'))->count() + 1;
        $date = Carbon::now()->format('dmy');
        $reference_no = "$date-DP$drawing_plan->id-R$last";
        $value = $request->all()['data'];

        $accept_issues = [];
        $redo_issues = [];

        if(isset($value['issue'])){
            foreach($value['issue'] as $issue_id => $value)
            {
                // dd($issue_id, $value);
                $issue = Issue::where('id', $issue_id)->first();
                // if($issue->status_id != 11)
                // {
                //     $status = $this->failedAppData("Form submission failed.");

                //     $emptyData = collect();
                //     $emptyData->appData = $this->prepareAppData($request, $data, $status);

                //     return new BaseResource($emptyData);
                // }

                if($value['status'] == "accept")
                {
                    $issue->update([
                        "status_id" => 14,
                    ]);

                    //create history
                    $history = History::create([
                        'user_id'               => $user->id,
                        'issue_id'              => $issue->id,
                        'status_id'             => 14,
                        'remarks'               => 'Issue closed external.',
                        'customer_view'         => null,
                    ]);

                    //create accept array
                    array_push($accept_issues, $issue_id);
                }
                else if($value['status'] == "reject")
                {
                    $issue->update([
                        "status_id" => 9,
                    ]);

                    //create history
                    $history = History::create([
                        'user_id'               => $user->id,
                        'issue_id'              => $issue->id,
                        'status_id'             => 9,
                        'remarks'               => $value['remarks'],
                        'customer_view'         => null,
                    ]);

                    //create reject array
                    array_push($redo_issues, $issue_id);
                }
            }
        }

        $form_detail[] = (object)[
            'signature_owner_name'          => $request->input('data.signature_owner_name'),
            'signature_handler_name'        => $request->input('data.signature_handler_name'),
            "signature_owner"               => \App\Processors\SaveSignatureProcessor::make($signature_owner)->execute(),
            'signature_handler'             => \App\Processors\SaveSignatureProcessor::make($signature_handler)->execute(),
            'signature_owner_datetime'      => $request->input('data.signature_owner_datetime'),
            'signature_handler_datetime'    => $request->input('data.signature_handler_datetime'),
        ];

        $issue_submission = IssueFormSubmission::create([
            'reference_no'                  => $reference_no,
            'drawing_plan_id'               => $request->input('data.plan_id'),
            'remarks'                       => $request->input('data.remarks') ?? '',
            'submission_type'               => $submission_type,   
            'accept_issue'                  => $accept_issues,
            'redo_issue'                    => $redo_issues,
            'details'                       => $form_detail,
            'created_by'                    => $user->id,
            
        ]);

        $pdf = $this->generatePdf($issue_submission);

        $issue_submission->forceFill(['pdf_name'    => $pdf])->save();

        $project = $drawing_plan->drawingSet->project;

        return $this->IssueDetails($user, $data, $request, $project->id);

    }


    /**
     * @SWG\Post(
     *     path="/issue/closeAndHandover",
     *     summary="To close and handover issues",
     *     method="post",
     *     tags={"Issue"},
     *     description="This Api will change issue status to close and handover unit.",
     *     operationId="closeAndHandover",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="plan_id",type="string",example="1"),
     *                      @SWG\Property(property="form_id",type="string",example="1"),
     *                      @SWG\Property(property="input",type="string",example="[]"),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function closeAndHandover(Request $request)
    {
        $data = $this->data;
        $user = $this->user;
        $submission_type = "Close And Handover";

        $rules = [
            'plan_id'   => 'required',
            'form_id'   => 'required',       
            'input'     => 'required',
        ];

        $message = [
            'data.plan_id.required'         => "Drawing Plan is required.",
            'data.form_id.required'         => "Form is required.",
            'data.input.requred'            => "Input is required."
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);

        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if(!$plan = DrawingPlan::find($request->input('data.plan_id'))){

            $status = $this->failedAppData('Drawing plan not found');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if(!$form = FormVersion::find($request->input('data.form_id')))
        {
            $status = $this->failedAppData('Form not found.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);
            return new BaseResource($emptyData);
        }

        //setup reference value
        $last = IssueFormSubmission::where('drawing_plan_id', $request->input('data.plan_id'))->count() + 1;
        $date = Carbon::now()->format('dmy');
        $form_group_id = $form->id;
        $reference_no = "$date-F$form_group_id-DP$plan->id-R$last";

        if($plan->ready_to_handover != 0)
        {
            $status = $this->failedAppData('Drawing Plan is already closed and ready to handover.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        } 

        // check if all location of the unit is ready to handover
        foreach($plan->location as $locations)
        {
            if($locations->status_id != 2)
            {
                $status = $this->failedAppData('Please make sure all location is ready to handover.');

                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }
        }
        
        $plan->update([
            'ready_to_handover' => 1
        ]);

        // update all location of the drawing plan to close
        foreach($plan->location as $locations)
        {
            $locations->update([
                'status_id' => 3, 
            ]);
        }
        
        $value = $request->all()['data'];

        $issue_submission = IssueFormSubmission::create([
            'reference_no'      => $reference_no,
            'drawing_plan_id'   => $request->input('data.plan_id'),
            'form_version_id'   => $request->input('data.form_id'),
            'remarks'           => $request->input('data.remarks') ?? '',
            // 'details'           => $form_detail,
            'submission_type'   => $submission_type, 
            'created_by'        => $user->id,
        ]);

        //store digital form
        foreach($value['input'] as $attribute_id => $form_attribute_location)
        {
            foreach($form_attribute_location as $input_id => $input_value)
            {
                switch ($attribute_id) {
                    case 1: // long text
                        $form_detail[] = (object)[
                            "form_attribute_location_id"    => $input_id,
                            "value"                         => $input_value,
                        ];
                        break;
                    case 2: // short text
                        $form_detail[] = (object)[
                            "form_attribute_location_id"    => $input_id,
                            "value"                         => $input_value,
                        ];
                        break;
                    case 9: // dropdown box
                        $form_detail[] = (object)[
                            "form_attribute_location_id"    => $input_id,
                            "value"                         => $input_value,
                        ];
                        break;
                    case 3: // signature
                        $image_file = $input_value;
                        
                        $name_unique = 'signature_' . time() . rand(10, 99) . '.png';
                        $store_path = Submission::FILE_PATH . '/' . $issue_submission->id;
                        $path = public_path($store_path);
                        
                        if (! File::isDirectory($path)) {
                            File::makeDirectory($path, 0775, true);
                        }
                        
                        $image = Image::make($image_file);
                        
                        $size['width'] = $image->width();
                        $size['height'] = $image->height();
                        
                        $image->save($path . DIRECTORY_SEPARATOR . '' . $name_unique);
                        
                        $form_detail[] = (object)[
                            "form_attribute_location_id"    => $input_id,
                            "value"                         => asset($store_path) . '/' . $name_unique,
                        ];
                        break;
                    case 5: // date
                        
                        $date_input = new Carbon($input_value);
                        
                        $form_detail[] = (object)[
                            "form_attribute_location_id"    => $input_id,
                            "value"                         => $date_input->format('d-m-Y'),
                        ];
                        break;
                    
                    case 6: // checkbox
                        $form_detail[] = (object)[
                            "form_attribute_location_id"    => $input_id,
                            "value"                         => $input_value == 1 ? 1 : 0,
                        ];
                        break;
                    case 7: // choice
                        // $checkbox_input = $input_value == true || $input['value'] == 1 ? 1 : 0;
                        $form_detail[] = (object)[
                            "form_attribute_location_id"    => $input_id,
                            "value"                         => $input_value == 1 ? 1 : 0,
                        ];
                        break;
                } 
            }
        }

        $issue_submission->update([
            'details'           => $form_detail,
        ]);

        $project = $plan->drawingSet->project;
        //return
        return $this->IssueDetails($user, $data, $request, $project->id);
    }

    /**
     * @SWG\Post(
     *     path="/issue/closeInt_POA",
     *     summary="To Change all close integer to pending owner acceptance",
     *     method="post",
     *     tags={"Issue"},
     *     description="This Api will change close integer to pending owner acceptance.",
     *     operationId="closeInt_POA",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="plan_id",type="string",example="1"),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function closeInt_POA(Request $request)
    {
        $data = $this->data;
        $user = $this->user;
        $submission_type = "Close And Handover";

        $rules = [
            'plan_id'   => 'required',
        ];

        $message = [
            'data.plan_id.required'         => "Drawing Plan is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);

        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if(!$plan = DrawingPlan::find($request->input('data.plan_id'))){

            $status = $this->failedAppData('Drawing plan not found');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        $title =  $plan->drawingSet->project->name;
        $locations = $plan->location;
        
        foreach ($locations as $location)
        {
            $close_internal_issues = Issue::where('location_id', $location->id)->where('status_id', 10)->where('handover_status', 'handed over')->get();
            if(count($close_internal_issues)>0)
            {
                foreach($close_internal_issues as $close_internal_issue){
                    $close_internal_issue->update([
                        "status_id"     => 11, 
                    ]);

                    $history = History::create([
                        'user_id'               => $user->id,
                        'issue_id'              => $close_internal_issue->id,
                        'status_id'             => $close_internal_issue->status_id,
                        'remarks'               => 'Issue status changed to Pending Owner Acceptance.',
                        'customer_view'         => null,
                    ]);

                    $message =  'Issue('. $close_internal_issue->reference .') is changed to "Pending Owner Acceptance" status.';   

                    $appData = array('type' => 'pending_owner_acceptance','project_id' => $close_internal_issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $close_internal_issue->location->drawingPlan->id ,'location_id' => $close_internal_issue->location_id,'issue_id' => $close_internal_issue->id, 'show_in_foreground' => true);

                    $inspector = User::where('id', $close_internal_issue->inspector_id)->select('id as user_id')->get();
                    $this->FCMnotification($title, $message, $appData, $inspector, $close_internal_issue->id, $user->id);

                    $contractor = RoleUser::where('role_id', 5)->where('group_id', $close_internal_issue->group_id)->select('user_id')->get();
                    $this->FCMnotification($title, $message, $appData, $contractor, $close_internal_issue->id, $close_internal_issue->created_by);
                }
            }
        }
        
        //return
        $project = $plan->drawingSet->project;
        //return
        return $this->IssueDetails($user, $data, $request, $project->id);
    }

    public function generatePdf($form){
        $form = $form;
        $unit = $form->drawingPlan;
        $project = $form->drawingPlan->drawingSet->project;
        $user = $form->drawingPlan->unitOwner;


        if(count($form->accept_issue)>0)
        {
            $accept_issues = Issue::whereIn('id', $form->accept_issue)->get();
        }
        else{
            $accept_issues =[];
        }

        if(count($form->redo_issue)>0)
        {
            $redo_issues = Issue::whereIn('id', $form->redo_issue)->get();
        }
        else{
            $redo_issues = [];
        }
        

        $path = public_path('uploads/oasignoff-form-submission');

        if (!\File::isDirectory($path)) {

            \File::makeDirectory($path, 0775, true);
        }

        $unique_name = time() . rand(10, 9999) . '.pdf';

        $pdf = \PDF::loadView('pdf.oa-signoff-submissions.index', compact('project', 'unit', 'user', 'form','accept_issues', 'redo_issues'));
        $pdf->save("{$path}/${unique_name}");

        return $unique_name;

    }

}


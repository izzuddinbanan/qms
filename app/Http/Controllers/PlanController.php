<?php
namespace App\Http\Controllers;

use File;
use Auth;
use DB;
use App\Http\Controllers\Traits\PushNotification;
use App\Entity\DrawingSet;
use App\Entity\DrawingPlan;
use App\Entity\DrillDown;
use App\Entity\Issue;
use App\Entity\RoleUser;
use App\Entity\Notification;
use App\Entity\Project;
use App\Entity\History;
use App\Entity\User;
use App\Entity\SettingPriority;
use App\Entity\CategoryProject;
use App\Entity\SettingCategory;
use App\Entity\GroupProject;
use App\Entity\LocationPoint;
use App\Entity\PriorityProject;
use App\Entity\UserDevice;
use App\Entity\Status;
use App\Entity\GeneralStatus;
use Illuminate\Http\Request;
use App\Entity\FormSubmission;
use App\Entity\Submission;
use App\Entity\SubmissionFormGroup;
use App\Entity\IssueProject;
use App\Entity\SettingIssue;
use App\Entity\SettingType;
use Helper;

class PlanController extends Controller
{
    use PushNotification;

    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role = RoleUser::find(session('role_user_id'));
        
        $project = session('project_id');
        
        $DrawingSet = DrawingSet::where('project_id', $project)->select('id')->get();
        $list = DrawingPlan::whereIn('drawing_set_id', $DrawingSet)->orderBy('created_at')->get();


        $data = DrawingSet::where('project_id', $project)->orderBy('seq')->get();

        $issue_status = Status::get();
        
        foreach ($issue_status as $key => $value) {
            if ($value["id"] == 2) {
                $issue_status[$key]["icon"] = url('/assets/images/icon/pin_marker_orange.png');
            } elseif ($value["id"] == 3) {
                $issue_status[$key]["icon"] = url('/assets/images/icon/pin_marker_brown.png');
            } elseif ($value["id"] == 4) {
                $issue_status[$key]["icon"] = url('/assets/images/icon/pin_marker_biege.png');
            } elseif ($value["id"] == 5) {
                $issue_status[$key]["icon"] = url('/assets/images/icon/pin_marker_yellow.png');
            } elseif ($value["id"] == 6) {
                $issue_status[$key]["icon"] = url('/assets/images/icon/pin_marker_lightblue.png');
            } elseif ($value["id"] == 7) {
                $issue_status[$key]["icon"] = url('/assets/images/icon/pin_marker_grey.png');
            } elseif ($value["id"] == 8) {
                $issue_status[$key]["icon"] = url('/assets/images/icon/pin_marker_purple.png');
            } elseif ($value["id"] == 9) {
                $issue_status[$key]["icon"] = url('/assets/images/icon/pin_marker_pink.png');
            } elseif ($value["id"] == 10) {
                $issue_status[$key]["icon"] = url('/assets/images/icon/pin_marker_green.png');
            } elseif ($value["id"] == 1) {
                $issue_status[$key]["icon"] = url('/assets/images/icon/pin_marker_blue.png');
            }
        }

        foreach ($issue_status as $key => $value) {
            if ($issue_status[$key]["id"] == 3) {
                $issue_status[$key]["internal"] = "Pending";
            }

            if ($issue_status[$key]["id"] == 4) {
                $issue_status[$key]["internal"] = "Void";
            }
        }
        $location_status = GeneralStatus::where('type', 'location')->get();
        foreach ($location_status as $key => $value) {
            if ($value["id"] == 1) {
                $location_status[$key]["icon"] = url('/assets/images/icon/target_marker_blue.png');
            }
            if ($value["id"] == 2) {
                $location_status[$key]["icon"] = url('/assets/images/icon/target_marker_yellow.png');
            }
            if ($value["id"] == 3) {
                $location_status[$key]["icon"] = url('/assets/images/icon/target_marker_green.png');
            }
        }

        return view('plan.index', compact('list', 'role', 'data', 'issue_status', 'location_status'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = DrawingPlan::with('drill.link')->with('location.status')
            ->with('location.issues.status')
            ->with('location.issues.joinIssue')
            ->with('location.issues.history')
            ->find($id);

        $locations = $data->location->all();
        usort($locations, array($this, 'sorting'));
        unset($data['location']);
        $data->location = $locations;

        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function planDetails(Request $request)
    {
        $data = DrillDown::find($request->input('id'));
        
        return $data;
    }

    public function locationDetails(Request $request)
    {
        $data = LocationPoint::find($request->input('id'));
        
        return $data;
    }

    public function detailProject()
    {
        $project = Project::find(session('project_id'));
        
        // Cateegory/issue/type option
        $category_id = CategoryProject::where("project_id", $project->id)->select('category_setting_id')->get();
        $category = SettingCategory::with('hasTypes.hasIssues')->whereIn('id', $category_id)->get();
        
        // foreach ($category as $key => $value) {
        //     $checkCategory = CategoryProject::where('category_setting_id', $value["id"])->where('project_id', $project->id)->first();
        //     $category[$key]["group_id"] = $checkCategory->group_id;
        // }


        $issue_project = IssueProject::where('project_id',  $project->id)->select('issue_setting_id')->get();

        $issue = SettingIssue::whereIn('id', $issue_project)->select('category_id')->get();

        $category = SettingCategory::whereIn('id', $issue)->get();

        foreach ($category as $key => $value) {
            
            // $checkCategory = CategoryProject::where('category_setting_id', $value["id"])->where('project_id', $request->input('data.project_id'))->first();

            $typeCheck = SettingIssue::whereIn('id', $issue_project)->where('category_id', $value["id"])->select('type_id')->get();
            $type = SettingType::whereIn('id', $typeCheck)->get();

            foreach ($type as $typeKey => $typeValue) {

                $issueType = SettingIssue::whereIn('id', $issue_project)->where('type_id', $typeValue['id'])->get();

                foreach ($issueType as $keyIssueType => $valueIssueType) {

                    $group_id = IssueProject::where('project_id',$project->id)->where('issue_setting_id', $valueIssueType["id"])->first();

                    $issueType[$keyIssueType]["group_id"] = $group_id["group_id"];
                }

                $type[$typeKey]["issue"] = $issueType;
            }

            $category[$key]["type"] = $type;

        }

        // contractor option
        $contractor = GroupProject::with('groupDetails')->where('project_id', session('project_id'))->get();
        
        // priority option
        $priority = SettingPriority::where('client_id', $project->client_id)->get();
        $priority = PriorityProject::where('project_id', $project->id)->with('priority')->get();

        $inspector = RoleUser::with('users')->whereIn('role_id', [4, 8])
            ->where('project_id', $project->id)
            ->get();
        
        $drawing_set = DrawingSet::where('project_id', session('project_id'))->with('drawingPlan')->get();
        
        $detailsProject = array(
            "contractor" => $contractor,
            "category" => $category,
            "priority" => $priority,
            "inspector" => $inspector,
            'drawing' => $drawing_set
        );
        return $detailsProject;
    }

    public function issueStore(Request $request)
    {
        
        // $rules = [
        // 'category' => 'required|exists:setting_category,id',
        // 'type' => 'required|exists:setting_types,id',
        // 'issue' => 'required|exists:setting_issues,id',
        // 'comment' => 'required',
        // 'priority' => 'required|exists:setting_priority,id',
        // 'due_by' => 'required',
        // ];
        
        // $message = [];
        
        // $validator = Validator::make($request->input(), $rules, $message);
        
        // if ($validator->fails()) {
        // return response()->json(['errors'=>$validator->errors()->all()]);
        // }
        $issue = Issue::create([
            
            'location_id' => $request->input('location'),
            'owner_id' => null,
            'group_id' => $request->input('contractor'),
            'created_by' => Auth::user()->id,
            'setting_category_id' => $request->input('category'),
            'setting_type_id' => $request->input('type'),
            'setting_issue_id' => $request->input('issue'),
            'status_id' => 2,
            'priority_id' => $request->input('priority'),
            'due_by' => $request->input('due_by'),
            'position_x' => $request->input('pos_x'),
            'position_y' => $request->input('pos_y'),
            'remarks' => $request->input('comment')
        ]);
        
        // $now = \Carbon\Carbon::now('Asia/Kuala_Lumpur');
        // $unique_ref = $format . '-L' . $issue->location_id . 'I' . $issue->id . '-R' . $last;
        $unique_ref = Helper::generateIssueReferenceByLocationID($issue->location_id);
        
        $issue->forcefill([
            'reference' => $unique_ref
        ])->save();
        
        $history = History::create([
            'user_id' => Auth::user()->id,
            'issue_id' => $issue->id,
            'status_id' => 2,
            'remarks' => $request->input('comment')
        ]);
        
        // if (! empty($request->file('image'))) {
        //     $defect_image = \App\Processors\SaveIssueProcessor::make($request->file('image'))->execute();
            
        //     $issue->forcefill([
        //         'image' => $defect_image
        //     ])->save();
        //     $history->forcefill([
        //         'image' => $defect_image
        //     ])->save();
        // }

        if (count($request->input('image')) > 0) {
            foreach ($request->input('image') as $key => $value) {
                $seq = null;
                
                if ($key == 0) {
                    $seq = 1;
                }
                $issue->images()->create([
                    'image' => $value,
                    'type'  => 1,
                    'seq'   =>$seq,
                ]);

                $history->images()->create([
                    'image'      => $value,
                    'history_id' => $history->id,
                ]);
            }
        }

        
        $location = LocationPoint::find($request->input('location'));

        $title = $location->drawingPlan->drawingSet->project->name;
        $message = 'New issue reported('. $issue->reference .')';
       
        $appData = array(
            'type' => 'new_issue',
            'project_id' => session('project_id'),
            'plan_id' => $issue->location->drawingPlan->id,
            'location_id' => $request->input('location'),
            'issue_id' => $issue->id
        );
        
        if ($request->input('inspector')) {
            $issue->forcefill([
                'inspector_id' => $request->input('inspector')
            ])
                ->save();
            
            // #notification inspector
            $inspector_user_id = User::where('id', $request->input('inspector'))->select('id as user_id')->get();
            $this->FCMnotification($title, $message, $appData, $inspector_user_id, $issue->id, $issue->created_by);
        } else {
            $issue->forcefill([
                'inspector_id' => Auth::user()->id
            ])->save();
        }
        
        // #notification power user/ admin
        $admin_user_id = RoleUser::whereIn('role_id', [
            2,
            3
        ])->where('user_id', '!=', $issue->created_by)
            ->where('project_id', session('project_id'))
            ->select('user_id')
            ->get();

        $this->FCMnotification($title, $message, $appData, $admin_user_id, $issue->id, $issue->created_by);
        
        // #notification contractor
        $user_id = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)
            ->select('user_id')
            ->get();
        $this->FCMnotification($title, $message, $appData, $user_id, $issue->id, $issue->created_by);
        
        $data = Issue::where('id', $issue->id)->with('joinIssue')->with('status')->first();
        
        return $data;
    }

    public function issueDetails(Request $request)
    {
        $data = Issue::where('id', $request->input('id'))->with('category')
            ->with('type')
            ->with('issue')
            ->with('startImage')
            ->with('lastImage')
            ->with('priority')
            ->with('status')
            ->with('createdBy')
            ->with('joinIssue')
            ->with('inspector')
            ->with('owner')
            ->with('contractor')
            ->with('history.status')
            ->with('history.user')
            ->with('history.images')
            ->with('location')
            ->with('location.drawingPlan')
            ->first();
        
        $data["new_created_at"] = $data["created_at"]->format('d M Y');
        $data["issue_due"] = $data["due_by"] ? date('d M Y', strtotime($data["due_by"])) : null;
        
        foreach ($data["history"] as $value) {
            $value["issue_created"] = $value["created_at"]->format('d M Y');
            $value["issue_diffHuman"] = $value["created_at"]->diffForHumans();
        }
        return $data;
    }

    public function issueInfoStore(Request $request)
    {
        $issue = Issue::find($request->input('issue_id_info'));
        $history = History::create([
            
            'user_id' => Auth::user()->id,
            'issue_id' => $request->input('issue_id_info'),
            'status_id' => $issue->status_id,
            'remarks' => $request->input('comment_info')
        
        ]);
        
        if (! empty($request->file('image_info'))) {
            $image = \App\Processors\SaveIssueProcessor::make($request->file('image_info'))->execute();
            
            // $history->forcefill([
            //     'image' => $image
            // ])->save();

            $history->images()->create([
                'image' => $image
            ]);
        }

        $title =  $issue->location->drawingPlan->drawingSet->project->name;
        $message =  'New info for issue('. $issue->reference .')';
        
        $appData = array(
            'type' => 'new_info',
            'project_id' => session('project_id'),
            'plan_id' => $issue->location->drawingPlan->id,
            'location_id' => $issue->location->id,
            'issue_id' => $issue->id
        );

        // #notification inspector
        $inspector_user_id = User::where('id', $issue->inspector_id)->select('id as user_id')->get();
        $this->FCMnotification($title, $message, $appData, $inspector_user_id, $issue->id, Auth::user()->id);

        
        // #notification contractor
        $user_id = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)
            ->select('user_id')
            ->get();
        $this->FCMnotification($title, $message, $appData, $user_id, $issue->id, Auth::user()->id);

        return $request->input('issue_id_info');
    }

    public function editIssue(Request $request)
    {
        $issue = Issue::find($request->input('id'));

        $detailsProject = $this->detailProject();

        $data = array("issue" => $issue, "details" => $detailsProject);
        return $data;
    }

    public function updateIssue(Request $request)
    {
        $destinationPath = public_path('uploads/issues/');
        
        $issue = Issue::find($request->input("issue_id"));
        
        $issue->update([
            'setting_category_id' => $request->input('category'),
            'setting_type_id' => $request->input('type'),
            'setting_issue_id' => $request->input('issue'),
            'group_id' => $request->input('contractor'),
            'priority_id' => $request->input('priority'),
            'due_by' => $request->input('due_by'),
            'remarks' => $request->input('comment')
        ]);

        if (count($request->input('image')) > 0) {
            
            // $issueImage = Issue::with('images')->find($request->input("issue_id"));

            // foreach ($issueImage["images"] as $key => $value) {
            //     File::delete($destinationPath.  $value->image);
                
            // }
            
            foreach ($request->input('image') as $key => $value) {
                $issue->images()->delete();

                $seq = null;
                
                if ($key == 0) {
                    $seq = 1;
                }
                $issue->images()->create([
                    'image' => $value,
                    'type'  => 1,
                    'seq'   =>$seq,
                ]);

                // $history->images()->create([
                //     'image'      => $value,
                //     'history_id' => $history->id,
                // ]);
            }
        }
        
        return $issue->id;
    }

    public function getIssueDocuments(Request $request)
    {
        $param = request()->query();
        $search = app('request')->input('search');
        $locationID = $request->input('location_id');
        
        $formSubmissions = Submission::where('location_id', $locationID)->with('user')
            ->search($search, null, true, true)
            ->sortable()
            ->paginate(10);
        
        return view('plan.documents', compact('formSubmissions'));
    }

    public function getIssueDocument(Request $request, $id)
    {
        $select_columns = [
            'submission_form_group.value as input_value',
            'form_attribute_locations.position_x as input_position_x',
            'form_attribute_locations.position_y as input_position_y',
            'form_attribute_locations.height as input_height',
            'form_attribute_locations.width as input_width',
            'form_attributes.key as input_key',
            'form_attributes.attribute_id as input_id_type',
            'forms.id as form_id',
            'forms.height as form_height',
            'forms.width as form_width',
        ];
        
        $friends_votes = \DB::table('submission_form_group')->where('submission_id', $id)
            ->join('form_attribute_locations', 'submission_form_group.form_attribute_location_id', '=', 'form_attribute_locations.id')
            ->join('form_attributes', 'form_attribute_locations.form_attribute_id', '=', 'form_attributes.id')
            ->join('forms', 'form_attributes.form_id', '=', 'forms.id')
            ->select($select_columns)
            ->get();
        
        return $friends_votes = collect($friends_votes)->groupBy('form_id');
        
        return $friends_votes;
    }

    public function resetPosition(Request $request)
    {
        return Issue::find($request->input('id'));
    }

    public function moveIssue(Request $request)
    {
        $issue = Issue::find($request->input('id'));

        if ($issue->merge_issue_id != null) {
            $parent_issue = Issue::find($issue->merge_issue_id);
            $parent_issue->update([
                'position_x'  => $request->input('x'),
                'position_y'  => $request->input('y'),
            ]);
        } else {
            $issue->update([
                'position_x'  => $request->input('x'),
                'position_y'  => $request->input('y'),
            ]);
        }
    }

    public function voidIssue(Request $request)
    {
        $issue = Issue::find($request->input('id'));
        
        $issue->forcefill([
            'void_by' => Auth::user()->id,
            'status_id' => 4
        ])->save();
        
        History::create([
            'user_id' => Auth::user()->id,
            'issue_id' => $request->input('id'),
            'status_id' => 4,
            'remarks' => 'Void the issue.'
        ]);
        
        return Issue::where('id', $request->input('id'))->with('status')->first();
    }

    public function joinIssue(Request $request)
    {
        $issue = Issue::find($request->input('issue_id'));
        
        $issue->update([
            'merge_issue_id' => $request->input('move_to_issue_id')
        ]);

        $issue = Issue::with('joinIssue')->find($request->input('move_to_issue_id'));

        return $issue;
    }

    public function setupModeEditLink(Request $request)
    {
        $drill = DrillDown::where('id', $request->input('id'))->with('link')->first();
        
        $data = array(
            'drill' => $drill
        );
        
        return $data;
    }

    public function setupModeUpdateLink(Request $request)
    {
        $drill = DrillDown::find($request->input('link_id'));
        
        $drill->update([
            'to_drawing_plan_id' => $request->input('drawing_plan')
        ]);
        
        return DrawingPlan::find($request->input('drawing_plan'));
    }

    public function viewMerge(Request $request)
    {
        $issue = Issue::where('merge_issue_id', $request->input('id'))->get();

        return $issue->count();
    }

    public function updateLocationStatus(Request $request)
    {
        $location = LocationPoint::find($request->input('id'));

        $location->forcefill([
            'status_id'   => $request->input('status_id'),
        ])->save();

        return $location;
    }


    public function listJoinIssue(Request $request)
    {
        $data = Issue::where('merge_issue_id', $request->input('id'))->select('id')->get();

        foreach ($data as $key => $value) {
            $issue_id[] = $value["id"];
        }

        $id = $request->input('id');
        array_push($issue_id, $id);

        $issue = Issue::whereIn('id', $issue_id)
                ->with('category')
                ->with('type')
                ->with('issue')
                ->with('priority')
                ->with('status')
                ->with('location')
                ->with('startImage')
                ->get();

        foreach ($issue as $key=>$value) {
            $issue[$key]["new_created_at"] = $value["created_at"]->format('d M Y');
        }

        return $issue;
    }

    public function splitIssue(Request $request)
    {
        $issue = Issue::find($request->input('id'));

        if ($merge_issue = Issue::where('merge_issue_id', $request->input('id'))->first()) {
            $merge_issue->update([
                'position_x'        => $issue->position_x,
                'position_y'        => $issue->position_y,
                'merge_issue_id'    => null,
            ]);

            Issue::where('merge_issue_id', $request->input('id'))->update(['merge_issue_id' => $merge_issue->id]);
        }

        $issue->update([
            'merge_issue_id'    => null,
            'position_x'        => $request->input('x'),
            'position_y'        => $request->input('y'),
        ]);
    }

    public function storeMergeIssue(Request $request)
    {
        $issue = Issue::find($request->input('active_marker_id'));

        $issue->update([
            'conflict_issue_id' => $request->input('merge_issue_id'),
        ]);


        Notification::where('issue_id', $request->input('active_marker_id'))->delete();

        $issue->delete();
    }

    public function mergeHistory(Request $request)
    {
        $data = Issue::withTrashed()->where('conflict_issue_id', $request->input('id'))->select('id')->get();

        if (count($data) < 1) {
            return array("errors" => "No merge history.");
        }

        foreach ($data as $key => $value) {
            $issue_id[] = $value["id"];
        }

        $issue = Issue::withTrashed()->whereIn('id', $issue_id)
                ->with('category')
                ->with('type')
                ->with('issue')
                ->with('priority')
                ->with('status')
                ->with('location')
                ->with('startImage')
                ->get();

        foreach ($issue as $key=>$value) {
            $issue[$key]["new_created_at"] = $value["created_at"]->format('d M Y');
        }

        return $issue;
    }

    public function sorting($a, $b)
    {
        $a_array = explode('-', $a->reference);
        $asorter = isset($a_array[2]) ? str_replace('R', '', $a_array[2]) : '0';

        $b_array = explode('-', $b->reference);
        $bsorter = isset($b_array[2]) ? str_replace('R', '', $b_array[2]) : '0';

        $asorter = (int) $asorter;
        $bsorter = (int) $bsorter;

        if ($asorter == $bsorter) {
            return 0;
        }
        return ($asorter < $bsorter) ? -1 : 1;
    }
}

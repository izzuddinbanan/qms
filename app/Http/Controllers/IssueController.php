<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use Helper;
use App\Entity\Project;
use App\Entity\DrawingSet;
use App\Entity\RoleUser;
use App\Entity\User;
use App\Entity\DrawingPlan;
use App\Entity\LocationPoint;
use App\Entity\Issue;
use App\Entity\History;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\PushNotification;

class IssueController extends Controller
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
    public function index(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    public function addInfo(Request $request)
    {
        $user = Auth::user();
        if (!$issue = Issue::find($request->input('issue_id'))) {
            return back()->with(['error-message' => 'Something wrong. Please try again.']);
        }

        $rules = [
          'info'       => 'required',
        ];

        $message = ['info.required' => "Please fill in the message field."];

        $validator = Validator::make($request->input(), $rules, $message);

        if ($validator->fails()) {
            return back()
              ->withErrors($validator)
              ->withInput();
        }

        $history = History::create([
            'user_id'               => $user->id,
            'issue_id'              => $request->input('issue_id'),
            'status_id'             => $issue->status_id,
            'remarks'               => $request->input('info'),
        ]);

        $issue = Issue::where('id', $request->input('issue_id'))->with('location.drawingPlan')->first();

        $title = $issue->location->drawingPlan->drawingSet->project->name;
        $message =  'New info for issue('. $issue->reference .')';

        $appData = array('type' => 'new_info','project_id' => session('project_id') ,'plan_id' => $issue->location->drawingPlan->id,'location_id' => $issue->location_id, 'issue_id' => $issue->id);

        ##inspector
        $inspector_user_id = User::where('id', $issue->inspector_id)->select('id as user_id')->get();
        $this->FCMnotification($title, $message, $appData, $inspector_user_id, $issue->id, $user->id);

        ##contractor
        $contractor = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)->select('user_id')->get();
        $this->FCMnotification($title, $message, $appData, $contractor, $issue->id, $user->id);

        return back()->with(['success-message' => 'Success.']);
    }
}

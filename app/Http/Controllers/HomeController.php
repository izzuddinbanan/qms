<?php

namespace App\Http\Controllers;

use Auth, Session;
use App\Entity\Language;
use App\Entity\User;
use App\Entity\RoleUser;
use App\Entity\Project;
use App\Entity\DrawingSet;
use App\Entity\DrawingPlan;
use App\Entity\LocationPoint;
// use App\Entity\Role;
// use App\Entity\Client;
// use App\Entity\RoleUser;
// use App\Entity\GroupContractor;
// use App\Entity\Issue;
// use App\Entity\Notification;
// use App\Entity\UserDevice;
use App\Entity\Status;
use Illuminate\Http\Request;
use App\Services\FilterService;
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\IOFactory;

##testing code
// use LaravelFCM\Message\OptionsBuilder;
// use LaravelFCM\Message\PayloadDataBuilder;
// use LaravelFCM\Message\PayloadNotificationBuilder;
// use FCM;
// use DB;
// use App\Entity\SettingPriority;
// use Carbon\Carbon;
// use Image;
// use File;

use App\Notifications\PushNotification;

class HomeController extends Controller
{
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

        $role_user = role_user();

        switch ($role_user->role_id) {
            case 1: # SUPER USER
                return $this->dashboardSuperUser();
                break;
            
            case 2: #CLIENT
                return $this->dashboardClients();
                break;
        }

    }


    public function dashboardSuperUser() {
        return view('dashboards.super_users.index');
    }

    public function dashboardClients() {
        if (Session::has('project_id')) {
            
            $project = Project::find(session('project_id'));

            $drawingSet = DrawingSet::where('project_id', $project->id)->select('id')->get();
            $drawingPlan = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->select('id')->get();
            $location = LocationPoint::whereIn('drawing_plan_id', $drawingPlan)->select('id')->get();

            // for status
            $status = Status::select('internal_color', 'internal', 'id')->get();
            $status = $status->keyBy(function ($item) {
                return implode('_', explode(' ', strtolower(str_replace('.', '', $item['internal']))));
            });

            $issue = \DB::table('issues')
                ->join('status', 'status.id', '=', 'issues.status_id')
                ->join('locations', 'locations.id', '=', 'issues.location_id')
                ->join('drawing_plans', 'drawing_plans.id', '=', 'locations.drawing_plan_id')
                ->join('drawing_sets', 'drawing_sets.id', '=', 'drawing_plans.drawing_set_id')
                ->where('drawing_sets.project_id', $project->id)
                ->select([
                    \DB::raw('sum(status.id = "1") lodged'),
                    \DB::raw('sum(status.id = "2") new'),
                    \DB::raw('sum(status.id = "3") pending_start'),
                    \DB::raw('sum(status.id = "4") void'),
                    \DB::raw('sum(status.id = "5") wip'),
                    \DB::raw('sum(status.id = "6") not_me'),
                    \DB::raw('sum(status.id = "7") reassign'),
                    \DB::raw('sum(status.id = "8") completed'),
                    \DB::raw('sum(status.id = "9") redo'),
                    \DB::raw('sum(status.id = "10") closed'),
                ])->first();
            // status end

            // priority
            $setting_priority = \DB::table('priority_project')
                ->join('setting_priority', 'setting_priority.id', 'priority_project.priority_id')
                ->where('priority_project.project_id', $project->id)
                ->whereNull('priority_project.deleted_at')
                ->select([
                    'setting_priority.id as id',
                    'setting_priority.type as type',
                    'setting_priority.name as name',
                    'setting_priority.no_of_days as no_of_days',
                ]);
                
            $field = [];
            
            if ($setting_priority->count()) {
                $setting_priority = $setting_priority->get();
                foreach ($setting_priority as $key => $val) {
                    $query_string = "sum(setting_priority.id = {$val->id}) priority_{$val->id}";
                    $field[] = \DB::raw($query_string);
                }
                
                $priority = \DB::table('issues')
                    ->join('setting_priority', 'setting_priority.id', '=', 'issues.priority_id')
                    ->join('locations', 'locations.id', '=', 'issues.location_id')
                    ->join('drawing_plans', 'drawing_plans.id', '=', 'locations.drawing_plan_id')
                    ->join('drawing_sets', 'drawing_sets.id', '=', 'drawing_plans.drawing_set_id')
                    ->where('drawing_sets.project_id', $project->id)
                    ->select($field)->first();

                $priority = json_decode(json_encode($priority), true);
                $setting_priority = json_decode(json_encode($setting_priority), true);
            } else {
                $setting_priority = [];
            }

            $filter_service = new FilterService();
            $type_count = $filter_service->generateTypeSummaryQuery(['project_id' => $project->id])->get();
            $count = array_count_values(array_map(function ($v) {
                return $v->issue_name;
            }, $type_count->toArray()));
            
            $type_holder = [];
            foreach ($type_count as $key => $val) {
                if ($count[$val->issue_name] > 1) {
                    $val->issue_name = $val->type_name . '/' . $val->issue_name;
                }
                $type_holder[] = $val;
            }
            
            $type_count = $type_holder;

            return view('dashboardUser', compact('dayType', 'project', 'issue', 'status', 'priority', 'setting_priority', 'type_count'));
        }
        return redirect()->route('project.index');
    }


    public function switchClient($role_user_id)
    {
        $role_user = RoleUser::with('roles')->with('clients')->where('id', $role_user_id)->first();
        
        Session::forget('project_id');
        Session::forget('role_user_id');
        Session::put('role_user_id', $role_user->id);

        return redirect()->route('home.project')->with('success-message', 'You have switched to '. $role_user->clients->name .' as a '.$role_user->roles->display_name.'.');
    }

    public function switchUser($id = null)
    {

        if (Session::has('original_user_id')) {
            Auth::login(User::find(Session::get('original_user_id')));
            Session::forget('original_user_id');

            $this->setRole();

            return redirect()->route('home')->with('success-message', 'You have switched back to '. Auth::user()->name .'.');

        }else {
            Session::put('original_user_id', Auth::user()->id);
            Auth::login(User::find($id));
            $this->setRole();

        }

        return redirect()->route('project.index')->with('success-message', 'You have switched to '. Auth::user()->name .'.');

    }


    public function setRole()
    {
        Session::forget('project_id');
        $role_user = RoleUser::where('user_id', Auth::user()->id)->first();
        Session::put('role_user_id', $role_user->id);
    }

    public function downloadZip()
    {
        $files = glob(public_path('uploads'));

        \Zipper::make(public_path('test.zip'))->add($files)->close();

        return response()->download(public_path('test.zip'));
    }


    public function viewNotification($id)
    {
        $user = Auth::user();

        if (!$notification = Notification::where('user_id', $user->id)->where('id', $id)->first()) {
            return back();
        }

        if (!$issue = Issue::where('id', $notification->issue_id)->with('location.drawingPlan.drawingSet')->first()) {
            return back();
        }

        ## FORGET SESSION PROJECT->SWITCH PROJECT
        Session::forget('project_id');
        Session::put('project_id', $issue->location->drawingPlan->drawingSet->project_id);

        $notification->update(['read_status_id' => 1]);

        return redirect()->route('issue.show', [$notification->issue_id]);
    }


    public function mainDashboard()
    {
        $client_id = RoleUser::find(session('role_user_id'))->client_id;

        $service = new FilterService();
        $contractors = $service->generateContractorSummaryQuery()->get();

        $status = Status::get()->pluck('internal', 'id');

        $field = [
            'projects.id as project_id',
            'projects.name as project_name',
        ];

        $client_id = RoleUser::find(session('role_user_id'))->client_id;

        foreach ($status as $key => $val) {
            $query_string = "sum(status.id = {$key}) status_{$key}";
            $field[] = \DB::raw($query_string);
        }

        $filter_service = new FilterService();
        $projects = $filter_service->generateProjectSummaryQuery()->where('clients.id', $client_id)->get();

        $today = Carbon::today();
        $day_6_before_today = Carbon::today()->subDays(6)->addDay();
        $day_7_before_today = Carbon::today()->subDays(7)->addDay();
        $day_14_before_today = Carbon::today()->subDays(14)->addDay();
        $day_15_before_today = Carbon::today()->subDays(15)->addDay();
        $day_22_before_today = Carbon::today()->subDays(22)->addDay();
        $day_23_before_today = Carbon::today()->subDays(23)->addDay();
        $day_30_before_today = Carbon::today()->subDays(30)->addDay();

        $query = '(status.internal = "W.I.P" OR status.internal = "Redo") AND issues.start_date <> "0000-00-00"';

        $issue_field = [
            'projects.id as project_id',
            \DB::raw('sum(status.internal = "Lodged" OR status.internal = "New") new_issues'),
            \DB::raw('sum(status.internal = "Pending Start") pending_start_issues'),
            \DB::raw('sum(' . $query . ') wip_issues'),
            \DB::raw('sum(status.internal = "Completed") completed_issues'),
            \DB::raw('sum(status.internal = "Closed") closed_issues'),
            \DB::raw('sum(' . $query . ' AND issues.start_date <="' . $today->format('Y-m-d') . '" AND issues.start_date >="' . $day_6_before_today->format('Y-m-d') . '") issues_less_than_7_days'),
            \DB::raw('sum(' . $query . ' AND issues.start_date <="' . $day_7_before_today->format('Y-m-d') . '" AND issues.start_date >="' . $day_14_before_today->format('Y-m-d') . '") issues_7_to_14_days'),
            \DB::raw('sum(' . $query . ' AND issues.start_date <="' . $day_15_before_today->format('Y-m-d') . '" AND issues.start_date >="' . $day_22_before_today->format('Y-m-d') . '") issues_15_to_22_days'),
            \DB::raw('sum(' . $query . ' AND issues.start_date <="' . $day_23_before_today->format('Y-m-d') . '" AND issues.start_date >="' . $day_30_before_today->format('Y-m-d') . '") issues_23_to_30_days'),
            \DB::raw('sum(' . $query . ' AND issues.start_date <"' . $day_30_before_today->format('Y-m-d') . '") issues_more_than_30_days'),
        ];

        $issue_count = $filter_service->generateIssueQuery()->groupBy('projects.id')->where('clients.id', $client_id)->select($issue_field)->get()->keyBy('project_id');

        $type_count = $filter_service->generateTypeSummaryQuery()->get();
        $count = array_count_values(array_map(function ($v) {
            return $v->issue_name;
        }, $type_count->toArray()));
           
        $type_holder = [];
        foreach ($type_count as $key => $val) {
            if ($count[$val->issue_name] > 1) {
                $val->issue_name = $val->type_name . '/' . $val->issue_name;
            }
            
            $type_holder[] = $val;
        }

        $type_count = $type_holder;
        
        return view('dashboard', compact('projects', 'issue_count', 'contractors', 'type_count'));
    }

    public function resizeImage()
    {
        set_time_limit(0);

        $drawing_path = public_path('uploads/drawings');
        $drawing_thumbnail_path = public_path('uploads/drawings/thumbnail');

        if (!File::isDirectory($drawing_thumbnail_path)) {
            File::makeDirectory($drawing_thumbnail_path, 0777, true);
        }

        $drawing_image = preg_grep('~\.(jpeg|jpg|png)$~', scandir($drawing_path));
        
        foreach ($drawing_image as $key => $value) {
            $image = Image::make($drawing_path . '/' . $value);

            $image->resize(400, null, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('jpg', 0)->save($drawing_thumbnail_path . '/' . $image->basename);
        }

        $issues_path = public_path('uploads/issues');
        $issues_thumbnail_path = public_path('uploads/issues/thumbnail');

        if (!File::isDirectory($issues_thumbnail_path)) {
            File::makeDirectory($issues_thumbnail_path, 0777, true);
        }

        $issue_image = preg_grep('~\.(jpeg|jpg|png)$~', scandir($issues_path));
        
        foreach ($issue_image as $key => $value) {
            $image = Image::make($issues_path . '/' . $value);

            $image->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('jpg', 0)->save($issues_thumbnail_path . '/' . $image->basename);
        }

        return 1;
    }


    public function switchLanguage($id){

        $user = Auth::user();

        if(!$lang = Language::find($id)){

            return back()->with(['warning-message' => 'Language not found.']);
        }
        
        $user->update([
            'language_id'     => $id,
        ]);

        return back()->with(['success-message' => trans('alert.change-lang') . $lang->name . '.' ]);

    }

    public function removeNotification(){


        $location = DB::table('locations')->whereNotNull('deleted_at')->get();

        foreach ($location as $key => $value) {
            $locationID[] = $value->id;
        }

        $issue = Issue::whereIn('location_id', $locationID)->get();

        foreach ($issue as $key => $value) {

            Notification::where('issue_id', $value->id)->delete();

        }

    }

}

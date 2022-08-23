<?php

namespace App\Http\Controllers\Manages\ProjectSettings;

use App\Entity\DrawingSet;
use App\Entity\Project;
use App\Entity\Role;
use App\Entity\RoleUser;
use App\Entity\User;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Session;
use Validator;
use Yajra\Datatables\Datatables;


class SetEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('project-settings.set-employees.index');
    }

    public function indexData($value='')
    {

        $client_id = RoleUser::find(session('role_user_id'));
        $data = RoleUser::where('client_id', $client_id->client_id)
                        ->whereIn('role_id', [3, 4, 8])
                        ->where('project_id', 0)
                        ->with('users')
                        ->with('roles');

        $user_project = RoleUser::where('client_id', $client_id->client_id)
                        ->where('project_id', session('project_id'))
                        ->pluck('user_id')
                        ->toArray();

        $project = Project::where('id', session('project_id'))->first();


        return Datatables::of($data)
            ->editColumn('users.name', function ($data) use ($project) {

                if($project->default_project_team_id == $data->users->id){
                    return  $data->users->name . ' <span class="label label-success">Main</span>';
                }
                return $data->users->name;
            })
            ->addColumn('is_current', function ($data) use ($user_project) {

                if(in_array($data->users->id, $user_project)){
                    return '<a href="" onclick="isCurrent('. $data->id .', true)"><i class="fa fa-check-square-o fa-lg isCurrent-'. $data->id .'"></i></a>';
                }

                return '<a href="" onclick="isCurrent('. $data->id .', false)"><i class="fa fa-square-o fa-lg isCurrent-'. $data->id .'"></i></a>';

            })
            ->addColumn('action', function ($data) use($project) {


                if($data->roles->display_name == "Project Team" && $project->default_project_team_id != $data->users->id){
                    $form = '<form action="'. route("set-employee.set-default") .'" method="POST">' . csrf_field();
                    $form .= '<button type="submit" name="default_project_team_id" class="btn btn-primary btn-xs" value="'. $data->users->id .'">Set as Main </button></form>';
                    return $form;
                }
            })
            ->rawColumns(['users.name', 'is_current', 'action'])
            ->make(true);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function saveUserProject(Request $request){

        $roleUser = RoleUser::find(session('role_user_id'));
        $user_id  = RoleUser::find($request->input('role_id'));

        if(!$user = RoleUser::where('client_id', $roleUser->client_id)->where('user_id', $user_id->user_id)->first()){

            return array("errors" => 'Record not found.');
        }

        RoleUser::create([
            'user_id'       => $user_id->user_id,
            'project_id'    => session('project_id'),
            'role_id'       => $user_id->role_id,
            'client_id'     => $roleUser->client_id,
        ]);
    }

    public function destroyUserProject(Request $request){

        $roleUser = RoleUser::find(session('role_user_id'));
        $user_id  = RoleUser::find($request->input('role_id'));

        if(!$user = RoleUser::where('client_id', $roleUser->client_id)->where('user_id', $user_id->user_id)->first()){

            return array("errors" => 'Record not found.');
        }

        if($roleUser->role_id != 2 && $user_id->role_id == 3){
            return array("errors" => 'You are not allowed to do this action.');
        }

        RoleUser::where('user_id', $user_id->user_id)
                ->where('role_id', $user_id->role_id)
                ->where('project_id', session('project_id'))
                ->delete();
    }

    public function setAsDefault(Request $request)
    {
        $project_id = session('project_id');
        $default_project_team_id=Project::where('id', $project_id)->first()->default_project_team_id;
        if(!is_null($default_project_team_id) && $default_project_team_id == $request->input('default_project_team_id'))
        {
            return back()->with(['warning-message' => 'This user is the main project team.']);
        }

        $count_project_team = RoleUser::where('user_id', $request->input('default_project_team_id'))->where('role_id', 8)->where('project_id', $project_id)->count();

        if($count_project_team == 0)
        {
            return back()->with(['warning-message' => 'This user is not Project Team of this project.']);
        }

        $project = Project::where('id', $project_id)->first();
        $project->update([
            'default_project_team_id'   => $request->input('default_project_team_id'),
        ]);

        return back();
    }
}

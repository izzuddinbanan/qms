<?php

namespace App\Http\Controllers\ProjectSetup;

use Auth;
use App\Entity\User;
use Validator;
use Session;
use App\Entity\Role;
use App\Entity\Project;
use App\Entity\RoleUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Step5Controller extends Controller
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
        $id = session('project_id');
        $client_id = RoleUser::find(session('role_user_id'));
        $project = Project::where('id', $id)->first();

        $data = RoleUser::where('client_id', $client_id->client_id)
                        ->whereIn('role_id', [3, 4, 8])
                        ->where('project_id', 0)
                        ->with('users')
                        ->with('roles')
                        ->get();

        $user_project = RoleUser::where('client_id', $client_id->client_id)
                        ->where('project_id', $id)
                        ->pluck('user_id')
                        ->toArray();

        return view('project.step5', compact('id', 'data', 'user_project', 'project'));
        
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
        $rules = [
          'name'            => 'required',
          'email'           => 'required|email|unique:users,email',
          'role'            => 'required',
          'password'        => 'required',
          'contact'           => 'numeric|nullable',
        ];

        $message = [];

        $validator = Validator::make($request->input(), $rules, $message);  

        if ($validator->fails()) {
        return back()
            ->withErrors($validator)
            ->with(["modal" => "show"])
            ->withInput();
        }

        $user = User::create([
            'name'              => $request->input('name'),
            'email'             => $request->input('email'),
            'password'          => bcrypt($request->input('password')),
            'contact'           => $request->input('contact'),

        ]);


        $roleUser = RoleUser::find(session('role_user_id'));

        RoleUser::create([
            'user_id'       => $user->id,
            'role_id'       => $request->input('role'),
            // 'project_id'    => session('project_id'),
            'client_id'     =>$roleUser->client_id,
        ]);

        RoleUser::create([
            'user_id'       => $user->id,
            'role_id'       => $request->input('role'),
            'project_id'    => session('project_id'),
            'client_id'     =>$roleUser->client_id,
        ]);


        return back()->with(['success-message' => 'New record successfully added.']);

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


    public function listRole(){

        $data = Role::whereIn('id', [3, 4, 8])->get();

        return $data;

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

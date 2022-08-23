<?php

namespace App\Http\Controllers\Manages\Projects;

use App\Entity\Project;
use App\Entity\RoleUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session, Auth, Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        ## FORGET SESSION PROJECT->SWITCH PROJECT
        Session::forget('project_id');
        Session::forget('langSetup');

        $user = Auth::user();

        $role_user = role_user();
        
        // power user
        if($role_user->role_id == 2){
            $data = Project::where('client_id', $role_user->client_id )->paginate(12);
        }

        // admin
        if($role_user->role_id == 3){
            
            $projectUser = RoleUser::where('user_id', $role_user->user_id)->where('client_id', $role_user->client_id)->select('project_id')->get();

            $data = Project::whereIn('id', $projectUser)->paginate(12);

        }
        
        return view('projects.index', compact('data'));
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
    public function destroy(Request $request, $id)
    {

        if ($request->ajax()) {

            $role_user = RoleUser::find(session('role_user_id'));

            if($role_user->role_id == 2){

                if ($project = Project::where('id', $id)->where('client_id', $role_user->client_id)->first()) {
                    
                    $project->categoryProject()->delete();
                    $project->delete();

                    ## NEED TO CHECK PROJECT | EVERYTHIIG DELETED | LATER
                    return response()->json(['status' => 'ok']);
                }
            }

        }
        return Response::json(['status' => 'fail']);

    }

    public function switchProject(Request $request){

        $project_id = $request->input('project_id');

        ##SET SESSION FOR PROJECT ID
        Session::put('project_id', $project_id);
        return redirect()->route('home');
    }
}

<?php

namespace App\Http\Controllers;

use AUth;
use File;
use Validate;
use Session;
use App\Entity\User;
use App\Entity\Role;
use App\Entity\RoleUser;
use App\Entity\Project;
use App\Entity\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;


class ProjectController extends Controller
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
        ## FORGET SESSION PROJECT->SWITCH PROJECT
        Session::forget('project_id');
        Session::forget('langSetup');

        $user = Auth::user();
        
        $param = request()->query();
        $search = app('request')->input('search');

        $role_user = RoleUser::with('roles')->with('clients')->where('id', session('role_user_id'))->first();
        
        // power user
        if($role_user->role_id == 2){
            $data = Project::where('client_id', $role_user->client_id )->search($search, null, true, true)->paginate(12);
        }

        // admin
        if($role_user->role_id == 3){
            
            $projectUser = RoleUser::where('user_id', $role_user->user_id)->where('client_id', $role_user->client_id)->select('project_id')->get();

            $data = Project::whereIn('id', $projectUser)->search($search, null, true, true)->paginate(12);

        }
        
        
        return view('project.index', compact('data','search'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   

        $langSetup = array();
        if(session('langSetup')){
            $langSetup = session('langSetup');
        }     
        else{
            Session::put('langSetup', []);
            $langSetup = session('langSetup');
        }

        $language = Language::get();

        foreach ($language as $key => $value) {

            if(in_array($value->id, $langSetup)){
                $language[$key]["check"] = "checked";
            }else{
                $language[$key]["check"] = "";
            }

            if($value->id == 1){
                $language[$key]["active"] = "active";
            }else{
                $language[$key]["active"] = "";
            }
        }

        return view('project.create', compact('language', 'langSetup'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $step)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $project_id = $request->input('project_id');

        ##SET SESSION FOR PROJECT ID
        Session::put('project_id', $project_id);
        return redirect()->route('home');
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

        $role_user = RoleUser::find(session('role_user_id'));

        if(!$project = Project::where('id', $id)->where('client_id', $role_user->client_id)->first()){

            return back()->withErrors('Record not found.')->withInput();
        }

        $project->categoryProject()->delete();
        $project->delete();

        return back()->with(['success-message' => 'Record successfully deleted.']);

    }

    public function chooseLangSetup(Request $request){

        $lang = array();
        Session::forget('langSetup');

        if($request->input('lang')==null){
            
            $lang = [1];
            Session::put('langSetup', $lang);

        }else{

            foreach ($request->input('lang') as $key => $value) {

                array_push($lang, $value);
            }
                array_push($lang, 1);
                Session::put('langSetup', $lang);

        }

        return redirect()->route('project.create');
    }


}

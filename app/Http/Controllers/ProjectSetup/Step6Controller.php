<?php

namespace App\Http\Controllers\ProjectSetup;

use Validator;
use App\Entity\RoleUser;
use App\Entity\GroupProject;
use App\Entity\GroupContractor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Step6Controller extends Controller
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
        $roleUser = RoleUser::find(session('role_user_id'));

        $data = GroupContractor::where('client_id', $roleUser->client_id)->get();

        $GroupProject = GroupProject::where('project_id', $id)->pluck('group_id')->toArray();

        return view('project.step6', compact('data', 'GroupProject', 'id'));
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
        $id = session('project_id');
        $RoleUser = RoleUser::find(session('role_user_id'));

        if(!$GroupContractor =   GroupContractor::where('client_id', $RoleUser->client_id)->where('id',$request->input('group_id') )->first()){

            return array("errors" => 'Record not found.');
        }


        GroupProject::create([
            'group_id'      => $request->input('group_id'),
            'project_id'    => $id,
        ]);

        return $request->input('group_id');
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
        $rules = [
          'abbreviation_name'       => 'required',
          'display_name'            => 'required',
        ];

        $message = [
            'abbreviation_name.required'     => 'Abbreviation name is required',
            'display_name.required'          => 'Display name is required',
        ];

        $validator = Validator::make($request->input(), $rules, $message);  

        if ($validator->fails()) {
            return back()
              ->withErrors($validator)
              ->withInput();
        }

        $role_user = RoleUser::find(session('role_user_id'));

        if(!$contractor = GroupContractor::where('id', $request->group_id)->where('client_id', $role_user->client_id)->first())
        {
            return back()
              ->withErrors('Record not found.')
              ->withInput();
        }

        $contractor->update([
            'abbreviation_name'         => $request->input('abbreviation_name'),
            'display_name'              => $request->input('display_name'),
            'description'               => $request->input('description'),
        ]);

        return back()->with([ 'success-message' => 'Record successfully updated.' ]);

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        $project_id = session('project_id');
        if(!$GroupProject = GroupProject::where('project_id', $project_id)->where('group_id', $request->input('group_id'))->first()){
            return array("errors" => 'Record not found.');
        }

        GroupProject::where('project_id', $project_id)->where('group_id', $id)->delete();
    }

    public function contractor($id){

        $role_user = RoleUser::find(session('role_user_id'));

        if(!$GroupContractor = GroupContractor::where('id', $id)->where('client_id', $role_user->client_id)->first()){
            return back()
              ->withErrors('Record not found.')
              ->withInput();
        }

        $data = RoleUser::where('role_id', 5)
                        ->with('users')
                        ->where('group_id', $id)
                        ->paginate(20);
        
        return view('project.step6Contractor', compact('data', 'GroupContractor', 'id'));

    }
}

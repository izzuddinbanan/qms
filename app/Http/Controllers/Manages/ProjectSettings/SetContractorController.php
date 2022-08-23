<?php

namespace App\Http\Controllers\Manages\ProjectSettings;

use App\Entity\GroupContractor;
use App\Entity\GroupProject;
use App\Entity\RoleUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class SetContractorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('project-settings.set-contractors.index');
    }

    public function indexData($value='')
    {

        $roleUser = role_user();
        $data = GroupContractor::where('client_id', $roleUser->client_id);
        $GroupProject = GroupProject::where('project_id', session('project_id'))->pluck('group_id')->toArray();

        return Datatables::of($data)
            ->addColumn('is_current', function ($data) use ($GroupProject) {

                if(in_array($data->id, $GroupProject)){
                    return '<a href="" onclick="isCurrent('. $data->id .', true)"><i class="fa fa-check-square-o fa-lg isCurrent-'. $data->id .'"></i></a>';
                }

                return '<a href="" onclick="isCurrent('. $data->id .', false)"><i class="fa fa-square-o fa-lg isCurrent-'. $data->id .'"></i></a>';

            })
            ->addColumn('action', function ($data)  {
                return $button = '<a href="" data-popup="tooltip" title="'. trans('main.edit') .'" data-placement="top" class="edit_button tooltip-show"  onclick="return editForm('. $data->id .')"><i class="fa fa-edit action-icon"></i></a>';
            })
            ->rawColumns(['is_current', 'action'])
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

    public function saveContractorProject(Request $request)
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

    public function destroyContractorProject(Request $request)
    {

        $project_id = session('project_id');
        if(!$GroupProject = GroupProject::where('project_id', $project_id)->where('group_id', $request->input('group_id'))->first()){
            return array("errors" => 'Record not found.');
        }

        GroupProject::where('project_id', $project_id)->where('group_id', $id)->delete();
    }

    public function updateContractor(Request $request)
    {
        $rules = [
          'abbreviation_name'       => 'required',
          'display_name'            => 'required',
        ];

        $message = [
            'abbreviation_name.required'     => 'Abbreviation name is required',
            'display_name.required'          => 'Display name is required',
        ];

        $validator = \Validator::make($request->input(), $rules, $message);  

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
}

<?php

namespace App\Http\Controllers;

use Validator;
use App\Entity\RoleUser;
use App\Entity\GroupContractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class groupController extends Controller
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
        $role_user = RoleUser::find(session('role_user_id'));

        $param = request()->query();
        $search = app('request')->input('search');

        $data = GroupContractor::where('client_id', $role_user->client_id)
                                ->search($search, null, true, true)
                                ->paginate(20);

        return view('contractors.index', compact('data'),[$data->appends(Input::except(array('page')))]);
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

        GroupContractor::create([
            'client_id'                 => $role_user->client_id,
            'abbreviation_name'         => $request->input('abbreviation_name'),
            'display_name'              => $request->input('display_name'),
            'description'               => $request->input('description'),
        ]);

        return back()->with([ 'success-message' => 'New record successfully added.' ]);

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
    public function edit(Request $request, $id)
    {
        $role_user = RoleUser::find(session('role_user_id'));

        if(!$contractor = GroupContractor::where('id', $request->id)->where('client_id', $role_user->client_id)->first()){
            return back()
              ->withErrors('Record not found.')
              ->withInput();
        }

        return $contractor;
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
    public function destroy($id)
    {
        $role_user = RoleUser::find(session('role_user_id'));
        
        if(!$contractor = GroupContractor::where('id', $id)->where('client_id', $role_user->client_id)->first())
        {
            return back()
              ->withErrors('Record not found.')
              ->withInput();
        }

        $contractor->delete();

        return back()->with([ 'success-message' => 'Record successfully deleted.' ]);


    }
}

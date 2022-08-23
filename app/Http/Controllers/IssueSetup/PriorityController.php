<?php

namespace App\Http\Controllers\IssueSetup;

use Auth;
use Validator;
use Session;
use App\Entity\RoleUser;
use App\Entity\SettingPriority;
use App\Entity\PriorityType;
use App\Entity\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Event;

class PriorityController extends Controller
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
        $search = app('request')->input('search');
        
        $role_user = RoleUser::find(session('role_user_id'));

        $data = SettingPriority::search($search, null, true, true)
                ->sortable()
                ->where('client_id', $role_user->client_id)
                ->paginate(20);

        $language = Language::get();
        
        $priorityType = PriorityType::get();

        return view('issue_setups.priority.index', compact('data', 'search', 'priorityType', 'language'), ['page' => $data->appends(Input::except(array('page')))]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $language = Language::get();

        return view('issue_setups.priority.create', compact('language'));
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
          'name'                => 'required',
          'no_of_days'          => 'required|numeric',
          'type'                => 'required|unique:setting_priority,type',
          'no_of_days_notify'   => 'required|numeric|min:0',
        ];

        $message = [
            'no_of_days.required'       => 'No of Days field is required',
            'no_of_days.numeric'        => 'No of Days field only accept numeric only',
            'type.unique'               => 'Name of type has been used',
            'no_of_days_notify.numeric' => 'No of Days Notify field only accept numeric only',
            'no_of_days_notify.min'     => 'No of Days Notify field must be equal or greater than 0',
        ];

        $validator = Validator::make($request->input(), $rules, $message);  

        if ($validator->fails()) {
          return back()
              ->withErrors($validator)
              ->with(["modal" => "show"])
              ->withInput();
        }

        $lang_json = array();
        $Systemlanguage = Language::where('id', '!=', 1)->get();       
        
        foreach ($Systemlanguage as $key => $value) {

            $lang_json[$value->abbreviation_name] = [

                                        'type'  => $request->input('type_lang')[$value->id],
                                        'name'  => $request->input('name_lang')[$value->id],
                                    ];

        } 

        $role_user = RoleUser::find(session('role_user_id'));

        $lang_json = json_encode($lang_json);

        $setting_priority = SettingPriority::create([
            'client_id'         => $role_user->client_id,
            'name'              => $request->input('name'),
            'no_of_days'        => $request->input('no_of_days'),
            'type'              => $request->input('type'),
            'no_of_days_notify' => $request->input('no_of_days_notify'),
            'data_lang'         => $lang_json,
        ]);

        return redirect()->route('setting_priority.show', [$setting_priority->id])->with('success-message', 'New record successfully added.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role_user = RoleUser::find(session('role_user_id'));

        if(!$priority = SettingPriority::where('id', $id)->where('client_id', $role_user->client_id)->first()){
            return redirect()->route('setting_priority.index')->withErrors('Record not found.'); 
        }

        $language = Language::get();

        $priority->data_lang = (array) json_decode($priority->data_lang);

        return view('issue_setups.priority.show', compact('language', 'priority'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {

        $role_user = RoleUser::find(session('role_user_id'));

        $data = SettingPriority::where('client_id', $role_user->client_id )->where('id', $request->id )->first();

        return $data;  
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        if(!$priority = SettingPriority::find($request->priority_id)){
        return back()
            ->withErrors('Record not found.')
            ->withInput(); 
        }


        $rules = [
          'name'                => 'required',
          'no_of_days'          => 'required|numeric',
          'type'                => 'required|unique:setting_priority,type,'.$priority->id,
          'no_of_days_notify'   => 'required|numeric|min:0'
        ];

        $message = [
            'no_of_days.required'       => 'No of Days field is required',
            'no_of_days.numeric'        => 'No of Days field only accept numeric only',
            'no_of_days_notify.numeric' => 'No of Days Notify field only accept numeric only',
            'no_of_days_notify.min'     => 'No of Days Notify field must be equal or greater than 0',
        ];

        $validator = Validator::make($request->input(), $rules, $message);  

        if ($validator->fails()) {
          return back()
              ->withErrors($validator)
              ->with(["modal" => "show"])
              ->withInput();
        }



        $lang_json = array();
        $Systemlanguage = Language::where('id', '!=', 1)->get();       
        
        foreach ($Systemlanguage as $key => $value) {

            $lang_json[$value->abbreviation_name] = [

                                        'type'  => $request->input('type_lang')[$value->id],
                                        'name'  => $request->input('name_lang')[$value->id],
                                    ];

        } 

        $lang_json = json_encode($lang_json);
        
        $priority->update([
            'name'             => $request->input('name'),
            'no_of_days'       => $request->input('no_of_days'),
            'type'             => $request->input('type'),
            'no_of_days_notify'=> $request->input('no_of_days_notify'),
            'data_lang'        => $lang_json,
        ]);
        
        return back()->with('success-message', 'Record successfully updated.');

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

        if(!$priority = SettingPriority::where('id', $id)->where('client_id', $role_user->client_id)->first() ){
          return back()
              ->withErrors('Record not found.')
              ->withInput();
        }

        $priority->delete();

        return back()->with(['success-message' => 'Record successfully deleted.']);
    }
}

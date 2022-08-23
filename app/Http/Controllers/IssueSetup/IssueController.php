<?php

namespace App\Http\Controllers\IssueSetup;

use Auth;
use Validator;
use App\Entity\RoleUser;
use App\Entity\SettingType;
use App\Entity\SettingCategory;
use App\Entity\SettingIssue;
use App\Entity\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Event;

class IssueController extends Controller
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

        $data = SettingIssue::with('type')
                ->search($search, null, true, true)
                ->with('category')
                ->sortable()
                ->where('client_id', $role_user->client_id)
                ->paginate(20);

        return view('issue_setups.issue.index', compact('data', 'search'), ['page' => $data->appends(Input::except(array('page')))]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $language = Language::get();
        $role_user = RoleUser::find(session('role_user_id'));

        $listCat = SettingCategory::where('client_id', $role_user->client_id)->orderBy('name')->with('hasTypes')->get();


        return view('issue_setups.issue.create', compact('language', 'listCat'));
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
          'category'        => 'required',
          'type'            => 'required',
          'issue_lang'      => 'required',
        ];

        $message = [
            'issue_lang.required'    => 'Issue field is required'
        ];

        $validator = Validator::make($request->input(), $rules, $message);  

        if ($validator->fails()) {
          return back()
              ->withErrors($validator)
              ->with(["modal" => "show"])
              ->withInput();
        }

        $Systemlanguage = Language::where('id', '!=', 1)->get();       
        
        foreach ($Systemlanguage as $key => $value) {

            $lang_json[$value->abbreviation_name] = [

                                        'name'  => $request->input('issue_lang')[$value->id],
                                    ];

        } 

        $lang_json = json_encode($lang_json);

        $role_user = RoleUser::find(session('role_user_id'));

        $SettingType = SettingIssue::create([
            'client_id'         => $role_user->client_id,
            'type_id'           => $request->input('type'),
            'category_id'       => $request->input('category'),
            'name'              => $request->input('issue'),
            'data_lang'         => $lang_json,
            'unit_owner'        => $request->input('unit_owner') ? 1: 0,
        ]);

        return redirect()->route('setting_issue.show', [$SettingType->id])->with('success-message', 'New record successfully added.');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        if(!$SettingIssue = SettingIssue::find($id)){
            return redirect()->route('setting_issue.index');

        }

        $SettingIssue->data_lang = (array) json_decode($SettingIssue->data_lang);

        $role_user = RoleUser::find(session('role_user_id'));

        $listCat = SettingCategory::where('client_id', $role_user->client_id)->orderBy('name')->with('hasTypes')->get();

        // dd($listCat);
        foreach ($listCat as $keyCat => $valCat) {

            if($valCat["id"] == $SettingIssue->category_id ){

                $listCat[$keyCat]["selected"] = 'selected';
            }else{
                $listCat[$keyCat]["selected"] = '';
            }


            foreach ($valCat["hasTypes"] as $keyType => $valType) {
                if($valType["id"] == $SettingIssue->type_id ){
                    $valCat["hasTypes"][$keyType]["selected"] = 'selected';
                }else{
                     $valCat["hasTypes"][$keyType]["selected"] = '';
                }
            }

        }

        $language = Language::get();

        return view('issue_setups.issue.show', compact('language', 'listCat', 'SettingIssue'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {

        $issue = SettingIssue::find($request->id);

        $category = SettingCategory::where('client_id', $issue->client_id)->get();

        $type = SettingType::where('client_id', $issue->client_id)->where('category_id', $issue->category_id )->get();
        


        $data = array('type' => $type, 'category' => $category, 'issue' => $issue );

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

        if(!$issue = SettingIssue::find($request->issue_id)){
        return back()
            ->withErrors('Issue not found.')
            ->withInput();
        }

        $Systemlanguage = Language::where('id', '!=', 1)->get();       
        
        foreach ($Systemlanguage as $key => $value) {

            $lang_json[$value->abbreviation_name] = [

                                        'name'  => $request->input('issue_lang')[$value->id],
                                    ];

        } 

        $lang_json = json_encode($lang_json);

        $issue->update([
            'type_id'           => $request->input('type'),
            'category_id'       => $request->input('category'),
            'name'              => $request->input('issue'),
            'data_lang'         => $lang_json,
            'unit_owner'        => $request->input('unit_owner') ? 1: 0,
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

        if(!$issue = SettingIssue::where('id', $id)->where('client_id', $role_user->client_id)->first() ){
          return back()
              ->withErrors('Issue not found.')
              ->withInput();
        }

        $issue->delete();

        return back()->with(['success-message' => 'Record successfully deleted.']);
    }



    public function listCat(){

        $role_user = RoleUser::find(session('role_user_id'));

        $data = SettingCategory::where('client_id', $role_user->client_id)->orderBy('name')->get();

        return $data;

    }

    public function listType(Request $request){

        $cat_id = $request->catID;

        $role_user = RoleUser::find(session('role_user_id'));

        $data = SettingType::where('category_id', $cat_id)->where('client_id', $role_user->client_id)->orderBy('name')->get();

        return $data;

    }
}

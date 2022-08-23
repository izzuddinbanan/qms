<?php

namespace App\Http\Controllers\IssueSetup;

use Auth;
use Validator;
use App\Entity\RoleUser;
use App\Entity\SettingType;
use App\Entity\SettingCategory;
use App\Entity\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Event;

class TypeController extends Controller
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

        $language = Language::get();

        $listCat = SettingCategory::where('client_id', $role_user->client_id)->orderBy('name')->get();

        $data = SettingType::search($search, null, true, true)
                ->with('inCategory')
                ->where('client_id', $role_user->client_id)
                ->sortable()
                ->paginate(20);

        return view('issue_setups.type.index', compact('data', 'search', 'language', 'listCat'), ['page' => $data->appends(Input::except(array('page')))]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $role_user = RoleUser::find(session('role_user_id'));

        $language = Language::get();
        
        $listCat = SettingCategory::where('client_id', $role_user->client_id)->orderBy('name')->get();

        
        return view('issue_setups.type.create', compact('language', 'listCat'));
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
          'category'       => 'required',
          'type_name'       => 'required',
        ];

        $message = [
            'type_name.required'    => 'Name field is required'
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

                                        'name'  => $request->input('type_name_lang')[$value->id],
                                    ];

        } 

        $lang_json = json_encode($lang_json);


        $role_user = RoleUser::find(session('role_user_id'));

        $SettingType = SettingType::create([
            'client_id'         => $role_user->client_id,
            'category_id'       => $request->input('category'),
            'name'              => $request->input('type_name'),
            'data_lang'         => $lang_json,
            'unit_owner'        => $request->input('unit_owner') ? 1: 0,
        ]);

        return redirect()->route('setting_type.show', [$SettingType->id])->with('success-message', 'New record successfully added.');
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

        if(!$type = SettingType::where('id', $id)->where('client_id', $role_user->client_id)->first()){
            return back()->withErrors('Record not found.')->withInput();
        }

        $type->data_lang = (array) json_decode($type->data_lang);

        $language = Language::get();
            
        $listCat = SettingCategory::where('client_id', $role_user->client_id)->orderBy('name')->get();
        foreach ($listCat as $key => $value) {


            if($value["id"] == $type->category_id){
                $listCat[$key]["selected"] = 'selected';

                $cat_lang = $value["data_lang"];
            }else{
                $listCat[$key]["selected"] = '';
            }
        }



        return view('issue_setups.type.show', compact('language', 'listCat','listCatJs', 'type'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {

        $type = SettingType::find($request->id);

        $category = SettingCategory::where('client_id', $type->client_id)->get();


        $cat_lang = "";
        foreach ($category as $key => $value) {

            if($value["id"] == $type->category_id){
                $category[$key]["selected"] = 'selected';

                $cat_lang = $value["data_lang"];
            }else{
                $category[$key]["selected"] = '';
            }
        }

        $data = array('type' => $type, 'category' => $category, 'cat_lang' => $cat_lang );
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
        
        if(!$type = SettingType::find($request->type_id)){
        return back()
            ->withErrors('Record not found.')
            ->withInput();
        }

        $Systemlanguage = Language::where('id', '!=', 1)->get();       

        foreach ($Systemlanguage as $key => $value) {

            $lang_json[$value->abbreviation_name] = [

                                        'name'  => $request->input('type_name_lang')[$value->id],
                                    ];

        } 

        $lang_json = json_encode($lang_json);
        // return $lang_json;
        // return $request;
        $type->update([
            'name'              => $request->input('type_name'),
            'category_id'       => $request->input('category'),
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

        if(!$type = SettingType::where('id', $id)->where('client_id', $role_user->client_id)->first() ){
          return back()
              ->withErrors('Record not found.')
              ->withInput();
        }

        $type->delete();

        return back()->with(['success-message' => 'Record successfully deleted.']);
    }

    public function listCat(){

        $role_user = RoleUser::find(session('role_user_id'));

        $data = SettingCategory::where('client_id', $role_user->client_id)->orderBy('name')->get();

        return $data;

    }

    public function catLang(Request $request){


        $role_user = RoleUser::find(session('role_user_id'));

        $type = SettingCategory::where('id', $request->input('id'))->where('client_id', $role_user->client_id)->first();

        $type->data_lang = $type->data_lang;

        return $type->data_lang;
    }
}

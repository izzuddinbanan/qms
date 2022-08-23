<?php

namespace App\Http\Controllers\IssueSetup;

use Auth;
use Validator;
use App\Entity\RoleUser;
use App\Entity\SettingCategory;
use App\Entity\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Event;


class CategoryController extends Controller
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

        $data = SettingCategory::search($search, null, true, true)
                ->where('client_id', $role_user->client_id)
                ->sortable()
                ->paginate(20);

        return view('issue_setups.category.index', compact('data', 'search', 'language'), ['page' => $data->appends(Input::except(array('page')))]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $language = Language::get();

        return view('issue_setups.category.create', compact('language'));
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
          'name'       => 'required',
        ];

        $message = [];

        $validator = Validator::make($request->input(), $rules, $message);  

        if ($validator->fails()) {
          return back()
              ->withErrors($validator)
              ->withInput();
        }


        $Systemlanguage = Language::where('id', '!=', 1)->get();       
        
        foreach ($Systemlanguage as $key => $value) {

            $lang_json[$value->abbreviation_name] = [

                                        'name'  => $request->input('name_lang')[$value->id],
                                    ];

        } 

        $lang_json = json_encode($lang_json);

        $role_user = RoleUser::find(session('role_user_id'));

        $SettingCategory = SettingCategory::create([
            'client_id'     => $role_user->client_id,
            'name'          => $request->input('name'),
            'data_lang'     => $lang_json,
        ]);


        return redirect()->route('setting_category.show', [$SettingCategory->id])->with('success-message', 'New record successfully added.');
        
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

        if(!$category = SettingCategory::where('id', $id)->where('client_id', $role_user->client_id)->first()){
            return redirect()->route('setting_category.index');
        }


        $category->data_lang = (array) json_decode($category->data_lang);

        $language = Language::get();


        return view('issue_setups.category.show', compact('language', 'category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        
        $data = SettingCategory::find($request->id);

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

        if(!$category = SettingCategory::find($request->category_id)){
        return back()
            ->withErrors('Record not found.')
            ->withInput();
        }


        $Systemlanguage = Language::where('id', '!=', 1)->get();       
        
        foreach ($Systemlanguage as $key => $value) {

            $lang_json[$value->abbreviation_name] = [

                                        'name'  => $request->input('edit_name_lang')[$value->id],
                                    ];

        } 

        $lang_json = json_encode($lang_json);

        $category->update([
            'name'        => $request->input('name'),
            'data_lang'   => $lang_json,
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

        if(!$category = SettingCategory::where('id', $id)->where('client_id', $role_user->client_id)->first() ){
          return back()
              ->withErrors('Record not found.')
              ->withInput();
        }

        $category->delete();

        return back()->with(['success-message' => 'Record successfully deleted.']);

    }
}

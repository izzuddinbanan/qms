<?php

namespace App\Http\Controllers\ProjectSetup;

use AUth;
use Session;
use File;
use Validate;
use Validator;
use App\Entity\User;
use App\Entity\Role;
use App\Entity\RoleUser;
use App\Entity\Project;
use App\Entity\Language;
use App\Entity\HandOverMenu;
use App\Entity\HandOverFormList;
use App\Entity\HandOverFormAcceptance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;


class Step1Controller extends Controller
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

        $forms = HandOverFormList::where('status', true)->where('project_id', $id)->get();

        if(Session::has('project_id')){
            Session::put('project_id', session('project_id') );
            $id = session('project_id');
        }

        if(!$data = Project::find($id)){
            return back()
                ->withErrors('Project not found.')
                ->withInput();
        }

        $client = $data->client;

        $language = Language::get();

        $langSetup = explode(',', $data->language_id);

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

        $data->data_lang = (array) json_decode($data->data_lang);

        return view('project.step1', compact('data', 'language', 'langSetup', 'forms'));
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
        $user = Auth::user();
        
        $rules = [
            'project_name'      => 'required',
            'abbreviation_name' => 'required|max:20',
            // 'contract_no'       => 'numeric|nullable',
        ];

        if($request->input('email_notification_at') == "" && $request->input('email_notification') == 1 ){

            $rules = [
                'project_name'              => 'required|max:255',
                'email_notification_at'     => 'required',
                // 'contract_no'               => 'numeric|nullable',
            ];
        } 

        // $message = ['contract_no.numeric' => 'Contract No field must in numeric only.'  ];
        $message = [];

        $validator = Validator::make($request->input(), $rules, $message);  

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $lang_json = array();
        foreach (json_decode($request->input('language_id')) as $key => $value) {

            if(!$language = Language::find($value)){
                return back()->withInput()->with(['warning-message' => 'Language not found.']);
            }

            if($language->id != 1){

                $destinationPath = public_path('uploads/project_logo/');
                 $input['applogoName'] = null;
                 $input['imagename'] = null;
                                
                if (!empty($request->file('logo_lang')[$language->id])) {
                    
                    $image = $request->file('logo_lang')[$language->id];
                    $input['imagename'] = 'logo_'.time().'.'.$image->getClientOriginalExtension();

                    $image->move($destinationPath, $input['imagename']);

                }

                if (!empty($request->file('app_logo_lang')[$language->id])) {
                    
                    $image = $request->file('app_logo_lang')[$language->id];
                    $input['applogoName'] = 'applogo_'.time().'.'.$image->getClientOriginalExtension();

                    $image->move($destinationPath, $input['applogoName']);

                }

                $lang_json[$language->abbreviation_name] = [

                                        'project_name'  => $request->input('project_name_lang')[$language->id],
                                        'name'                  => $request->input('project_name_lang')[$language->id],
                                        'abbreviation_name'     => $request->input('abbreviation_name_lang')[$language->id],
                                        'contract_no'           => $request->input('contract_no_lang')[$language->id],
                                        'description'           => $request->input('description_lang')[$language->id],
                                        // 'email_notification'    => $request->input('email_notification_lang')[$language->id],
                                        // 'email_notification_at'    => $request->input('email_notification_at_lang')[$language->id],
                                        'logo'                  => $input['imagename'],
                                        'app_logo'              => $input['applogoName'],
                                    ];

            }

        }

        $lang_json = json_encode($lang_json);

        $lang_id = implode(',', json_decode($request->input('language_id')));
        $client_id = RoleUser::where('id', session('role_user_id'))->first();

        $project = Project::create([
            'client_id'             => $client_id->client_id,
            'project_id'            => $request->input('project_id'),
            'name'                  => $request->input('project_name'),
            'abbreviation_name'     => $request->input('abbreviation_name'),
            'contract_no'           => $request->input('contract_no'),
            'description'           => $request->input('description'),
            'language_id'           => $lang_id,
            'data_lang'             => $lang_json,
            'email_notification'    => $request->input('email_notification'),
            'email_notification_at' => $request->input('email_notification_at'),
            'header'                => $request->hasFile('header') ? \App\Processors\SaveTemplatePdfProcessor::make($request->file('header'))->execute() : null,
            'footer'                => $request->hasFile('header') ? \App\Processors\SaveTemplatePdfProcessor::make($request->file('footer'))->execute() : null,

        ]);

        $this->ProjectLogo($request, $project);

        

        // $role = RoleUser::create([
        //     'user_id'               => $user->id,
        //     'role_id'               => 2,
        //     'project_id'            => $project->id,
        //     'client_id'             => $client_id->client_id,
        // ]);

        Session::put('project_id', $project->id);
        
        return redirect()->route('set-drawing.index')->with(['success-message' => 'New project was created. Proceed to the next step.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {   
        
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
    public function update(Request $request)
    {
        $user = Auth::user();
        $id = Session('temp_project_id');
        
        if(Session::has('project_id')){
            $id = session('project_id');

        }

        if(!$project = Project::find($id)){
            return back()
                ->withErrors('Record not found.')
                ->withInput();
        }
        $rules = [
            'project_name'             => 'required',
            'abbreviation_name'        => 'required|max:20',
        ];
        if($request->input('email_notification_at') == "" && $request->input('email_notification') == 1 ){

            $rules = [
            'project_name'              => 'required',
            'email_notification_at'     => 'required',
            ];
        } 

        $message = [
        ];

        $validator = Validator::make($request->input(), $rules, $message);  

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        foreach (json_decode($request->input('language_id')) as $key => $value) {

            if(!$language = Language::find($value)){
                return back()->withInput()->with(['warning-message' => 'Language not found.']);
            }

            if($language->id != 1){
                
                $destinationPath = public_path('uploads/project_logo/');
                $input['applogoName'] = null;
                $input['imagename'] = null;
                                
                if (!empty($request->file('logo_lang')[$language->id])) {
                    
                    $image = $request->file('logo_lang')[$language->id];
                    $input['imagename'] = 'logo_'.time().'.'.$image->getClientOriginalExtension();

                    $image->move($destinationPath, $input['imagename']);

                }

                if (!empty($request->file('app_logo_lang')[$language->id])) {
                    
                    $image = $request->file('app_logo_lang')[$language->id];
                    $input['applogoName'] = 'applogo_'.time().'.'.$image->getClientOriginalExtension();

                    $image->move($destinationPath, $input['applogoName']);

                }


                $multiLangData = [

                                    'name'                  => $request->input('project_name_lang')[$language->id],
                                    'description'           => $request->input('description_lang')[$language->id],
                                    'abbreviation_name'     => $request->input('abbreviation_name_lang')[$language->id],
                                    'contract_no'           => $request->input('contract_no_lang')[$language->id],
                                    'description'           => $request->input('description_lang')[$language->id],
                                    // 'email_notification'    => $request->input('email_notification_lang')[$language->id],
                                    // 'email_notification_at' => $request->input('email_notification_at_lang')[$language->id],
                                    'logo'                  => $input['imagename'],
                                    'app_logo'              => $input['applogoName'],
                                ];

                $lang_json[$language->abbreviation_name] = $multiLangData;

            }

        }

        // $lang_json = json_encode($lang_json);

        $lang_id = implode(',', json_decode($request->input('language_id')));

        $project->update([
            'name'                  => $request->input('project_name'),
            'project_id'            => $request->input('project_id'),
            'abbreviation_name'     => $request->input('abbreviation_name'),
            'contract_no'           => $request->input('contract_no'),
            'description'           => $request->input('description'),
            // 'data_lang'             => $lang_json,
            'email_notification'    => $request->input('email_notification'),
            'email_notification_at' => $request->input('email_notification_at'),
        ]);

        if($request->input('acceptance_form'))
        {
            $project->update([
                'acceptance_form' => $request->input('acceptance_form'),
            ]);
        }

        // return \App\Processors\SaveTemplatePdfProcessor::make($request->file('header'))->execute();

        $destinationPath = public_path('uploads/template-pdf/');
        if($request->hasFile('header')) {

            File::delete($destinationPath .  $project->header);

            $project->forcefill(['header'   => \App\Processors\SaveTemplatePdfProcessor::make($request->file('header'))->execute()])->save();

        }


        if($request->hasFile('footer')) {

            File::delete($destinationPath .  $project->footer);

            $project->forcefill(['footer'   => \App\Processors\SaveTemplatePdfProcessor::make($request->file('footer'))->execute()])->save();

        }

        $this->ProjectLogo($request, $project);

        return back()->with(['success-message' => 'Record successfully updated.']);
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


    // UPLOAD PROJECT LOGO TO STORAGE
    function ProjectLogo($request, $project){

        $destinationPath = public_path('uploads/project_logo/');
        if (!empty($request->file('logo'))) {

            if ($project->logo != null) {
                File::delete($destinationPath .  $project->logo);
                // File::delete($destinationPath .  'thumbnail_'.$project->logo);
            }
            
            $image = $request->file('logo');
            $input['imagename'] = 'logo_'.time().'.'.$image->getClientOriginalExtension();

            // $resize_image =  Image::make($image)->resize(550, 550);
            $image->move($destinationPath, $input['imagename']);
            // $resize_image->save($destinationPath .'thumbnail_' . $input['imagename']);


            $project->forcefill(['logo' => $input['imagename']])->save();
        }

        if (!empty($request->file('app_logo'))) {

            if ($project->app_logo != null) {
                File::delete($destinationPath .  $project->app_logo);
            }
            
            $applogo = $request->file('app_logo');
            $input['imagename'] = 'applogo_'.time().'.'.$applogo->getClientOriginalExtension();
            $applogo->move($destinationPath, $input['imagename']);
            $project->forcefill(['app_logo' => $input['imagename']])->save();
        }

    }

    public function chooseLangSetup(Request $request){
        $id = session('project_id');
        $lang = array();
        Session::forget('langSetup');

        if($request->input('lang') == null){
            
            $lang = [1];
            Session::put('langSetup', $lang);

        }else{


            foreach ($request->input('lang') as $key => $value) {

                array_push($lang, $value);
            }
                array_push($lang, 1);
                Session::put('langSetup', $lang);

        }
            $project = Project::find($id);
            
            $lang_id = implode(',', session('langSetup'));
            $project->update(['language_id' => $lang_id]);

        return redirect()->route('step1.index');
    }


    function multipleLanguageStore(){
        
    }
}

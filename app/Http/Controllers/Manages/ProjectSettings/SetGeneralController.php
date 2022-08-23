<?php

namespace App\Http\Controllers\Manages\ProjectSettings;

use App\Entity\HandOverFormAcceptance;
use App\Entity\HandOverMenu;
use App\Entity\HandoverFormWaiver;
use App\Entity\Language;
use App\Entity\Project;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;

class SetGeneralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if(session('project_id')) {
            return redirect()->route('set-general.show', [session('project_id')]);
        }

        if(!session('langSetup')){
            Session::put('langSetup', [1]);
        }
        return view('project-settings.set-generals.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try {

            $rules = [
                'name.1'      => 'required',
                'abv.1'       => 'required|max:10',
                'logo.*'      => 'image|mimes:jpg,png,jpeg',
                'app_logo.*'  => 'image|mimes:jpg,png,jpeg',
                'header'      => 'image|mimes:jpg,png,jpeg',
                'footer'      => 'image|mimes:jpg,png,jpeg',
                'notify_at.1' => 'required_if:email_notify.1,1',
            ];

            $message = [
                'name.1.required'   => 'Project name is required.',
                'abv.1.required'    => 'Abbreviation name is required.',
                'abv.1.max'         => 'Abbreviation name must not more than 10 characters.',
                'notify_at.1.required_if' => 'Notify at field is required.',
            ];

          
            $this->validate($request, $rules, $message);

            $project_language_str = implode(',', session('langSetup'));

            $lang_json = [];
            foreach (session('langSetup') as $key => $value) {

                if($value != 1) {

                    $language = Language::find($value);
                    $lang_json[$language->abbreviation_name] = (object)[
                        'name'                  => $request->input('name')[$value],
                        'abbreviation_name'     => $request->input('abv')[$value],
                        'contract_no'           => $request->input('contract')[$value],
                        'description'           => $request->input('description')[$value],
                    ];

                }
            }

            $role_user = role_user();

            $project = Project::create([
                'project_id'            => $request->input('project_id'),
                'client_id'             => $role_user->client_id,
                'name'                  => $request->input('name')[1],
                'abbreviation_name'     => $request->input('abv')[1],
                'contract_no'           => $request->input('contract')[1],
                'description'           => $request->input('description')[1],
                'language_id'           => implode(',', session('langSetup')),
                'data_lang'             => $lang_json,
                'logo'                  => $request->file('logo')[1] ? \App\Processors\SaveProjectLogoProcesscor::make($request->file('logo')[1])->execute() : null,
                'app_logo'              => $request->file('app_logo')[1] ? \App\Processors\SaveProjectLogoProcesscor::make($request->file('app_logo')[1])->execute() : null,
                'email_notification'    => $request->input('email_notify')[1] ?? null,
                'email_notification_at' => $request->input('notify_at')[1] ?? null,
                'header'                => $request->hasFile('header') ? \App\Processors\SaveTemplatePdfProcessor::make($request->file('header'))->execute() : null,
                'footer'                => $request->hasFile('header') ? \App\Processors\SaveTemplatePdfProcessor::make($request->file('footer'))->execute() : null,
            ]);


            ## CREATE SAMPLE DATA ##
            $this->firstCreateProjectData($project);
            ## CREATE SAMPLE DATA ##



        } catch (ValidationException $e) {
            return redirect(route('set-general.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        Session::put('project_id', $project->id);

        return redirect()->route('set-drawing-set.index')->with(['success-message' => 'New record successfully added.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $role_user = role_user();


        if($role_user->role_id == 2){
            $project = Project::where('id', $id)->where('client_id', $role_user->client_id )->first();
        }

        // admin
        if($role_user->role_id == 3){
            
            $projectUser = RoleUser::where('user_id', $role_user->user_id)->where('client_id', $role_user->client_id)->where('project_id', $id)->first();
            $project = Project::where('id', $id)->where('client_id', $role_user->client_id )->first();
        }

        if(!$project){
            return redirect()->route('project.create')->with(['warning-message' => 'Record not found.']);

        }

        Session::put('project_id', $project->id);

        $project->language_id = explode(',', $project->language_id);
        $language = Language::whereIn('id', $project->language_id)->get();

        return view('project-settings.set-generals.show', compact('project', 'language'));

  
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
        try {

            $rules = [
                'name.1'      => 'required',
                'abv.1'       => 'required|max:10',
                'logo.*'      => 'image|mimes:jpg,png,jpeg|',
                'app_logo.*'  => 'image|mimes:jpg,png,jpeg|',
            ];

            $message = [
                'name.1.required'   => 'Project name is required.',
                'abv.1.required'    => 'Abbreviation name is required.',
                'abv.1.max'         => 'Abbreviation name must not more than 10 characters.',
            ];

            $this->validate($request, $rules, $message);

            if(!$project = Project::find(session('project_id'))){
                return redirect()->route('project.index')->with(['warning-message' => 'Record not found.']);
            }

            if($request->input('email_notify')[1] && !$request->input('notify_at')[1]){
                return back()->with(['warning-message' => 'Email notification at is required']);
            }

            $lang_json = [];
            foreach (explode(',', $project->language_id) as $key => $value) {

                if($value != 1) {

                    $language = Language::find($value);
                    $lang_json[$language->abbreviation_name] = (object)[
                        'name'                  => $request->input('name')[$value],
                        'abbreviation_name'     => $request->input('abv')[$value],
                        'contract_no'           => $request->input('contract')[$value],
                        'description'           => $request->input('description')[$value],
                    ];

                }
            }

            $project->update([
                'project_id'            => $request->input('project_id'),
                'name'                  => $request->input('name')[1],
                'abbreviation_name'     => $request->input('abv')[1],
                'contract_no'           => $request->input('contract')[1],
                'description'           => $request->input('description')[1],
                'data_lang'             => $lang_json,
                'email_notification'    => $request->input('email_notify')[1] ?? null,
                'email_notification_at' => $request->input('notify_at')[1] ?? null,
            ]);


            if ($request->file('logo')[1]) {
                $logo = \App\Processors\SaveProjectLogoProcesscor::make($request->file('logo')[1])->execute();

                if ($project->logo) {
                    \File::delete('uploads/project_logo/' .  $project->logo);
                }

                $project->forcefill(['logo' => $logo])->save();

            }

            if ($request->file('app_logo')[1]) {
                $app_logo = \App\Processors\SaveProjectLogoProcesscor::make($request->file('app_logo')[1])->execute();

                if ($project->app_logo) {
                    \File::delete('uploads/project_logo/' .  $project->app_logo);
                }

                $project->forcefill(['app_logo' => $app_logo])->save();
            }


            if ($request->hasFile('header')) {
                $header = \App\Processors\SaveTemplatePdfProcessor::make($request->file('header'))->execute();

                if ($project->header) {
                    \File::delete('uploads/template-pdf/' .  $project->header);
                }

                $project->forcefill(['header' => $header])->save();
            }

            if ($request->hasFile('footer')) {
                $footer = \App\Processors\SaveTemplatePdfProcessor::make($request->file('footer'))->execute();

                if ($project->footer) {
                    \File::delete('uploads/template-pdf/' .  $project->footer);
                }

                $project->forcefill(['footer' => $footer])->save();
            }


        } catch (ValidationException $e) {
            return redirect(route('set-general.show', [$project->id]))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect()->route('set-general.show', [$project->id])->with(['success-message' => 'Record successfully updated.']);
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

    public function setLang(Request $request){

        if(session('project_id')) {
                
            $project = Project::find(session('project_id'));
    

            if(count($request->input('lang')) > 0){
    
                $project->language_id = '1,' . implode(',', $request->input('lang'));
            }else {
                $project->language_id = 1;
            }

            $project->save();

            return back();

        }


        $lang = array();
        Session::forget('langSetup');


        if(count($request->input('lang')) > 0){
            foreach ($request->input('lang') as $key => $value) {

                if(!Language::find($value)) {
                    return back()->with(['warning-message' => 'language not found.']);
                }
                array_push($lang, $value);
            }
        }
        array_push($lang, 1);
        Session::put('langSetup', $lang);

        return back();
    }


    function firstCreateProjectData($project){

        $handover_menu = [
            'key'           => 'Key',
            'es'            => 'E/S',
            'waiver'        => 'Waiver',
            'photo'         => 'Photo',
            'acceptance'    => 'Acceptance',
            'survey'        => 'Survey',
        ];

        foreach ($handover_menu as $key => $value) {
            $handover_menu = HandOverMenu::create([
                'original_name'         => $key,
                'display_name'          => $value,
                'show'                  => "yes",
                'field_mandatory'       => "yes",
                'project_id'            => $project->id,
            ]);
        }

        $handover_menu = HandOverMenu::create([
            'original_name'         => "waiver",
            'display_name'          => "Waiver",
            'show'                  => "no",
            'field_mandatory'       => "no",
            'project_id'            => $project->id,
        ]);

        $handover_acceptance = HandOverFormAcceptance::create([
            'termsConditions'   => '<p>We hereby acknowledge receipt of all items as per the Checklist.</p>',
            'project_id'        => $project->id,
            'status'            => 'Active',
        ]);

        $handover_form_waiver  = HandoverFormWaiver::create([
            'project_id'        => $project->id,
            'description'       => '<p>Insert waiver text here...</p>',
        ]);
    }
}

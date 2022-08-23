<?php

namespace App\Http\Controllers\Manages\Survey;

use File, Session;
use App\Entity\Project;
use App\Entity\HandOverMenu;
use App\Entity\HandoverFormSurvey;
use App\Entity\HandoverFormSurveyVersion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $project_id = session('project_id');

        $survey_version = HandoverFormSurveyVersion::where('project_id', $project_id)->orderBy('created_at', 'desc')->get();

        $handover_menu = HandOverMenu::where('project_id', $project_id)->where('original_name', 'survey')->first();
        
        $survey = HandoverFormSurvey::where('project_id', $project_id)->where('status', 'Active')->get();

        return view('survey.index', compact('survey', 'survey_version', 'handover_menu'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $survey = (object)[];
        return view('survey.create', compact('survey'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $project_id = session('project_id');

        $handover_menu = HandOverMenu::where('project_id', $project_id)->where('original_name', 'survey')->first();

        $survey_version = HandoverFormSurveyVersion::where('project_id', $project_id)->where('id', $id)->first();

        $survey = HandoverFormSurvey::where('project_id', $project_id)->where('handover_form_survey_id', $id)->get();

        return view('survey.show', compact('survey', 'survey_version','handover_menu'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $project_id = session('project_id');

        $survey_version_id = $id;

        $survey = HandoverFormSurvey::where('project_id', $project_id)->where('handover_form_survey_id', $id)->get();

        return view('survey.edit', compact('survey', 'survey_version_id'));
    }

    public function store(Request $request)
    {
        $project_id = session('project_id');
        
        $handover_version = HandoverFormSurveyVersion::create([
            'project_id'    => $project_id,
            'status'        => "Draft", 
        ]);

        $handover_version->save();

        for($i = 0; $i < count($request->input('question')); $i++) {

                HandoverFormSurvey::create([
                    'question'                  => $request->input('question')[$i],
                    'sequence'                  => $i+1,
                    'type'                      => $request->input('type_survey')[$i],
                    'project_id'                => $project_id,
                    'status'                    => 'Active',
                    'handover_form_survey_id'   => $handover_version->id,
                ]);
            
        }

        return redirect()->route('survey.index')->with(['success-message' => 'Survey successfully updated.']);
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
        $project_id = session('project_id');

        $survey = HandoverFormSurvey::where('handover_form_survey_id', $id)->get();

        if(count($survey) > 0)
        {
            foreach($survey as $s)
            {
                $s->delete();
            }
        }

        for($i = 0; $i < count($request->input('question')); $i++) {

                HandoverFormSurvey::create([
                    'question'                  => $request->input('question')[$i],
                    'sequence'                  => $i+1,
                    'type'                      => $request->input('type_survey')[$i],
                    'project_id'                => $project_id,
                    'status'                    => 'Active',
                    'handover_form_survey_id'   => $id,
                ]);
            
        }

        return redirect()->route('survey.index')->with(['success-message' => 'Survey successfully updated.']);
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

    public function publish($id)
    {
        $project_id = session('project_id');

        $current_publish_version_count = HandoverFormSurveyVersion::where('project_id', $project_id)->where('status', 'Publish')->count();
        if($current_publish_version_count>0)
        {
            $current_publish_version = HandoverFormSurveyVersion::where('project_id', $project_id)->where('status', 'Publish')->first();
            $current_publish_version->status="Expired";
            $current_publish_version->save();  
        } 
        
        $countHandoverVersion = HandoverFormSurveyVersion::where('project_id', $project_id)->whereNotNull('version')->count();
        $new_survey_version = HandoverFormSurveyVersion::where('project_id', $project_id)->where('id', $id)->where('status', 'Draft')->first();

        $new_survey_version->version = $countHandoverVersion + 1;
        $new_survey_version->status = "Publish";
        $new_survey_version->save(); 

        return redirect()->route('survey.index')->with(['success-message' => 'Survey successfully published.']);
    }

}

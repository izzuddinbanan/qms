<?php

namespace App\Http\Controllers\Manages\Handover;

use File, Session;
use App\Entity\HandOverMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HandoverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $project_id = session('project_id');
        $key = HandOverMenu::where('project_id', $project_id)->where('original_name', 'key')->first();
        $es = HandOverMenu::where('project_id', $project_id)->where('original_name', 'es')->first();
        $waiver = HandOverMenu::where('project_id', $project_id)->where('original_name', 'waiver')->first();
        $photo = HandOverMenu::where('project_id', $project_id)->where('original_name', 'photo')->first();
        $acceptance = HandOverMenu::where('project_id', $project_id)->where('original_name', 'acceptance')->first();
        $survey = HandOverMenu::where('project_id', $project_id)->where('original_name', 'survey')->first();

        return view('handover.index', compact('key','es','waiver','photo','acceptance','survey'));
    }

    public function editHandover(Request $request)
    {
        $project_id = session('project_id');

        if($request->has('key_submit'))
        {
            $handover_form_list = HandOverMenu::where('original_name', 'key')->where('project_id', $project_id)->first();
            $handover_form_list->display_name=$request->input('display_name');
            $handover_form_list->field_mandatory = $request->input('key') == "on" ? "yes" : "no";
            $handover_form_list->save();
        }

        if($request->has('es_submit'))
        {
            $handover_form_list = HandOverMenu::where('original_name', 'es')->where('project_id', $project_id)->first();
            $handover_form_list->display_name=$request->input('display_name');
            $handover_form_list->field_mandatory = $request->input('es') == "on" ? "yes" : "no";
            $handover_form_list->save();
        }

        if($request->has('waiver_submit'))
        {
            // dd($request->input());
            $handover_form_list = HandOverMenu::where('original_name', 'waiver')->where('project_id', $project_id)->first();
            $handover_form_list->display_name=$request->input('display_name');
            $handover_form_list->field_mandatory=$request->input('waiver_field_mandatory');
            $handover_form_list->field_mandatory = $request->input('waiver') == "on" ? "yes" : "no";
            $handover_form_list->save();
        }

        if($request->has('photo_submit'))
        {
            $handover_form_list = HandOverMenu::where('original_name', 'photo')->where('project_id', $project_id)->first();
            $handover_form_list->display_name=$request->input('display_name');
            $handover_form_list->field_mandatory = $request->input('photo') == "on" ? "yes" : "no"; 
            $handover_form_list->save();
        }

        if($request->has('acceptance_submit'))
        {
            $handover_form_list = HandOverMenu::where('original_name', 'acceptance')->where('project_id', $project_id)->first();
            $handover_form_list->display_name = $request->input('display_name');
            $handover_form_list->field_mandatory = $request->input('acceptance') == "on" ? "yes" : "no"; 
            $handover_form_list->save();
        }

        if($request->has('survey_submit'))
        {
            $handover_form_list = HandOverMenu::where('original_name', 'survey')->where('project_id', $project_id)->first();
            $handover_form_list->display_name = $request->input('display_name');
            $handover_form_list->field_mandatory = $request->input('survey') == "on" ? "yes" : "no";
            $handover_form_list->save();
        }
        
        return redirect()->route('handover.index')->with(['success-message' => 'Setting successfully updated.']);
    }

    public function editHandoverSetting(Request $request)
    {
        $project_id = session('project_id');

        $key = HandOverMenu::where('project_id', $project_id)->where('original_name', 'key')->first();
        $es = HandOverMenu::where('project_id', $project_id)->where('original_name', 'es')->first();
        $waiver = HandOverMenu::where('project_id', $project_id)->where('original_name', 'waiver')->first();
        $photo = HandOverMenu::where('project_id', $project_id)->where('original_name', 'photo')->first();
        $acceptance = HandOverMenu::where('project_id', $project_id)->where('original_name', 'acceptance')->first();
        $survey = HandOverMenu::where('project_id', $project_id)->where('original_name', 'survey')->first();

        if($request->has('key') && $request->input('key')=="on")
        {
            $key->show = "yes";
            $key->save();
        }
        else
        {
            $key->show = "no";
            $key->save();
        }

        if($request->has('es') && $request->input('es')=="on")
        {
            $es->show = "yes";
            $es->save();
        }
        else
        {
            $es->show = "no";
            $es->save();
        }

        if($request->has('waiver') && $request->input('waiver')=="on")
        {
            $waiver->show = "yes";
            $waiver->save();
        }
        else
        {
            $waiver->show = "no";
            $waiver->save();
        }

        if($request->has('photo') && $request->input('photo')=="on")
        {
            $photo->show = "yes";
            $photo->save();
        }
        else
        {
            $photo->show = "no";
            $photo->save();
        }

        if($request->has('acceptance') && $request->input('acceptance')=="on")
        {
            $acceptance->show = "yes";
            $acceptance->save();
        }
        else
        {
            $acceptance->show = "no";
            $acceptance->save();
        }

        if($request->has('survey') && $request->input('survey')=="on")
        {
            $survey->show = "yes";
            $survey->save();
        }
        else
        {
            $survey->show = "no";
            $survey->save();
        }

        return redirect()->route('handover.index')->with(['success-message' => 'Handover Setting successfully updated.']);
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

}

<?php

namespace App\Http\Controllers\Manages\Acceptance;

use File, Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Entity\HandOverMenu;
use App\Entity\HandOverFormAcceptance;

class AcceptanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $project_id = session('project_id');
        $termsConditions = HandOverFormAcceptance::where('project_id', $project_id)->where('status', 'Active')->first();
        $handover_menu = HandOverMenu::where('project_id', $project_id)->where('original_name', 'acceptance')->first();
        return view('acceptance.index', compact('handover_menu', 'termsConditions'));
    }

    public function editTermsConditions(Request $reqeust)
    {   
        $project_id = session('project_id');
        $termsConditions = HandOverFormAcceptance::where('project_id', $project_id)->where('status', 'Active')->first();
        $handover_menu = HandOverMenu::where('project_id', $project_id)->where('original_name', 'acceptance')->first();

        return view('acceptance.editTermsConditions', compact('handover_menu', 'termsConditions'));
    }

    public function updateTermsConditions(Request $request)
    {
        $project_id = session('project_id');
        if($request->input('content') != null && $request->input('content') != "" && $request->input('designation') != null && $request->input('designation') != "")
        {
            $handoverFormAcceptance = HandOverFormAcceptance::where('project_id', $project_id)->where('status', 'Active')->first();
            $handoverFormAcceptance->status="Inactive";    
            $handoverFormAcceptance->save();

            $new_handoverFormAcceptance = HandOverFormAcceptance::create([
                "termsConditions"   => $request->input('content'),
                "designation"       => $request->input('designation'),
                "project_id"        => $project_id,
                "status"            => "Active",
            ]);

            return redirect()->route('acceptance.index')->with(['success-message' => 'Terms & Condition successfully updated.']);
        }
        else
        {
            return redirect()->route('acceptance.editTermsConditions')->withErrors('Terms & Conditions cannot be blank.');
        }
        

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

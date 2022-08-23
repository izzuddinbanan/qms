<?php

namespace App\Http\Controllers\Board;

use Carbon\Carbon;
use App\Entity\Project;
use App\Entity\DrawingSet;
use App\Entity\DrawingPlan;
use App\Entity\HandOverMenu;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;

class HandoverReportController extends Controller
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
    public function index(Request $request)
    {
        $project = Project::where('id', session('project_id'))->first();

        $drawing_set = DrawingSet::where('project_id', $project->id)->select('id')->get();
        $drawing_plan = DrawingPlan::whereIn('drawing_set_id', $drawing_set)->where('types', 'unit')->get();

        return view('handover-reports.index')->with('drawing_plan', $drawing_plan);
    }

    public function indexData()
    {
        $project = Project::where('id', session('project_id'))->first();

        $drawing_set = DrawingSet::where('project_id', $project->id)->select('id')->get();
        $drawing_plan = DrawingPlan::whereIn('drawing_set_id', $drawing_set)->where('types', 'unit')->select(['drawing_plans.*']);

        return Datatables::of($drawing_plan)
            ->addColumn('status-hand-over', function ($plan) {
                
                if($plan->handover_status == "handed over"){
                    $handover_status = '<p style="color:green; font-weight:bold;">Handed Over</p>';
                }
                else{
                    if($plan->ready_to_handover){
                        $handover_status = '<p style="color:blue; font-weight:bold;">Ready To Handover</p>';
                    }
                    else{
                        $handover_status = '<p style="color:red; font-weight:bold;">Not Handed Over</p>';
                    }
                }

                return $handover_status;
            })
            ->addColumn('handover-date', function ($plan) {
                
                if($plan->handover_date != null){
                    $handover_date = $plan->handover_date;
                }
                else{
                    $handover_date = 'N/A';
                }

                return $handover_date;
            })
            ->addColumn('spa-date', function ($plan) {
                
                if($plan->spa_date != null){
                    $spa_date = $plan->spa_date;
                }
                else{
                    $spa_date = 'N/A';
                }

                return $spa_date;
            })
            ->addColumn('vp-date', function ($plan) {
                
                if($plan->vp_date != null){
                    $vp_date = $plan->vp_date;
                }
                else{
                    $vp_date = 'N/A';
                }

                return $vp_date;
            })
            ->addColumn('dlp-expiry-date', function ($plan) {
                
                if($plan->dlp_expiry_date != null){
                    $dlp_expiry_date = $plan->dlp_expiry_date;
                }
                else{
                    $dlp_expiry_date = 'N/A';
                }

                return $dlp_expiry_date;
            })
            ->addColumn('action', function ($plan){

                if($plan->handover_status=="not handed over" && $plan->ready_to_handover == 1)
                {
                    $button = '<a href="'. route('handover.handover', [$plan->id]) .'" data-popup="tooltip" title="Handover" data-placement="top" class="edit_button">
                                <i class="fa fa-copy fa-lg"></i>
                            </a>';    
                }
                else
                {
                    $button = '';
                }
                


                return $button;
            })
            ->rawColumns(['status-hand-over','handover-date', 'spa-date', 'vp-date', 'dlp-expiry-date', 'action'])
            ->make(true);
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

    public function handover($id)
    {
        $project = Project::where('id', session('project_id'))->first();
        $drawing_plan = DrawingPlan::where('id', $id)->first();
        if($drawing_plan->drawingSet->project->id != $project->id)
        {
            return back()->withErrors('Invalid Unit.');
        }
        else{
            $handover_menu_key = HandOverMenu::where('project_id', $project->id)->where('original_name', 'key')->first();
            $handover_menu_es = HandOverMenu::where('project_id', $project->id)->where('original_name', 'es')->first();
            $handover_menu_waiver = HandOverMenu::where('project_id', $project->id)->where('original_name', 'waiver')->first();
            $handover_menu_photo = HandOverMenu::where('project_id', $project->id)->where('original_name', 'photo')->first();
            $handover_menu_acceptance = HandOverMenu::where('project_id', $project->id)->where('original_name', 'acceptance')->first();
            $handover_menu_survey = HandOverMenu::where('project_id', $project->id)->where('original_name', 'survey')->first();

            $key = $drawing_plan->drawingSet->keyForm->section;   
            $es = $drawing_plan->drawingSet->esForm->section;
            $waiver = $drawing_plan->drawingSet->project->HandoverFormWaiver;
        }
        // dd($waiver);
        return view('handover-reports.handover', compact('handover_menu_key', 'handover_menu_es', 'handover_menu_waiver', 'handover_menu_photo', 'handover_menu_acceptance', 'handover_menu_survey', 'key', 'es', 'waiver'));
    }

}

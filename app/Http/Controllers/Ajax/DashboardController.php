<?php

namespace App\Http\Controllers\Ajax;

use Session;
use App\Entity\Project;
use App\Entity\DrawingPlan;
use App\Entity\DrawingSet;
use App\Entity\LocationPoint;
use App\Entity\Issue;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;


class DashboardController extends Controller
{

    public function dashboardIssue(Request $request){

        $project = Project::find($request->input('id'));


        $drawingSet  = DrawingSet::where('project_id', $project->id)->select('id')->get();
        $drawingPlan = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->select('id')->get();
        $location    = LocationPoint::whereIn('drawing_plan_id', $drawingPlan)->select('id')->get();

        $issue = Issue::whereIn('location_id', $location);

        return [
                    'new'           => Issue::whereIn('location_id', $location)->new()->count(),
                    'wip'           => Issue::whereIn('location_id', $location)->wip()->count(),
                    'pending'       => Issue::whereIn('location_id', $location)->pendingStart()->count(),
                    'void'          => Issue::whereIn('location_id', $location)->reject()->count(),
                    'completed'     => Issue::whereIn('location_id', $location)->complete()->count(),
                    'closed'        => Issue::whereIn('location_id', $location)->closed()->count(),
                ];
    }
}

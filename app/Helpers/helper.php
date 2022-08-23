<?php

// use RoleUser;
namespace App\Helpers;
use Auth;
use App\Entity\Notification;
use App\Entity\Project;
use App\Entity\RoleUser;
use App\Entity\LocationPoint;
use App\Entity\Issue;
use App\Entity\DrawingSet;
use App\Entity\DrawingPlan;
use App\Entity\Language;

class Helper {
    
    public static function list_client() 
    {	
    	##get user
    	$user_id = \App\Entity\RoleUser::where('id', session('role_user_id'))->first();

    	##get all client
        $role = \App\Entity\RoleUser::with('roles')->with('clients')->where('user_id', $user_id->user_id )->groupBy('role_id', 'client_id')->get();

    	return $role;

    }


    public static function curret_client() 
    {
        $role = \App\Entity\RoleUser::with('roles')->with('clients')->where('id', session('role_user_id'))->first();

    	return $role;

    }


    public static function generateIssueReferenceByLocationID($location_id) {
        $last = Issue::where('location_id', $location_id)->count();

        $client_detail = LocationPoint::where('locations.id', $location_id)
            ->join('drawing_plans', 'drawing_plans.id', 'locations.drawing_plan_id')
            ->join('drawing_sets', 'drawing_sets.id', 'drawing_plans.drawing_set_id')
            ->join('projects', 'projects.id', 'drawing_sets.project_id')
            ->join('clients', 'clients.id', 'projects.client_id')
            ->select([
                'clients.abbreviation_name as client_name',
                'projects.abbreviation_name as project_name',
                'projects.id as project_id',
                'drawing_plans.types as types',
                'drawing_plans.block as block',
                'drawing_plans.level as level',
                'drawing_plans.unit as unit',
            ])
            ->first();


        $drawingSet = DrawingSet::where('project_id', $client_detail->project_id)->withTrashed()->select('id')->get();
        $drawingPlan = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->withTrashed()->select('id')->get();
        $location = LocationPoint::whereIn('drawing_plan_id', $drawingPlan)->withTrashed()->select('id')->get();
        $issue = Issue::whereIn('location_id', $location)->withTrashed()->get();


        // $reference = "{$client_detail->client_name}-{$client_detail->project_name}-{$client_detail->block}-{$client_detail->level}-{$client_detail->unit}-L{$location_id}R{$last}";

        $reference = "{$client_detail->client_name}-{$client_detail->project_name}-" . ucwords(substr($client_detail->types, 0, 1)) . count($issue);

        $ref_arr = Issue::whereIn('location_id', $location)->withTrashed()->pluck('reference')->toArray();

        while(in_array($reference, $ref_arr)){

            $reference = "{$client_detail->client_name}-{$client_detail->project_name}-" . ucwords(substr($client_detail->types, 0, 1)) . (count($issue) + 1);

        }
        
        return strtoupper($reference);
    }
}


<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Traits\PushNotification;
use Carbon\Carbon;

class NotifyUserIssues extends Command
{
    use PushNotification;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:issues';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify Contractors or Inspector when they dint response to the issues';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $issues_group = \DB::table('issues')
            ->leftJoin('users as inpectors', function ($join) {
                $join->on('inpectors.id', '=', 'issues.inspector_id')
                    ->where('inpectors.deleted_at', null);
            })
            ->leftJoin('role_user', function ($join) {
                $join->on('role_user.group_id', '=', 'issues.group_id')
                    ->where('role_user.role_id', 5)
                    ->where('role_user.deleted_at', null);
            })
            ->leftJoin('users as contractors', function ($join) {
                $join->on('contractors.id', '=', 'role_user.user_id')
                    ->where('contractors.deleted_at', null);
            })
            ->leftJoin('locations', function ($join) {
                $join->on('locations.id', '=', 'issues.location_id')
                    ->where('locations.deleted_at', null);
            })
            ->leftJoin('drawing_plans', function ($join) {
                $join->on('drawing_plans.id', '=', 'locations.drawing_plan_id')
                    ->where('drawing_plans.deleted_at', null);
            })
            ->leftJoin('drawing_sets', function ($join) {
                $join->on('drawing_sets.id', '=', 'drawing_plans.drawing_set_id')
                    ->where('drawing_sets.deleted_at', null);
            })
            ->leftJoin('projects', function ($join) {
                $join->on('projects.id', '=', 'drawing_sets.project_id')
                    ->where('projects.deleted_at', null);
            })
            ->join('setting_priority', function ($join) {
                $join->on('setting_priority.id', '=', 'issues.priority_id')
                    ->where('setting_priority.deleted_at', null);
            })
            ->whereIn('issues.status_id', [1, 2])
            ->whereNull('issues.deleted_at')
            ->select([
                'issues.id as id',
                'issues.reference as reference',
                'issues.location_id as location_id',
                'drawing_plans.id as drawing_plan_id',
                'projects.id as project_id',
                'projects.name as project_name',
                'contractors.id as contractor_id',
                'inpectors.id as inspector_id',
                'issues.created_at as created_at',
                'setting_priority.no_of_days_notify as no_of_days_notify',
            ])
            ->get();

        $notification_group = [];
        foreach ($issues_group as $issues) {
            if ($issues->no_of_days_notify) {
                $diff_days = Carbon::parse($issues->created_at)->startOfDay()->diffInDays() + 1;
    
                if ($diff_days % $issues->no_of_days_notify == 0) {
                    if ($issues->contractor_id && $issues->inspector_id) {
                        $notification_group["$issues->project_id|$issues->project_name"]["$issues->id|$issues->reference"]['plan_id'] = $issues->drawing_plan_id;
                        $notification_group["$issues->project_id|$issues->project_name"]["$issues->id|$issues->reference"]['location_id'] = $issues->location_id;
                        $notification_group["$issues->project_id|$issues->project_name"]["$issues->id|$issues->reference"]['notify_by'] = $issues->inspector_id;
                        $notification_group["$issues->project_id|$issues->project_name"]["$issues->id|$issues->reference"]['notify_to'][]['user_id']= $issues->contractor_id;
                    }
                }
            }
        }

        // foreach ($notification_group as $project => $issues) {
        //     $project_id = explode('|', $project)[0];
        //     $project_name = explode('|', $project)[1];
            
        //     foreach ($issues as $issue => $val) {
        //         $issue_id = explode('|', $issue)[0];
        //         $issue_reference = explode('|', $issue)[1];

        //         $appData = [
        //             'type' => 'reminder',
        //             'project_id' => $project_id,
        //             'plan_id' => $val['plan_id'],
        //             'location_id' => $val['location_id'],
        //             'issue_id' => $issue_id,
        //             'show_in_foreground' => true
        //         ];
                
        //         $this->FCMnotification($project_name, "Issue Pending Response ($issue_reference)", $appData, $val['notify_to'], $issue_id, $val['notify_by']);
        //     }
        // }


        ##fatihah ask to combine all the noti issue in 1 notification by project.

        $notify_to = array();
        foreach ($notification_group as $project => $issues) {
            $project_id = explode('|', $project)[0];
            $project_name = explode('|', $project)[1];
            
            foreach ($issues as $issue => $val) {
                foreach ($val["notify_to"] as $notiTo) {

                    if(array_key_exists($notiTo["user_id"], $notify_to)){
                        array_push($notify_to[$notiTo["user_id"]], $notiTo["user_id"]);
                    }else{

                        $notify_to[$notiTo["user_id"]] = array($notiTo["user_id"]);
                        
                    }
                }
            }

            $appData = [
                'type' => 'reminder',
                'project_id' => $project_id,
                'show_in_foreground' => true
            ];
            
            foreach ($notify_to as $key => $value) {

                $this->FCMnotification($project_name, "There are still " . count($value) . " issue is pending in this project.", $appData, array("tets" => array( "user_id" => $key) ) , null, 1);

            }
        }

    }
}

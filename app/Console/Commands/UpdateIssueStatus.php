<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Entity\Issue;
use App\Entity\History;
use App\Entity\LocationPoint;
use App\Entity\ItemSubmitted;
use App\Entity\RoleUser;
use Illuminate\Console\Command;
use App\Http\Controllers\Traits\PushNotification;


class UpdateIssueStatus extends Command
{
    use PushNotification;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To keep track of access item and update the issues status.';

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
        $item_submitted = ItemSubmitted::All();

        $now = Carbon::now();
        $time_now = date("H:i:s", strtotime($now));
        $morning = '00:00:00';
        $afternoon = '15:00:00';

        
        $item_submitted = ItemSubmitted::select(['drawing_plan_id'])->groupBy('drawing_plan_id')->get();
        if(count($item_submitted)>0)
        {
            foreach($item_submitted as $item_submitted_plan)
            {
                $drawing_plan_id = $item_submitted_plan->drawing_plan_id;  

                $locations = LocationPoint::where('drawing_plan_id', $drawing_plan_id)->get();

                foreach($locations as $location)
                {   
                    $pending_access_issues = Issue::where('location_id', $location->id)->where('status_id', 13)->get();

                    if(count($pending_access_issues)>0)
                    {
                        foreach($pending_access_issues as $pending_access_issue)
                        {
                            $pending_access_issue->update([
                                'status_id' => 5,
                            ]);

                            $history = History::create([
                                'user_id'               => 1,
                                'issue_id'              => $pending_access_issue->id,
                                'status_id'             => $pending_access_issue->status_id,
                                'remarks'               => "Issue status changed to W.I.P",
                                'customer_view'         => 1,
                            ]);

                            $title =  $pending_access_issue->location->drawingPlan->drawingSet->project->name;
                            $message =  'Issue('. $pending_access_issue->reference .') is changed to "W.I.P" status.';

                            $appData = array('type' => 'accept_issue','project_id' => $pending_access_issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $pending_access_issue->location->drawingPlan->id ,'location_id' => $pending_access_issue->location_id,'issue_id' => $pending_access_issue->id, 'show_in_foreground' => true);

                            $contractor = RoleUser::where('role_id', 5)->where('group_id', $pending_access_issue->group_id)->select('user_id')->get();
                            $this->FCMnotification($title, $message, $appData, $contractor, $pending_access_issue->id, $pending_access_issue->created_by);

                            if($pending_access_issue->inspector_id!=null)
                            {
                                $inspector = RoleUser::where('user_id', $pending_access_issue->inspector_id)->select('user_id')->get();

                                $this->FCMnotification($title, $message, $appData,$inspector, $pending_access_issue->id, $history->user_id);  
                            }
                        }
                    }
                }
            }
        }
    }
}


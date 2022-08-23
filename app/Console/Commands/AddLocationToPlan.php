<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Entity\LocationPoint;
use App\Entity\DrawingPlan;

class AddLocationToPlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Location ("Other") to all drawing plan';

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
        $drawing_plans = DrawingPlan::all();

        foreach ($drawing_plans as $plan) {
            if ($plan->types != 'custom') {
                $id = $plan->id;

                $location = LocationPoint::where('drawing_plan_id', $id)->where('reference', 'LIKE', '%R0')->first();
            
                if (!$location) {
                    $location = LocationPoint::create([
                        'name'              =>  "Other",
                        'drawing_plan_id'   =>  $plan->id,
                        'status_id'         =>  1,
                        'points'            =>  implode(',', [0,0,0,$plan->height, $plan->width, $plan->height, $plan->width, 0]),
                        'color'             =>  "#000000",
                    ]);
            
                    $now = \Carbon\Carbon::now('Asia/Kuala_Lumpur');
                    $format = date("dmy", strtotime($now));
                    $unique_ref = $format . '-P' . $id . 'L' . $location->id . '-R0'; 
            
                    $location->forcefill(['reference' => $unique_ref])->save();
                }
            }
        }
    }
}

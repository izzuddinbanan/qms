<?php

use Illuminate\Database\Seeder;
use App\Entity\Status;

class PendingOwnerStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pendingOwnerAcceptance = Status::create([
        	'internal'			=>	'Pending Owner Acceptance',
        	'internal_color'	=>	'#DF00D0',
        	'external'			=>	'Pending Owner Acceptance',
        	'external_color'	=>	'#DF00D0',
        ]);

        $redoVerification = Status::create([
            'internal'          =>  'Redo Verification',
            'internal_color'    =>  '#FF4A93',
            'external'          =>  'Pending Verification',
            'external_color'    =>  '#EFD000',
        ]);

        $status_new = Status::where('id', 2)->update(['external' => 'W.I.P']);
        $status_pending_start = Status::where('id', 3)->update(['external' => 'W.I.P']);

    }
}

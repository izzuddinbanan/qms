<?php

use Illuminate\Database\Seeder;
use App\Entity\GeneralStatus;

class SubmissionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        return collect([
            [
                'name'  => 'Accept',
                'type'  => 'submission',
            ],
            [
                'name'  => 'Reject',
                'type'  => 'submission',
            ],
            [
                'name'  => 'Pending',
                'type'  => 'submission',
            ],
            
        ])
        ->each(function ($status) {
            
            GeneralStatus::create([
                'name'  => $status['name'],
                'type'  => $status['type'],
            ]);
        });
    }
}

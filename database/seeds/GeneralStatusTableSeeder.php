<?php

use App\Entity\GeneralStatus;
use Illuminate\Database\Seeder;

class GeneralStatusTableSeeder extends Seeder
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
                'name'  => 'Open',
                'type'  => 'location',
                'color' => '#0000FF',
            ],
            [
                'name'  => 'Ready',
                'type'  => 'location',
                'color' => '#FFFF00',
            ],
            [
                'name'  => 'Closed',
                'type'  => 'location',
                'color' => '#008000',
            ],

        ])
            ->each(function ($status) {

                GeneralStatus::create([
                    'name'  => $status['name'],
                    'type'  => $status['type'],
                    'color' => $status['color'],
                ]);
            });
    }
}

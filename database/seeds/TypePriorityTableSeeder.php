<?php

use App\Entity\PriorityType;
use Illuminate\Database\Seeder;

class TypePriorityTableSeeder extends Seeder
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
                'name'  => 'Commercial',
            ],
            [
                'name'  => 'Residential',
            ],
        ])
            ->each(function ($status) {

                PriorityType::create([
                    'name'  => $status['name'],
                ]);
            });
    }
}

<?php

use App\Entity\Language;
use Illuminate\Database\Seeder;

class LanguageTableSeeder extends Seeder
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
                'name'  => 'English',
                'abbreviation_name'  => 'en',

            ],
            [
                'name'  => 'Malaysia',
                'abbreviation_name'  => 'my',
            ],
        ])
            ->each(function ($language) {

                Language::create([
                    'name'               => $language['name'],
                    'abbreviation_name'  => $language['abbreviation_name'],
                ]);
            });
    }
}

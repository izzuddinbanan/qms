<?php

use App\Entity\Role;
use App\Entity\User;
use Illuminate\Database\Seeder;

class AddingThirdPartySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::firstOrCreate([
            'name'         => 'third_party',
            'display_name' => 'Third Party',
        ]);

        $user = User::whereEmail('salesforce@commudesk.com')->first();

        if (!$user) {
            $user = User::create([
                'email'    => 'salesforce@commudesk.com',
                'password' => bcrypt('password'),
            ]);
        }

        if (!$user->hasRole('thirdy_party')) {

            $user->attachRole($role);
        }

    }
}

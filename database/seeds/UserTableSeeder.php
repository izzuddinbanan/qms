<?php

use App\Entity\User;
use App\Entity\RoleUser;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        return collect([
            ['name' => 'super User', 'email' => 'su@convep.com', 'role' => 1],
        ])
        ->each(function ($user) {

            $registeredUser = User::create([
                'name'      => $user['name'],
                'email'     => $user['email'],
                'password'  => bcrypt('password'),
                'verified'  => true,
            ]);

            RoleUser::create([
                'user_id'   => $registeredUser->id,
                'role_id'   => $user['role'], 
            ]);


        });

    }
}

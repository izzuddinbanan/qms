<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(StatusTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(LanguageTableSeeder::class);
        $this->call(FormAttributeSeeder::class);
        $this->call(GeneralStatusTableSeeder::class);
        $this->call(SubmissionStatusSeeder::class);
        $this->call(TypePriorityTableSeeder::class);
        $this->call(PendingOwnerStatus::class);
    }
}

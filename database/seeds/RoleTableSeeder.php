<?php

use App\Entity\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RoleTableSeeder extends Seeder
{
    /**
     * @var array
     */
    private $tables = [

        'roles',
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $this->truncateTables();

        return collect([
            [
                'name'         => 'super_user',
                'display_name' => 'Super User',
            ],
            [
                'name'         => 'power_user',
                'display_name' => 'Power User',
            ],
            [
                'name'         => 'admin',
                'display_name' => 'Administrator',
            ],
            [
                'name'         => 'inspector',
                'display_name' => 'Inspector',
            ],
            [
                'name'         => 'contractor',
                'display_name' => 'Contractor',
            ],
            [
                'name'         => 'subcontractor',
                'display_name' => 'Subcontractor',
            ],
            [
                'name'         => 'owner',
                'display_name' => 'Owner',
            ],
            [
                'name'         => 'project_team',
                'display_name' => 'Project Team',
            ],

        ])
            ->each(function ($status) {

                Role::create([
                    'name'         => $status['name'],
                    'display_name' => $status['display_name'],
                ]);
            });

        Schema::enableForeignKeyConstraints();
    }

    private function truncateTables()
    {
        $this->mysqlTruncate();
    }

    private function mysqlTruncate()
    {
        foreach ($this->tables as $table) {
            DB::table($table)->truncate();
        }
    }
}

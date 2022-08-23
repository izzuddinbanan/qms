<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTableClientProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('abbreviation_name', 20)->after('name')->nullable();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('abbreviation_name', 20)->after('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('abbreviation_name');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('abbreviation_name');
        });
    }
}

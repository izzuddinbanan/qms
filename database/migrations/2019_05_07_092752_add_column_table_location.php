<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTableLocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->text('normal_form')->after('data_lang')->nullable();
            $table->text('normal_group_form')->after('normal_form')->nullable();
            $table->text('main_form')->after('normal_group_form')->nullable();
            $table->text('main_group_form')->after('main_form')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn('normal_form');
            $table->dropColumn('normal_group_form');
            $table->dropColumn('main_form');
            $table->dropColumn('main_group_form');
        });
    }
}

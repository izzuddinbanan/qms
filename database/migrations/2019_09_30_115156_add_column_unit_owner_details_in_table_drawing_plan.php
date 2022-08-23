<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUnitOwnerDetailsInTableDrawingPlan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drawing_plans', function (Blueprint $table) {
            $table->text('car_park')->nullable();
            $table->text('access_card')->nullable();
            $table->text('key_fob')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drawing_plans', function (Blueprint $table) {
            $table->dropColumn('car_park');
            $table->dropColumn('access_card');
            $table->dropColumn('key_fob');
        });
    }
}

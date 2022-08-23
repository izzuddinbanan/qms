<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAllLocationReadyColumnToDrawingPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drawing_plans', function (Blueprint $table) {
            $table->boolean('all_location_ready')->default(0);
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
            $table->dropColumn('all_location_ready');
        });
    }
}

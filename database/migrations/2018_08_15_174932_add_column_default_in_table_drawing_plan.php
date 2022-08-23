<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDefaultInTableDrawingPlan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drawing_plans', function (Blueprint $table) {
            $table->integer('default')->default(0)->after('seq');
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
            $table->dropColumn([
                'default',
            ]);
        });
    }
}

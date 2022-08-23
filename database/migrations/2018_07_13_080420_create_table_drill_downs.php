<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDrillDowns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drill_downs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('drawing_plan_id')->nullable();
            $table->integer('to_drawing_plan_id')->nullable();
            $table->string('position_x')->nullable();
            $table->string('position_y')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drill_downs');
    }
}

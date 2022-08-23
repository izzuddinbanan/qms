<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDrawingPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drawing_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('drawing_set_id')->nullable();
            $table->string('name')->nullable();
            $table->string('file')->nullable();
            $table->integer('seq')->nullable();
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
        Schema::dropIfExists('drawing_plans');
    }
}

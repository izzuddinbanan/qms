<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLocationForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_normal_form', function (Blueprint $table) {
            $table->string('location_id')->nullable();
            $table->string('form_id')->nullable();
        });

        Schema::create('location_main_form', function (Blueprint $table) {
            $table->string('location_id')->nullable();
            $table->string('form_id')->nullable();
        });

        Schema::create('location_normal_group_form', function (Blueprint $table) {
            $table->string('location_id')->nullable();
            $table->string('form_id')->nullable();
        });

        Schema::create('location_main_group_form', function (Blueprint $table) {
            $table->string('location_id')->nullable();
            $table->string('form_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_normal_form');
        Schema::dropIfExists('location_main_form');
        Schema::dropIfExists('location_normal_group_form');
        Schema::dropIfExists('location_main_group_form');
        
    }
}

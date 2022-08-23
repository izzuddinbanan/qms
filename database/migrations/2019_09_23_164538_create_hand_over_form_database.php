<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHandOverFormDatabase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('handover_form_list', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('handover_form_section', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('handover_form_list_id')->nullable();
            $table->string('name')->nullable();
            $table->integer('seq')->nullable();
            $table->longText('config')->nullable(); ## LIST OF ITEM DETAILS
            $table->timestamps();
        });

        Schema::create('handover_form_drawing', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('drawing_set_id')->unique();
            $table->integer('key_form_id')->nullable();
            $table->integer('es_form_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('handover_form_list');
        Schema::dropIfExists('handover_form_section');
        Schema::dropIfExists('handover_form_drawing');
    }
}

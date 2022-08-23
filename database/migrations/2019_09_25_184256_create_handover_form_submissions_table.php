<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHandoverFormSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('handover_form_submissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('drawing_plan_id');
            $table->longText('key_submission')->nullable();
            $table->longText('es_submission')->nullable();
            $table->longText('waiver_submission')->nullable();
            $table->longText('photo_submission')->nullable();
            $table->longText('acceptance_submission')->nullable();
            $table->integer('survey_id')->nullable();
            $table->string('pdf_name')->nullable();
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('handover_form_submissions');
    }
}

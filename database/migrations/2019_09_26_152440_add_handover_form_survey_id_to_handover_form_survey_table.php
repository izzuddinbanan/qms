<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHandoverFormSurveyIdToHandoverFormSurveyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('handover_form_survey', function (Blueprint $table) {
            $table->integer('handover_form_survey_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('handover_form_survey', function (Blueprint $table) {
            $table->dropColumn('handover_form_survey_id');
        });
    }
}

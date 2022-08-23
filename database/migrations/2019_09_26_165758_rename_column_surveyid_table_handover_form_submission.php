<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnSurveyidTableHandoverFormSubmission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('handover_form_submissions', function (Blueprint $table) {
             $table->renameColumn('survey_id', 'survey_submission');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('handover_form_submissions', function (Blueprint $table) {
             $table->renameColumn('survey_submission', 'survey_id');
        });
    }
}

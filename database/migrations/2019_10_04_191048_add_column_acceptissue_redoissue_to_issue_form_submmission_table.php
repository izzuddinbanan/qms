<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAcceptissueRedoissueToIssueFormSubmmissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('issue_form_submmission', function (Blueprint $table) {
            $table->longText('accept_issue')->nullable();
            $table->longText('redo_issue')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('issue_form_submmission', function (Blueprint $table) {
            $table->dropColumn('accept_issue');
            $table->dropColumn('redo_issue');
        });
    }
}

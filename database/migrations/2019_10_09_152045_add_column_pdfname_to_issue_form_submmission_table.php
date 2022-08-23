<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPdfnameToIssueFormSubmmissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('issue_form_submmission', function (Blueprint $table) {
            $table->string('pdf_name')->nullable();
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
            $table->dropColumn('pdf_name');
        });
    }
}

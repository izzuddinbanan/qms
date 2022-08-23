<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssueFormSubmmissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issue_form_submmission', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference_no')->nullable();
            $table->integer('drawing_plan_id')->nullable();
            $table->integer('form_version_id')->nullable();
            $table->integer('status_id')->nullable();
            $table->string('remarks')->nullable();
            $table->longText('details')->nullable();
            $table->string('submission_type')->nullable();
            $table->datetime('created_by')->nullable();
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
        Schema::dropIfExists('issue_form_submmission');
    }
}

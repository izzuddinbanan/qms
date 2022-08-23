<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableIssues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('location_id')->nullable();
            $table->integer('inspector_id')->nullable();
            $table->integer('owner_id')->nullable();
            $table->integer('group_id')->nullable();
            $table->integer('setting_category_id')->nullable();
            $table->integer('setting_type_id')->nullable();
            $table->integer('setting_issue_id')->nullable();
            $table->integer('status_id')->nullable();
            $table->integer('priority_id')->nullable();
            $table->date('due_by')->nullable();
            $table->string('image')->nullable();
            $table->string('position_x')->nullable();
            $table->string('position_y')->nullable();
            $table->string('merge_issue_id')->nullable();
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
        Schema::dropIfExists('issues');
    }
}

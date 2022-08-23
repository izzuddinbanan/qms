<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormSubmission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reference_no', 100);
            $table->unsignedBigInteger('location_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('form_group_id');
            $table->unsignedInteger('status_id');
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('location_id')
                ->references('id')
                ->on('locations')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::create('submission_form_group', function (Blueprint $table) {
            $table->unsignedBigInteger('submission_id');
            // $table->unsignedBigInteger('form_group_id');
            $table->unsignedBigInteger('form_attribute_location_id');
            $table->text('value');

            $table->foreign('submission_id')
                ->references('id')
                ->on('submissions')
                ->onDelete('cascade');

            // $table->foreign('form_group_id')
            //     ->references('id')
            //     ->on('form_groups')
            //     ->onDelete('cascade');

            $table->foreign('form_attribute_location_id')
                ->references('id')
                ->on('form_attribute_locations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('submission_form_group');
        Schema::dropIfExists('submissions');
    }
}

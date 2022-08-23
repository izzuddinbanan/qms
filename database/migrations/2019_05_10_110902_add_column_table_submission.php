<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTableSubmission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->text('remarks')->after('status_id')->nullable();
            $table->unsignedBigInteger('form_version_id')->after('user_id')->nullable();
        });


        // Schema::table('submission_form_group', function (Blueprint $table) {
        //     $table->dropForeign(['form_group_id']);
        //     $table->dropColumn(['form_group_id']);
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('remarks');
            $table->dropColumn('form_version_id');
        });

        // Schema::table('submission_form_group', function (Blueprint $table) {

        //     $table->unsignedBigInteger('form_group_id')->after('submission_id');

        //     $table->foreign('form_group_id')
        //             ->references('id')
        //             ->on('form_groups')
        //             ->onDelete('cascade');

        // });

    }
}

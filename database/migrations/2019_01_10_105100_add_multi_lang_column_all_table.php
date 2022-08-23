<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMultiLangColumnAllTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            
        Schema::table('attributes', function (Blueprint $table) {
            $table->text('data_lang')->after('multiple_row')->nullable();
        });

        Schema::table('drawing_sets', function (Blueprint $table) {
            $table->text('data_lang')->after('default')->nullable();
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->text('data_lang')->after('description')->nullable();
        });

        Schema::table('history', function (Blueprint $table) {
            $table->text('data_lang')->after('status_id')->nullable();
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->text('data_lang')->after('status_id')->nullable();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->text('data_lang')->after('email_notification_at')->nullable();
        });

        Schema::table('setting_category', function (Blueprint $table) {
            $table->text('data_lang')->after('name')->nullable();
        });

        Schema::table('setting_issues', function (Blueprint $table) {
            $table->text('data_lang')->after('name')->nullable();
        });

        Schema::table('setting_priority', function (Blueprint $table) {
            $table->text('data_lang')->after('no_of_days')->nullable();
        });

        Schema::table('setting_priority_type', function (Blueprint $table) {
            $table->text('data_lang')->after('name')->nullable();
        });

        Schema::table('setting_types', function (Blueprint $table) {
            $table->text('data_lang')->after('name')->nullable();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('language_id')->string('name')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('language_id')->after('current_role')->default(1)->nullable();
        });

        Schema::table('languages', function (Blueprint $table) {
            $table->string('abbreviation_name')->after('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attributes', function (Blueprint $table) {
            $table->dropColumn('data_lang');
        });

        Schema::table('drawing_sets', function (Blueprint $table) {
            $table->dropColumn('data_lang');
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('data_lang');
        });

        Schema::table('history', function (Blueprint $table) {
            $table->dropColumn('data_lang');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn('data_lang');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('data_lang');
        });

        Schema::table('setting_category', function (Blueprint $table) {
            $table->dropColumn('data_lang');
        });

        Schema::table('setting_issues', function (Blueprint $table) {
            $table->dropColumn('data_lang');
        });

        Schema::table('setting_priority', function (Blueprint $table) {
            $table->dropColumn('data_lang');
        });

        Schema::table('setting_priority_type', function (Blueprint $table) {
            $table->dropColumn('data_lang');
        });

        Schema::table('setting_types', function (Blueprint $table) {
            $table->dropColumn('data_lang');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('language_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('language_id');
        });
    }
}

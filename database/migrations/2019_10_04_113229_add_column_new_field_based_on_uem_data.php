<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnNewFieldBasedOnUemData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drawing_plans', function (Blueprint $table) {
            $table->date('spa_date')->nullable();
            $table->date('vp_date')->nullable();
            $table->date('dlp_expiry_date')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('salutation')->nullable();
            $table->string('ic_no')->nullable();
            $table->string('passport_no')->nullable();
            $table->string('comp_reg_no')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('house_no')->nullable();
            $table->string('office_no')->nullable();
            $table->text('mailing_address')->nullable();
            $table->string('staff_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drawing_plans', function (Blueprint $table) {
            $table->dropColumn('spa_date');
            $table->dropColumn('vp_date');
            $table->dropColumn('dlp_expiry_date');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('salutation');
            $table->dropColumn('ic_no');
            $table->dropColumn('passport_no');
            $table->dropColumn('comp_reg_no');
            $table->dropColumn('phone_no');
            $table->dropColumn('house_no');
            $table->dropColumn('office_no');
            $table->dropColumn('mailing_address');
            $table->dropColumn('staff_id');
        });
    }
}

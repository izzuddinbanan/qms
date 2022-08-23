<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreColumnTableHandoverFormSection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('handover_form_list', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->boolean('meter_reading')->default(false)->after('name');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('handover_form_list', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('meter_reading');
        });
    }
}

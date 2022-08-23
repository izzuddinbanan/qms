<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnHandoverFormInTableDrawingSets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drawing_sets', function (Blueprint $table) {
            $table->integer('handover_key_id')->nullable()->after('name');
            $table->integer('handover_es_id')->nullable()->after('handover_key_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drawing_sets', function (Blueprint $table) {
            $table->dropColumn('handover_key_id');
            $table->dropColumn('handover_es_id');
        });
    }
}

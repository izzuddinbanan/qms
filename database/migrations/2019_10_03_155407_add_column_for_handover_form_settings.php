<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnForHandoverFormSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drawing_sets', function (Blueprint $table) {
            $table->integer('handover_form')->nullable()->after('handover_es_id')->comment('link to digital form for close and handover form'); // link to digital form for close and handover form
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
            $table->dropColumn('handover_form_id');
        });
    }
}

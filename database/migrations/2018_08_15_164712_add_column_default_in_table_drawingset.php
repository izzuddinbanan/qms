<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDefaultInTableDrawingset extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drawing_sets', function (Blueprint $table) {
            $table->integer('default')->default(0)->after('seq');
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
            $table->dropColumn([
                'default',
            ]);
        });
    }
}

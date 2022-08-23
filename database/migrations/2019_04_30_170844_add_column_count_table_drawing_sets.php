<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCountTableDrawingSets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drawing_sets', function (Blueprint $table) {
            $table->integer('count')->after('seq')->nullable();
        });

        Schema::table('drawing_sets', function (Blueprint $table) {
            $table->dropColumn('default');
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
            $table->integer('default')->after('seq')->nullable();
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsTableIssues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->date('confirmed_date')->after('start_date');
            $table->unsignedInteger('confirmed_by')->nullable()->after('confirmed_date');
            $table->date('completed_date')->after('confirmed_by');
            $table->unsignedInteger('completed_by')->nullable()->after('completed_date');
            $table->date('closed_date')->after('completed_by');
            $table->unsignedInteger('closed_by')->nullable()->after('closed_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropColumn([
                'confirmed_date',
                'confirmed_by',
                'completed_date',
                'completed_by',
                'closed_date',
                'closed_by',
            ]);
        });
    }
}

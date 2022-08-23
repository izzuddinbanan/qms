<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemSubmittedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_submitted', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('drawing_plan_id')->nullable();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('primary')->nullable();
            $table->string('possessor')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_submitted');
    }
}

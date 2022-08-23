<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemSubmittedTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_submitted_transaction', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('items')->nullabe();
            $table->string('status')->nullable();
            $table->integer('drawing_plan_id')->nullable();
            $table->integer('possessor_from')->nullable();
            $table->integer('possessor_to')->nullable();
            $table->string('signature_receive')->nullable();
            $table->string('signature_submit')->nullable();
            $table->dateTime('signature_receive_datetime')->nullable();
            $table->dateTime('signature_submit_datetime')->nullable();
            $table->string('name_receive')->nullable();
            $table->string('name_submit')->nullable();
            $table->text('internal_remarks')->nullable();
            $table->text('external_remarks')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('item_submitted_transaction');
    }
}

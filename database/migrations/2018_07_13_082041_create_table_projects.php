w<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('client_id');
            $table->string('name')->nullable();
            $table->string('contract_no')->nullable();
            $table->text('description')->nullable();
            $table->integer('language_id')->nullable();
            $table->string('logo')->nullable();
            $table->string('app_logo')->nullable();
            $table->integer('email_notification')->default(0)->nullable();
            $table->time('email_notification_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}

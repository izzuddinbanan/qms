<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableFormGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::create('attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('display_name');
            $table->boolean('allow_insert_value');
            $table->string('preset_value')->nullable();
            $table->boolean('multiple_input');
            $table->boolean('multiple_row');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('form_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->unsignedBigInteger('client_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->onDelete('cascade');
        });

        Schema::create('form_group_project', function (Blueprint $table) {
            $table->unsignedBigInteger('form_group_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();

            $table->foreign('form_group_id')
                ->references('id')
                ->on('form_groups')
                ->onDelete('cascade');
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');
        });

        Schema::create('form_versions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('version', 100);
            $table->unsignedBigInteger('form_group_id')->nullable();
            $table->tinyInteger('status');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('form_group_id')
                ->references('id')
                ->on('form_groups')
                ->onDelete('cascade');
        });

        Schema::create('form_sections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->tinyInteger('sequence');
            $table->unsignedBigInteger('form_version_id')->nullable();
            $table->timestamps();

            $table->foreign('form_version_id')
                ->references('id')
                ->on('form_versions')
                ->onDelete('cascade');
        });

        Schema::create('forms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('file');
            $table->string('width');
            $table->string('height');
            $table->unsignedBigInteger('form_version_id')->nullable();
            $table->timestamps();

            $table->foreign('form_version_id')
                ->references('id')
                ->on('form_versions')
                ->onDelete('cascade');
        });

        Schema::create('form_attributes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('form_id')->nullable();
            $table->unsignedBigInteger('form_section_id')->nullable();
            $table->unsignedInteger('attribute_id')->nullable();
            $table->string('key');
            $table->boolean('is_required');
            $table->timestamps();

            $table->foreign('form_id')
                ->references('id')
                ->on('forms')
                ->onDelete('cascade');
            $table->foreign('attribute_id')
                ->references('id')
                ->on('attributes')
                ->onDelete('cascade');
            $table->foreign('form_section_id')
                ->references('id')
                ->on('form_sections')
                ->onDelete('cascade');
        });

        Schema::create('form_attribute_locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('form_attribute_id')->nullable();
            $table->string('height');
            $table->string('width');
            $table->string('position_x');
            $table->string('position_y');
            $table->string('number_of_row')->nullable();
            $table->text('value')->nullable();
            $table->string('background_color')->nullable();

            $table->foreign('form_attribute_id')
                ->references('id')
                ->on('form_attributes')
                ->onDelete('cascade');
        });

        Schema::create('form_attribute_role', function (Blueprint $table) {
            $table->unsignedBigInteger('form_attribute_id')->nullable();
            $table->unsignedInteger('role_id')->nullable();

            $table->foreign('form_attribute_id')
                ->references('id')
                ->on('form_attributes')
                ->onDelete('cascade');
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_attribute_role');
        Schema::dropIfExists('form_attribute_locations');
        Schema::dropIfExists('form_attributes');
        Schema::dropIfExists('forms');
        Schema::dropIfExists('form_sections');
        Schema::dropIfExists('form_versions');
        Schema::dropIfExists('form_group_project');
        Schema::dropIfExists('form_groups');
        Schema::dropIfExists('attributes');
    }
}

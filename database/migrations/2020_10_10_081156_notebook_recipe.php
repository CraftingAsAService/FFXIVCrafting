<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NotebookRecipe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notebook', function(Blueprint $table) {
            $table->increments('id')->unsigned();
        });

        Schema::create('notebook_recipe', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('recipe_id')->unsigned();
            $table->integer('notebook_id')->unsigned();
            $table->tinyinteger('slot')->unsigned();
        });

        Schema::create('notebookdivision', function(Blueprint $table) {
            $table->increments('id')->unsigned(); // 0 indexed, but artificially +1'd
            $table->integer('category_id')->unsigned();
            $table->string('name');
        });

        Schema::create('notebook_notebookdivision', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('notebookdivision_id')->unsigned(); // 0 indexed, but artificially +1'd
            $table->integer('notebook_id')->unsigned();
        });

        Schema::create('notebookdivision_category', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notebook');
        Schema::dropIfExists('notebook_recipe');
        Schema::dropIfExists('notebookdivision');
        Schema::dropIfExists('notebook_notebookdivision');
        Schema::dropIfExists('notebookdivision_category');
    }
}

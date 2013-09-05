<?php

use Illuminate\Database\Migrations\Migration;

class DatabaseStructure extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::create('stats', function($table)
		{
			$table->increments('id');
			$table->string('name', 50);
		});
		
		Schema::create('jobs', function($table)
		{
			$table->increments('id');
			$table->string('abbreviation', 3);
			$table->string('name', 50);
		});
		
		Schema::create('equipment_types', function($table)
		{
			$table->increments('id');
			$table->string('name', 10);
			$table->smallInteger('rank');
		});
		
		Schema::create('materia', function($table)
		{
			$table->increments('id');
			$table->integer('job_id');
			$table->string('name', 50);
			$table->integer('stat_id');
			$table->smallInteger('amount');
		});
		
		Schema::create('food', function($table)
		{
			$table->increments('id');
			$table->integer('job_id');
			$table->string('name', 50);
		});

		Schema::create('food_stat', function($table)
		{
			$table->increments('id');
			$table->integer('food_id');
			$table->integer('stat_id');
			$table->smallInteger('percent');
			$table->smallInteger('maximum');
		});
		
		Schema::create('equipment', function($table)
		{
			$table->increments('id');
			$table->integer('job_id');
			$table->integer('type_id');
			$table->string('name', 50);
			$table->smallInteger('level');
			$table->string('origin', 50);
			$table->smallInteger('materia');
			$table->text('comments');
		});
		
		Schema::create('equipment_stat', function($table)
		{
			$table->increments('id');
			$table->integer('equipment_id');
			$table->integer('stat_id');
			$table->smallInteger('amount');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('stats');
		Schema::drop('jobs');
		Schema::drop('equipment_types');
		Schema::drop('materia');
		Schema::drop('food');
		Schema::drop('food_stat');
		Schema::drop('equipment');
		Schema::drop('equipment_stat');
	}

}
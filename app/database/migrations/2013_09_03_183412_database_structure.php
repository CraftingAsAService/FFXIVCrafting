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
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 50);
		});
		
		Schema::create('jobs', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('abbreviation', 3);
			$table->string('name', 50);
		});
		
		Schema::create('slots', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 10);
			$table->smallInteger('rank');
		});

		Schema::create('items', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 50);
			$table->string('href', 255);
			$table->smallInteger('vendors');
			$table->smallInteger('cost');
		});

		Schema::create('equipment', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('item_id');
			$table->integer('slot_id');
			$table->integer('crafted_by');
			$table->smallInteger('level');
		});
		
		Schema::create('equipment_job', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('job_id');
			$table->integer('item_id');
		});
		
		Schema::create('equipment_stat', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('equipment_id');
			$table->integer('stat_id');
			$table->smallInteger('amount');
		});
		
		Schema::create('materia', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('job_id');
			$table->integer('item_id');
			$table->integer('stat_id');
			$table->smallInteger('amount');
		});
		
		Schema::create('food', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('job_id');
			$table->integer('item_id');
			$table->integer('crafted_by'); // Crafted By
		});

		Schema::create('food_stat', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('food_id');
			$table->integer('stat_id');
			$table->smallInteger('percent');
			$table->smallInteger('maximum');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('stats');
		Schema::dropIfExists('jobs');
		Schema::dropIfExists('slots');
		Schema::dropIfExists('equipment');
		Schema::dropIfExists('equipment_job');
		Schema::dropIfExists('equipment_stat');
		Schema::dropIfExists('materia');
		Schema::dropIfExists('food');
		Schema::dropIfExists('food_stat');
	}

}
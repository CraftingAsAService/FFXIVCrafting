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
		
		Schema::create('slots', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 10);
			$table->smallInteger('rank');
			$table->enum('type', array('equipment', 'materia', 'food', 'reagent'));
		});
		
		Schema::create('jobs', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('abbreviation', 3);
			$table->string('name', 50);
			$table->enum('disciple', array('DOH', 'DOL', 'DOW', 'DOM'));
		});

		Schema::create('stats', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 50);
		});

		Schema::create('items', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 50);
			$table->string('href', 255);
			$table->smallInteger('level');
			$table->integer('slot_id');
			$table->smallInteger('vendors');
			$table->smallInteger('gil');
			$table->smallInteger('ilvl');
		});
		
		Schema::create('item_job', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('item_id');
			$table->integer('job_id');
		});
		
		Schema::create('item_stat', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('item_id');
			$table->integer('stat_id');
			$table->decimal('amount', 6, 2);
			$table->smallInteger('maximum');
		});
		
		Schema::create('recipes', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('item_id');
			$table->integer('job_id');
			$table->string('name', 50);
			$table->smallInteger('yields');
			$table->smallInteger('level');
			$table->smallInteger('job_level');
		});
		
		Schema::create('item_recipe', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('recipe_id');
			$table->integer('item_id');
			$table->smallInteger('amount');
		});
		
		Schema::create('quest_items', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('item_id');
			$table->integer('job_id');
			$table->smallInteger('level');
			$table->smallInteger('amount');
			$table->smallInteger('quality');
			$table->string('notes', 50);
		});

		Schema::create('locations', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 50);
		});

		Schema::create('item_location', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('item_id');
			$table->integer('location_id');
			$table->smallInteger('level');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		
		// Just delete every table
		foreach (DB::select('SHOW TABLES') as $table)
		{	
			$table = (Array) $table;
			$table = end($table);
			// Except for migrations
			if ($table != 'migrations')
				Schema::dropIfExists($table);
		}

	}

}
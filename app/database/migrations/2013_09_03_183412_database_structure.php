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
			$table->enum('type', array('equipment', 'materia', 'food'));
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
			$table->string('disciple_focus', 3);
		});

		Schema::create('items', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 50);
			$table->string('href', 255);
			$table->smallInteger('level');
			$table->integer('slot_id');
			$table->integer('crafted_by');
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
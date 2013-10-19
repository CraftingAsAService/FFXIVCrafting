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
		Schema::create('jobs', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('abbreviation', 3);
			$table->string('name', 50);
			$table->enum('disciple', array('DOH', 'DOL', 'DOW', 'DOM', 'ALL'));

			$table->index('abbreviation');
			$table->index('disciple');
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
			$table->string('icon', 20);
			$table->enum('role', array('Main Hand', 'Off Hand', 'Head', 'Body', 'Hands', 'Waist', 'Wrists', 'Ears', 'Feet', 'Right Ring', 'Legs', 'Neck', 'Catalyst', 'Materia', 'Fishing Tackle', 'Meal', 'Medicine', 'Miscellany', 'Other', 'Seafood', 'Soul Crystal', 'Dye', 'Crystal', 'Bone', 'Cloth', 'Ingredient', 'Leather', 'Lumber', 'Metal', 'Part', 'Reagent', 'Stone'));
			$table->string('sub_role', 35);
			$table->smallInteger('level');
			$table->smallInteger('ilvl');
			$table->smallInteger('stack');
			$table->smallInteger('seals');
			$table->smallInteger('buy');
			$table->smallInteger('sell');
			$table->smallInteger('repair');
			$table->boolean('untradable');
			$table->boolean('unique');
			$table->string('cannot_equip', 30);
			$table->string('achievement', 50);

			$table->index('role');
			$table->index('level');
		});
		
		Schema::create('item_job', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('item_id');
			$table->integer('job_id');

			$table->index('item_id', 'job_id');
		});
		
		Schema::create('item_stat', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('item_id');
			$table->integer('stat_id');
			$table->decimal('amount', 6, 2);
			$table->smallInteger('maximum');

			$table->index('item_id', 'stat_id');
		});
		
		Schema::create('recipes', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('item_id');
			$table->integer('job_id');
			$table->string('name', 50);
			$table->string('icon', 20);
			$table->smallInteger('stars');
			$table->smallInteger('yields');
			$table->smallInteger('level');
			$table->smallInteger('job_level');

			$table->index('item_id', 'job_id');
			$table->index('level');
		});
		
		Schema::create('item_recipe', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('recipe_id');
			$table->integer('item_id');
			$table->smallInteger('amount');

			$table->index('item_id', 'recipe_id');
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

			$table->index('item_id', 'job_id');
			$table->index('level');
		});

		Schema::create('locations', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 50);
		});
		
		Schema::create('leves', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 50);
			$table->integer('job_id');
			$table->integer('item_id');
			$table->smallInteger('level');
			$table->smallInteger('amount');
			$table->integer('xp');
			$table->smallInteger('gil');
			$table->smallInteger('triple');
			$table->enum('type', array('Town', 'Courier', 'Field'));
			$table->integer('major_location_id');
			$table->integer('minor_location_id');
			$table->integer('location_id');

			$table->index('item_id', 'job_id');
			$table->index('level');
			$table->index('type');
		});

		Schema::create('experience', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->smallInteger('level');
			$table->integer('experience');

			$table->index('level');
		});

		Schema::create('gathering_nodes', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('job_id');
			$table->enum('action', array('Harvesting', 'Logging', 'Mining', 'Quarrying'));
			$table->smallInteger('level');
			$table->integer('location_id');
			$table->smallInteger('location_level');

			$table->index('job_id');
			$table->index('action');
			$table->index('location_id');
		});

		Schema::create('gathering_node_item', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('item_id');
			$table->integer('gathering_node_id');

			$table->index('item_id', 'gathering_node_id');
			$table->index('gathering_node_id');
		});

		Schema::create('vendors', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 50);
			$table->string('title', 50);
			$table->integer('location_id');
			$table->smallInteger('x');
			$table->smallInteger('y');
		});

		Schema::create('item_vendor', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('item_id');
			$table->integer('vendor_id');
			
			$table->index('item_id', 'vendor_id');
		});

		Schema::create('careers', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->enum('type', array('recipe', 'item'));
			$table->integer('identifier');
			$table->smallInteger('level');
			
			$table->index('type');
			$table->index('identifier');
			$table->index('level');
		});

		Schema::create('career_job', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('career_id');
			$table->integer('job_id');
			$table->decimal('amount', 6, 2);
			
			$table->index('career_id', 'job_id');
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
			$table = (array) $table;
			$table = end($table);
			// Except for migrations
			if ($table != 'migrations')
				Schema::dropIfExists($table);
		}

	}

}
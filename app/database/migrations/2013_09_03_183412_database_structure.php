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
		// DEPRECIATED!
		return;
		
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
			
			$table->enum('role', array(
				'Armor',
				'Ears',
				'Main Hand',
				'Neck',
				'Off Hand',
				'Other',
				'Right Ring',
				'Wrists'
			));

			$table->enum('sub_role', array(
				'Crystal',
				'Head',
				'Body',
				'Legs',
				'Hands',
				'Feet',
				'Waist',
				'Gladiator\\\'s Arm',
				'Pugilist\\\'s Arm',
				'Marauder\\\'s Arm',
				'Lancer\\\'s Arm',
				'Archer\\\'s Arm',
				'One-handed Conjurer\\\'s Arm',
				'Two-handed Conjurer\\\'s Arm',
				'One-handed Thaumaturge\\\'s Arm',
				'Two-handed Thaumaturge\\\'s Arm',
				'Arcanist\\\'s Grimoire',
				'Shield',
				'Carpenter\\\'s Primary Tool',
				'Carpenter\\\'s Secondary Tool',
				'Blacksmith\\\'s Primary Tool',
				'Blacksmith\\\'s Secondary Tool',
				'Armorer\\\'s Primary Tool',
				'Armorer\\\'s Secondary Tool',
				'Goldsmith\\\'s Primary Tool',
				'Goldsmith\\\'s Secondary Tool',
				'Leatherworker\\\'s Primary Tool',
				'Leatherworker\\\'s Secondary Tool',
				'Weaver\\\'s Primary Tool',
				'Weaver\\\'s Secondary Tool',
				'Alchemist\\\'s Primary Tool',
				'Alchemist\\\'s Secondary Tool',
				'Culinarian\\\'s Primary Tool',
				'Culinarian\\\'s Secondary Tool',
				'Miner\\\'s Primary Tool',
				'Miner\\\'s Secondary Tool',
				'Botanist\\\'s Primary Tool',
				'Botanist\\\'s Secondary Tool',
				'Fisher\\\'s Primary Tool',
				'Fishing Tackle',
				'Bracelets',
				'Earrings',
				'Ring',
				'Necklace',
				'Soul Crystal',
				'Medicine',
				'Reagent',
				'Meal',
				'Ingredient',
				'Seafood',
				'Miscellany',
				'Metal',
				'Stone',
				'Leather',
				'Cloth',
				'Lumber',
				'Bone',
				'Part',
				'Catalyst',
				'Materia',
				'Dye',
				'Minion',
				'Construction Permit',
				'Roof',
				'Exterior Wall',
				'Window',
				'Door',
				'Roof Decoration',
				'Exterior Wall Decoration',
				'Placard',
				'Fence',
				'Interior Wall',
				'Flooring',
				'Ceiling Light',
				'Outdoor Furnishing',
				'Furnishing',
				'Table',
				'Tabletop',
				'Wall-mounted',
				'Rug'
			));
			
			$table->smallInteger('level');
			$table->smallInteger('ilvl');
			$table->smallInteger('stack');
			$table->smallInteger('buy');
			$table->smallInteger('sell');
			$table->smallInteger('repair');
			$table->boolean('untradable');
			$table->boolean('unique');
			$table->boolean('rewarded');
			$table->string('stat_wrench', 15);
			$table->string('cannot_equip', 30);

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
			$table->decimal('hq', 6, 2)->nullable()->default(NULL);
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
			$table->enum('type', array('Town', 'Courier', 'Reverse Courier', 'Field', 'Gathering'));
			$table->integer('major_location_id');
			$table->integer('minor_location_id');
			$table->integer('location_id');
			$table->string('notes', 100);

			$table->index('item_id', 'job_id');
			$table->index('level');
			$table->index('type');
		});
		
		Schema::create('leve_rewards', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('item_name', 50);
			$table->integer('item_id');
			$table->integer('job_id');
			$table->smallInteger('level');
			$table->smallInteger('amount');
			
			$table->index('job_id', 'level');
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
			$table->integer('area_id');

			$table->index('job_id');
			$table->index('action');
			$table->index('location_id');
			$table->index('area_id');
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
		$tables = array(
			'jobs',
			'stats',
			'items',
			'item_job',
			'item_stat',
			'recipes',
			'item_recipe',
			'quest_items',
			'locations',
			'leves',
			'leve_rewards',
			'experience',
			'gathering_nodes',
			'gathering_node_item',
			'vendors',
			'item_vendor',
			'careers',
			'career_job',
		);

		foreach ($tables as $table)
			Schema::dropIfExists($table);

	}

}
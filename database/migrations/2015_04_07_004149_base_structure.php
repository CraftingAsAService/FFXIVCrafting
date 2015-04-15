<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BaseStructure extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create generic translations table
		// Not prefixing with "libra_"
		if ( ! Schema::hasTable('translations'))
			Schema::create('translations', function($table)
			{
				$table->engine = 'InnoDB';

				$table->increments('id');
				$table->string('term', 255);
			});

		// Delete all existing tables, except for translations
		$this->down();

		// Super basic tables (id & name only)
		$super_basic = array(
			'classjob_category',
			'item_ui_kind', 
			'item_category', 
			'item_series', 
			'item_special_bonus', 
			'baseparam', 
			'races',
			'recipe_elements',
			'notebook_division',
			'guardians',
			'shops'
		);

		foreach ($super_basic as $table_name)
			Schema::create($table_name, function($table)
			{
				$table->engine = 'InnoDB';

				$table->increments('id');
				$table->integer('name_en')->unsigned(); // FK translations
				$table->integer('name_ja')->unsigned(); // FK translations
				$table->integer('name_fr')->unsigned(); // FK translations
				$table->integer('name_de')->unsigned(); // FK translations
			});

		Schema::create('classjob', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->boolean('is_job');
			$table->smallInteger('rank')->unsigned();
			$table->integer('name_en')->unsigned(); // FK translations
			$table->integer('name_ja')->unsigned(); // FK translations
			$table->integer('name_fr')->unsigned(); // FK translations
			$table->integer('name_de')->unsigned(); // FK translations
			$table->integer('abbr_en')->unsigned(); // FK translations
			$table->integer('abbr_ja')->unsigned(); // FK translations
			$table->integer('abbr_fr')->unsigned(); // FK translations
			$table->integer('abbr_de')->unsigned(); // FK translations

			$table->index('is_job');
			$table->index('rank');
		});

		Schema::create('classjob_classjob_category', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('classjob_id')->unsigned(); // FK classjob
			$table->integer('classjob_category_id')->unsigned(); // FK classjob_category

			$table->index('classjob_id', 'classjob_category_id');
		});

		Schema::create('place_name', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('region')->unsigned(); // FK self
			$table->integer('name_en')->unsigned(); // FK translations
			$table->integer('name_ja')->unsigned(); // FK translations
			$table->integer('name_fr')->unsigned(); // FK translations
			$table->integer('name_de')->unsigned(); // FK translations

			$table->index('region');
			$table->index('name_en');
		});

		Schema::create('item_ui_category', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->smallInteger('itemuikind_id')->unsigned(); // FK itemuikind
			$table->smallInteger('rank')->unsigned();
			$table->integer('name_en')->unsigned(); // FK translations
			$table->integer('name_ja')->unsigned(); // FK translations
			$table->integer('name_fr')->unsigned(); // FK translations
			$table->integer('name_de')->unsigned(); // FK translations

			$table->index('itemuikind_id');
			$table->index('rank');
		});

		Schema::create('items', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->smallInteger('itemcategory_id')->unsigned(); // FK itemcategory
			$table->smallInteger('itemuicategory_id')->unsigned(); // FK itemuicategory
			$table->smallInteger('classjobcategory_id')->unsigned()->nullable()->default(null); // FK classjob_category
			$table->integer('name_en')->unsigned(); // FK translations
			$table->integer('name_ja')->unsigned(); // FK translations
			$table->integer('name_fr')->unsigned(); // FK translations
			$table->integer('name_de')->unsigned(); // FK translations
			$table->smallInteger('level')->unsigned();
			$table->smallInteger('equip_level')->unsigned();
			$table->smallInteger('rarity')->unsigned();
			$table->smallInteger('has_hq')->unsigned();
			$table->smallInteger('itemseries_id')->unsigned(); // FK itemseries
			$table->smallInteger('itemspecialbonus_id')->unsigned(); // FK itemspecialbonus
			$table->smallInteger('slot')->unsigned();
			$table->smallInteger('min_price')->unsigned()->nullable()->default(null);
			$table->smallInteger('max_price')->unsigned()->nullable()->default(null);
			$table->smallInteger('materia')->unsigned()->nullable()->default(null);
			$table->boolean('untradable');
			$table->boolean('unique');
			$table->boolean('achievable');
			$table->boolean('rewarded');
			$table->string('color', 11)->nullable()->default(null);
			$table->integer('rank');

			$table->index('itemcategory_id');
			$table->index('itemuicategory_id');
			$table->index('slot');
			$table->index('equip_level');
			$table->index('level');
			$table->index('rank');
		});

		Schema::create('baseparam_items', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('item_id')->unsigned(); // FK item
			$table->integer('baseparam_id')->unsigned(); // FK baseparam
			$table->decimal('nq_amount', 6, 2)->unsigned();
			$table->decimal('hq_amount', 6, 2)->unsigned()->nullable()->default(null);
			$table->smallInteger('nq_limit')->nullable()->default(null);
			$table->smallInteger('hq_limit')->nullable()->default(null);
			$table->boolean('bonus')->nullable()->default(null);

			$table->index('item_id', 'baseparam_id');
		});

		Schema::create('classjob_items', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('item_id')->unsigned(); // FK item
			$table->integer('classjob_id')->unsigned(); // FK classjob

			$table->index('item_id', 'classjob_id');
		});

		Schema::create('npcs', function($table)
		{
			$table->engine = 'InnoDB';

			$table->bigIncrements('id');
			$table->integer('name_en')->unsigned(); // FK translations
			$table->integer('name_ja')->unsigned(); // FK translations
			$table->integer('name_fr')->unsigned(); // FK translations
			$table->integer('name_de')->unsigned(); // FK translations
			$table->enum('genus', array('beast', 'shop'));

			$table->index('genus');
		});

		// NPC Locations

		Schema::create('npcs_place_name', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->bigInteger('npcs_id')->unsigned(); // FK npcs
			$table->integer('placename_id')->unsigned(); // FK placename
			$table->smallInteger('x')->unsigned()->nullable()->default(null);
			$table->smallInteger('y')->unsigned()->nullable()->default(null);
			$table->string('levels', 10)->nullable()->default(null);
			$table->boolean('triggered')->nullable()->default(null);

			$table->index('npcs_id', 'placename_id');
		});

		// NPC Drops
		Schema::create('npcs_items', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->bigInteger('npcs_id')->unsigned(); // FK npcs
			$table->integer('item_id')->unsigned(); // FK item

			$table->index('npcs_id', 'item_id');
		});

		// NPC Shops
		Schema::create('npcs_shops', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->bigInteger('npcs_id')->unsigned(); // FK npcs
			$table->integer('shop_id')->unsigned(); // FK shop

			$table->index('npcs_id', 'shop_id');
		});

		// Shop items
		Schema::create('items_npcs_shops', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('item_id')->unsigned(); // FK items
			$table->integer('npcs_shop_id')->unsigned(); // FK npcs_shop
			$table->string('color', 11)->nullable()->default(null);

			$table->index('item_id');
			$table->index('npcs_shop_id');
		});

		Schema::create('recipes', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('item_id')->unsigned(); // FK item
			$table->integer('classjob_id')->unsigned(); // FK classjob
			$table->smallInteger('element_id')->unsigned(); // FK recipe_elements
			$table->boolean('can_hq');
			$table->smallInteger('yields')->unsigned();
			$table->smallInteger('level')->unsigned();
			$table->smallInteger('level_view')->unsigned();
			$table->smallInteger('stars')->unsigned()->nullable()->default(null);
			$table->smallInteger('req_craftsmanship')->unsigned();
			$table->smallInteger('req_control')->unsigned();
			$table->smallInteger('durability')->unsigned();
			$table->smallInteger('max_quality')->unsigned();
			$table->smallInteger('difficulty')->unsigned();
			$table->integer('rank')->unsigned();

			$table->index('item_id');
			$table->index('classjob_id');
			$table->index('level_view');
			$table->index('rank');
		});

		// Shop items
		Schema::create('recipe_reagents', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('recipe_id')->unsigned(); // FK recipes
			$table->integer('item_id')->unsigned(); // FK items
			$table->smallInteger('amount')->unsigned();

			$table->index('item_id');
			$table->index('recipe_id');
		});


		// Non Libra Tables

		Schema::create('experience', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->smallInteger('level');
			$table->integer('experience');

			$table->index('level');
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

		Schema::create('career_classjob', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('career_id');
			$table->integer('classjob_id');
			$table->decimal('amount', 6, 2);
			
			$table->index('career_id', 'classjob_id');
		});

		Schema::create('clusters', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('placename_id'); // FK placename
			$table->integer('classjob_id'); // FK classjob
			$table->smallInteger('level');
			$table->string('icon', 10);
			$table->decimal('x', 7, 4);
			$table->decimal('y', 7, 4);
			
			$table->index('placename_id');
			$table->index('classjob_id');
		});

		Schema::create('cluster_nodes', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('cluster_id'); // FK clusters
			$table->string('description', 50);
			$table->decimal('x', 7, 4);
			$table->decimal('y', 7, 4);
			
			$table->index('cluster_id');
		});

		Schema::create('cluster_items', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('cluster_id'); // FK clusters
			$table->integer('item_id'); // FK items
			
			$table->index('cluster_id', 'item_id');
		});
		
		Schema::create('quest_items', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('item_id'); // FK items
			$table->integer('classjob_id'); // FK classjob
			$table->smallInteger('level');
			$table->smallInteger('amount');
			$table->smallInteger('quality');
			$table->string('notes', 50);

			$table->index('classjob_id', 'item_id');
			$table->index('level');
		});
		
		Schema::create('leves', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 50);
			$table->integer('classjob_id');
			$table->integer('item_id');
			$table->smallInteger('level');
			$table->smallInteger('amount');
			$table->integer('xp');
			$table->smallInteger('xp_spread');
			$table->smallInteger('gil');
			$table->smallInteger('gil_spread');
			$table->smallInteger('triple');
			$table->enum('type', array('Town', 'Courier', 'Reverse Courier', 'Field', 'Gathering'));
			$table->string('major_location', 50);
			$table->string('minor_location', 50);
			$table->string('location', 50);
			$table->string('notes', 100);

			$table->index('item_id', 'classjob_id');
			$table->index('level');
			$table->index('type');
		});
		
		Schema::create('leve_rewards', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('item_name', 50);
			$table->integer('item_id');
			$table->integer('classjob_id');
			$table->smallInteger('level');
			$table->smallInteger('amount');
			
			$table->index('classjob_id', 'level');
		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Delete all "libra" tables
		$tables = array(
			'classjob_category',
			'item_ui_kind', 
			'item_category', 
			'item_series', 
			'item_special_bonus', 
			'baseparam', 
			'races',
			'recipe_elements',
			'notebook_division',
			'guardians',
			'shops',
			'classjob',
			'classjob_classjob_category',
			'place_name',
			'item_ui_category',
			'items',
			'baseparam_items',
			'classjob_items',
			'npcs',
			'npcs_place_name',
			'npcs_items',
			'npcs_shops',
			'items_npcs_shops',
			'recipes',
			'recipe_reagents',
			'experience',
			'careers',
			'career_classjob',
			'clusters',
			'cluster_nodes',
			'cluster_items',
			'quest_items',
			'leves',
			'leve_rewards'
		);

		foreach ($tables as $table)
			Schema::dropIfExists($table);

	}

}

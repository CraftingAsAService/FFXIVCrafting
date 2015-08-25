<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GarlandTables extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		if ( ! Schema::hasTable('node'))
		Schema::create('node', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name');
			$table->integer('type')->unsigned();
			$table->integer('level')->unsigned();
			$table->integer('bonus_id')->unsigned()->nullable();
			$table->integer('zone_id')->unsigned();
			$table->integer('area_id')->unsigned()->nullable();

			$table->index(['level', 'type']);
			$table->index(['zone_id', 'area_id']);
		});

		if ( ! Schema::hasTable('node_bonuses'))
		Schema::create('node_bonuses', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('condition');
			$table->string('bonus');
		});

		if ( ! Schema::hasTable('item_node'))
		Schema::create('item_node', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('item_id')->unsigned();
			$table->integer('node_id')->unsigned();

			$table->index('item_id');
			$table->index('node_id');
		});

		if ( ! Schema::hasTable('fishing'))
		Schema::create('fishing', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name');
			$table->integer('category_id')->unsigned();
			$table->integer('level')->unsigned();
			$table->integer('radius')->unsigned();
			$table->decimal('x', 5, 2)->unsigned();
			$table->decimal('y', 5, 2)->unsigned();
			$table->integer('zone_id')->unsigned();
			$table->integer('area_id')->unsigned();

			$table->index('level');
			$table->index(['zone_id', 'area_id']);
		});

		if ( ! Schema::hasTable('fishing_item'))
		Schema::create('fishing_item', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('item_id')->unsigned();
			$table->integer('fishing_id')->unsigned();
			$table->integer('level')->unsigned();

			$table->index('item_id');
			$table->index('fishing_id');
		});

		if ( ! Schema::hasTable('mob'))
		Schema::create('mob', function(Blueprint $table)
		{
			$table->bigIncrements('id')->unsigned();
			$table->string('name');
			$table->boolean('quest')->unsigned()->nullable();
			$table->string('level');
			$table->integer('zone_id')->unsigned();

			$table->index('zone_id');
		});

		if ( ! Schema::hasTable('item_mob'))
		Schema::create('item_mob', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('item_id')->unsigned();
			$table->bigInteger('mob_id')->unsigned();

			$table->index('item_id');
			$table->index('mob_id');
		});

		if ( ! Schema::hasTable('location'))
		Schema::create('location', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name');
			$table->integer('location_id')->unsigned()->nullable();
			$table->smallInteger('size')->unsigned()->nullable();
		});

		if ( ! Schema::hasTable('npc'))
		Schema::create('npc', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name');
			$table->integer('zone_id')->unsigned()->nullable();
			$table->boolean('approx')->unsigned()->nullable();
			$table->decimal('x', 5, 2)->unsigned()->nullable();
			$table->decimal('y', 5, 2)->unsigned()->nullable();
		});

		if ( ! Schema::hasTable('npc_shop'))
		Schema::create('npc_shop', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('npc_id')->unsigned();
			$table->integer('shop_id')->unsigned();

			$table->index('npc_id');
			$table->index('shop_id');
		});

		if ( ! Schema::hasTable('npc_quest'))
		Schema::create('npc_quest', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('npc_id')->unsigned();
			$table->integer('quest_id')->unsigned();

			$table->index('npc_id');
			$table->index('quest_id');
		});

		if ( ! Schema::hasTable('npc_base'))
		Schema::create('npc_base', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name');
			$table->string('title')->nullable();
		});

		if ( ! Schema::hasTable('npc_npc_base'))
		Schema::create('npc_npc_base', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('npc_id')->unsigned();
			$table->integer('npc_base_id')->unsigned();

			$table->index('npc_id');
			$table->index('npc_base_id');
		});

		if ( ! Schema::hasTable('shop_name'))
		Schema::create('shop_name', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name');
		});

		if ( ! Schema::hasTable('shop'))
		Schema::create('shop', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('name_id')->unsigned();
		});

		if ( ! Schema::hasTable('item_shop'))
		Schema::create('item_shop', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('item_id')->unsigned();
			$table->integer('shop_id')->unsigned();

			$table->index('item_id');
			$table->index('shop_id');
		});

		if ( ! Schema::hasTable('instance'))
		Schema::create('instance', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->smallInteger('type')->unsigned();
			$table->string('name');
			$table->integer('icon')->unsigned();
			$table->integer('zone_id')->unsigned()->nullable();

			$table->index('type');
			$table->index('zone_id');
		});

		if ( ! Schema::hasTable('instance_item'))
		Schema::create('instance_item', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('instance_id')->unsigned();
			$table->integer('item_id')->unsigned();

			$table->index('instance_id');
			$table->index('item_id');
		});

		if ( ! Schema::hasTable('instance_mob'))
		Schema::create('instance_mob', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('instance_id')->unsigned();
			$table->bigInteger('mob_id')->unsigned();

			$table->index('instance_id');
			$table->index('mob_id');
		});

		if ( ! Schema::hasTable('quest'))
		Schema::create('quest', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name');
			$table->smallInteger('job_category_id')->unsigned()->nullable;
			$table->smallInteger('level')->unsigned();
			$table->smallInteger('sort')->unsigned();
			$table->integer('zone_id')->unsigned();
			$table->integer('icon')->unsigned()->nullable();
			$table->integer('issuer_id')->unsigned()->nullable();
			$table->integer('target_id')->unsigned()->nullable();
			$table->smallInteger('genre')->unsigned();

			$table->index('zone_id');
			$table->index('issuer_id');
			$table->index('target_id');
		});

		if ( ! Schema::hasTable('quest_reward'))
		Schema::create('quest_reward', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('item_id')->unsigned();
			$table->integer('quest_id')->unsigned();
			$table->smallInteger('amount')->unsigned()->nullable();

			$table->index('item_id');
			$table->index('quest_id');
		});

		if ( ! Schema::hasTable('quest_required'))
		Schema::create('quest_required', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('item_id')->unsigned();
			$table->integer('quest_id')->unsigned();
			// $table->smallInteger('amount')->unsigned()->nullable();

			$table->index('item_id');
			$table->index('quest_id');
		});

		if ( ! Schema::hasTable('achievement'))
		Schema::create('achievement', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name');
			$table->integer('item_id')->unsigned();
			$table->integer('icon')->unsigned();

			$table->index('item_id');
		});

		if ( ! Schema::hasTable('fate'))
		Schema::create('fate', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name');
			$table->smallInteger('level')->unsigned();
			$table->smallInteger('max_level')->unsigned();
			$table->smallInteger('type')->unsigned();
			$table->integer('zone_id')->unsigned();
			$table->decimal('x', 5, 2)->unsigned();
			$table->decimal('y', 5, 2)->unsigned();

			$table->index('zone_id');
		});

		if ( ! Schema::hasTable('job_category'))
		Schema::create('job_category', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name');
		});

		if ( ! Schema::hasTable('job_job_category'))
		Schema::create('job_job_category', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('job_id')->unsigned();
			$table->integer('job_category_id')->unsigned();

			$table->index('job_id');
			$table->index('job_category_id');
		});

		if ( ! Schema::hasTable('job'))
		Schema::create('job', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name');
			$table->string('abbr');
		});

		if ( ! Schema::hasTable('venture'))
		Schema::create('venture', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('amounts')->nullable();
			$table->integer('job_category_id')->unsigned();
			$table->smallInteger('level')->unsigned();
			$table->smallInteger('cost')->unsigned();
			$table->smallInteger('minutes')->unsigned();

			$table->index('job_category_id');
		});

		if ( ! Schema::hasTable('item_venture'))
		Schema::create('item_venture', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('item_id')->unsigned();
			$table->integer('venture_id')->unsigned();

			$table->index('item_id');
			$table->index('venture_id');
		});

		if ( ! Schema::hasTable('leve'))
		Schema::create('leve', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name');
			$table->string('type', 25)->nullable();
			$table->smallInteger('level')->unsigned();
			$table->integer('job_category_id')->unsigned();
			$table->integer('area_id')->unsigned();
			$table->tinyInteger('repeats')->unsigned()->nullable();
			$table->integer('xp')->unsigned()->nullable();
			$table->integer('gil')->unsigned()->nullable();
			$table->integer('plate')->unsigned();
			$table->integer('frame')->unsigned();
			$table->integer('area_icon')->unsigned();

			$table->index('level');
			$table->index('job_category_id');
			$table->index('area_id');
		});

		if ( ! Schema::hasTable('leve_reward'))
		Schema::create('leve_reward', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('item_id')->unsigned();
			$table->integer('leve_id')->unsigned();
			$table->smallInteger('rate')->unsigned();
			$table->smallInteger('amount')->unsigned()->nullable();

			$table->index('item_id');
			$table->index('leve_id');
		});

		if ( ! Schema::hasTable('leve_required'))
		Schema::create('leve_required', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('item_id')->unsigned();
			$table->integer('leve_id')->unsigned();
			$table->smallInteger('amount')->unsigned()->nullable();

			$table->index('item_id');
			$table->index('leve_id');
		});

		if ( ! Schema::hasTable('item_category'))
		Schema::create('item_category', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name');
			// $table->integer('attribute_id')->unsigned()->nullable();
			$table->string('attribute')->nullable();
		});

		if ( ! Schema::hasTable('item'))
		Schema::create('item', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('eorzea_id', 15)->nullable();
			$table->string('name');
			$table->string('de_name');
			$table->string('fr_name');
			$table->string('jp_name');
			$table->string('help')->nullable();
			$table->integer('price')->unsigned()->nullable();
			$table->integer('sell_price')->unsigned()->nullable();
			$table->integer('ilvl')->unsigned();
			$table->integer('elvl')->unsigned()->nullable();
			$table->integer('item_category_id')->unsigned();
			$table->integer('job_category_id')->unsigned()->nullable();
			$table->boolean('unique')->unsigned()->nullable();
			$table->boolean('tradeable')->unsigned()->nullable();
			$table->boolean('desynthable')->unsigned()->nullable();
			$table->boolean('projectable')->unsigned()->nullable();
			$table->boolean('crestworthy')->unsigned()->nullable();
			$table->smallInteger('delivery')->unsigned()->nullable();
			$table->smallInteger('equip')->unsigned()->nullable();
			$table->smallInteger('repair')->unsigned()->nullable();
			$table->smallInteger('slot')->unsigned()->nullable();
			$table->tinyInteger('rarity')->unsigned()->nullable();
			$table->string('icon', 25);
			$table->smallInteger('sockets')->unsigned()->nullable();

			$table->index('ilvl');
			$table->index('elvl');
			$table->index('job_category_id');
			$table->index('item_category_id');
		});

		if ( ! Schema::hasTable('attribute'))
		// Schema::create('attribute', function(Blueprint $table)
		// {
		// 	$table->increments('id')->unsigned();
		// 	$table->string('name');
		// });

		if ( ! Schema::hasTable('item_attribute'))
		Schema::create('item_attribute', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('item_id')->unsigned();
			$table->enum('quality', ['nq', 'hq', 'max']);
			// $table->integer('attribute_id')->unsigned();
			$table->string('attribute');
			$table->integer('amount')->unsigned();
			$table->integer('limit')->unsigned()->nullable();

			$table->index('item_id');
			$table->index('quality');
			$table->index('attribute');
		});

		if ( ! Schema::hasTable('recipe'))
		Schema::create('recipe', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('item_id')->unsigned();
			$table->integer('job_id')->unsigned();
			$table->integer('recipe_level')->unsigned();
			$table->integer('level')->unsigned();
			$table->smallInteger('durability')->unsigned()->nullable();
			$table->smallInteger('quality')->unsigned()->nullable();
			$table->smallInteger('progress')->unsigned()->nullable();
			$table->smallInteger('yield')->unsigned();
			$table->boolean('quick_synth')->unsigned()->nullable();
			$table->boolean('hq')->unsigned()->nullable();
			$table->boolean('fc')->unsigned()->nullable();

			$table->index('item_id');
			$table->index('job_id');
			$table->index('recipe_level');
			$table->index('level');
		});

		if ( ! Schema::hasTable('recipe_reagents'))
		Schema::create('recipe_reagents', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('item_id')->unsigned();
			$table->integer('recipe_id')->unsigned();
			$table->smallInteger('amount')->unsigned();

			$table->index('item_id');
			$table->index('recipe_id');
		});
		
		if ( ! Schema::hasTable('career'))
		Schema::create('career', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('identifier')->unsigned();
			$table->enum('type', ['recipe', 'item']);
			$table->smallInteger('level')->unsigned();

			$table->index('identifier');
			$table->index('type');
		});
		
		if ( ! Schema::hasTable('career_job'))
		Schema::create('career_job', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('career_id')->unsigned();
			$table->integer('job_id')->unsigned();
			$table->decimal('amount', 6, 2)->unsigned();

			$table->index('career_id');
			$table->index('job_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$tables = [
			'node',
			'node_bonuses',
			'item_node',
			'fishing',
			'fishing_item',
			'item_mob',
			'mob',
			'location',
			'npc',
			'npc_shop',
			'npc_quest',
			'npc_base',
			'npc_npc_base',
			'shop_name',
			'shop',
			'item_shop',
			'instance',
			'instance_item',
			'instance_mob',
			'quest',
			'quest_reward',
			'quest_required',
			'achievement',
			'fate',
			'job_category',
			'job_job_category',
			'job',
			'venture',
			'item_venture',
			'leve',
			'leve_reward',
			'leve_required',
			'item_category',
			'item',
			// 'attribute',
			'item_attribute',
			'recipe',
			'recipe_reagents',
			'career',
			'career_job',
		];

		foreach ($tables as $table)
			Schema::dropIfExists($table);
	}

}

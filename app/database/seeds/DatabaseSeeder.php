<?php

class DatabaseSeeder extends Seeder
{

	public function run()
	{
		Eloquent::unguard();

		// Don't bother logging queries
		DB::connection()->disableQueryLog();

		$this->call('StatTableSeeder');

		$this->call('JobTableSeeder');

		$this->call('SlotTableSeeder');

		$this->call('DataTablesSeeder');

		$this->call('QuestSeeder');

		echo "\n";
	}

}

class StatTableSeeder extends Seeder
{

	public function run()
	{
		$stats = array(
			// Custom Attributes
			'ALL' => array(
				'Materia',
				'Defense', 
			),
			'DOL' => array(
				'Gathering', 
				'GP',
				'Perception', 
			),
			'DOH' => array(
				'Control', 
				'CP', 
				'Craftsmanship', 
			),
			'DOW' => array(
				'Strength', 
				'Vitality',
				'Dexterity', 

				'Physical Damage', 
				'Skill Speed', 

				'Block Rate', 
				'Block Strength', 
				'Parry', 
				'Determination', 
			),
			'DOM' => array(
				'Intelligence',
				'Mind',
				'Piety', 

				'Magic Damage',
				'Spell Speed', 
			),
			'NA' => array(
				// Actual Attributes
				'Accuracy', 
				'Auto-Attack', 
				'Critical Hit Rate', 
				'Delay', 
				'DPS', 
				'Magic Defense',
				
				'Increased Spiritbond Gain',
				'Reduced Durability Loss', 
				
				'Blind Resistance', 
				'Blunt Resistance',
				'Earth Resistance', 
				'Fire Resistance', 
				'Heavy Resistance',
				'Ice Resistance',
				'Lightning Resistance',
				'Paralysis Resistance',
				'Piercing Resistance', 
				'Poison Resistance', 
				'Silence Resistance', 
				'Slashing Resistance', 
				'Sleep Resistance', 
				'Water Resistance',
				'Wind Resistance',
			),
		);

		foreach ($stats as $focus => $attributes)
			foreach ($attributes as $stat)
				Stat::create(array(
					'name' => $stat,
					'disciple_focus' => $focus
				));
	}
	
}

class JobTableSeeder extends Seeder
{

	public function run()
	{
		$jobs = array(
			'DOH' => array(
				'BSM' => 'Blacksmith',
				'GSM' => 'Goldsmith',
				'ARM' => 'Armorer',
				'CRP' => 'Carpenter',
				'LTW' => 'Leatherworker',
				'WVR' => 'Weaver',
				'ALC' => 'Alchemist',
				'CUL' => 'Culinarian',
			),

			'DOL' => array(
				'FSH' => 'Fisher',
				'BTN' => 'Botanist',
				'MIN' => 'Miner',
			),

			'DOW' => array(
				'GLA' => 'Gladiator',
				'PGL' => 'Pugilist',
				'MRD' => 'Marauder',
				'LNC' => 'Lancer',
				'ARC' => 'Archer',

				'MNK' => 'Monk',
				'PLD' => 'Paladin',
				'WAR' => 'Warrior',
				'DRG' => 'Dragoon',
				'BRD' => 'Bard',
			),

			'DOM' => array(
				'CNJ' => 'Conjurer',
				'THM' => 'Thaumaturge',
				'ACN' => 'Arcanist',

				'SCH' => 'Scholar',
				'SMN' => 'Summoner',
				'BLM' => 'Black Mage',
				'WHM' => 'White Mage',
			),

		);

		foreach ($jobs as $disciple => $classes)
			foreach ($classes as $abbr => $job)
				Job::create(array(
					'abbreviation' => $abbr,
					'name' => $job,
					'disciple' => $disciple
				));
	}
	
}

class SlotTableSeeder extends Seeder
{
	public function run()
	{
		$slots = array(
			'equipment' => array(
				'Primary',
				'Secondary',
				'Head',
				'Body',
				'Hands',
				'Waist',
				'Legs',
				'Feet',
				'Neck',
				'Ears',
				'Wrists',
				'Ring',
				'Ring',
			),
			'materia' => array(
				'Materia',
			),
			'food' => array(
				'Food',
			),
			'reagent' => array(
				'Reagent'
			),
		);

		foreach ($slots as $type => $s)
			foreach ($s as $key => $slot)
				Slot::create(array(
					'name' => $slot,
					'rank' => $key,
					'type' => $type
				));
	}
	
}

class DataTablesSeeder extends Seeder
{
	private $batch_limit = 100;

	public
		$stats = array(),
		$jobs = array(),
		$job_names = array(),
		$slots = array(),
		$disciples = array();

	public function run()
	{
		// Get stats, jobs and equipment slots
		foreach (Stat::all() as $stat)
			$this->stats[$stat->name] = $stat->id;

		foreach (Slot::all() as $slot)
			$this->slots[$slot->name] = $slot->id;

		foreach (Job::all() as $job)
		{
			if ( ! isset($this->disciples[$job->disciple]))
				$this->disciples[$job->disciple] = array();
			$this->disciples[$job->disciple][] = $job->abbreviation;
			$this->jobs[$job->abbreviation] = $this->job_names[$job->name] = $job->id;
		}

		// Custom job ID
		$this->job_names['Achievement:'] = 99;

		// Import item files
		$this->items(json_decode(file_get_contents(storage_path() . '/raw-data/items.json')));
		$this->items(json_decode(file_get_contents(storage_path() . '/raw-data/reagents.json')));

		// Import recipes
		$this->recipes(json_decode(file_get_contents(storage_path() . '/raw-data/recipes.json')));
	}

	private function recipes($data = array())
	{
		echo "+\n";
		
		$recipes = array();
		$reagents = array();

		foreach ($data as $object)
		{
			$recipes[] = array(
				'id' => $object->id,
				'item_id' => $object->item_id,
				'job_id' => $this->jobs[$object->class],
				'name' => $object->name,
				'yields' => $object->yields,
				'level' => $object->level, 
				'job_level' => $object->crafting_level
			);

			// Ingredients
			foreach ($object->ingredients as $ingredient)
				$reagents[] = array(
					'recipe_id' => $object->id,
					'item_id' => $ingredient->id,
					'amount' => $ingredient->required
				);

			// Attempt a batch insert every $this->batch_limit

			if (count($recipes) > $this->batch_limit)
				$recipes = $this->_batch_insert($recipes, 'recipes');

			if (count($reagents) > $this->batch_limit)
				$reagents = $this->_batch_insert($reagents, 'item_recipe');
		}

		// Insert the straglers
		if ($recipes)
			$this->_batch_insert($recipes, 'recipes');

		if ($reagents)
			$this->_batch_insert($reagents, 'item_recipe');
	}

	private function items($data = array())
	{
		echo "+\n";

		$items = array();
		$classes = array();
		$stats = array();

		foreach ($data as $object)
		{
			// Cleanup
			if (in_array($object->slot, array('Left', 'Right')))
				$object->slot = 'Ring';

			// Item ID is either set or based on the HREF
			$item_id = isset($object->xivdb_id) ? $object->xivdb_id : preg_replace('/^.*\/(\d+)\/.*$/', '$1', trim($object->href));

			// Base Item
			$items[] = array(
				'id' => $item_id,
				'name' => $object->name,
				'href' => $object->href,
				'level' => $object->level ?: 0,
				'vendors' => $object->vendors ?: 0,
				'gil' => $object->gil ?: 0,
				#'crafted_by' => isset($object->crafted_by) ? $this->job_names[trim($object->crafted_by)] : 0,
				'slot_id' => $this->slots[$object->slot],
				'ilvl' => $object->ilvl ?: 0
			);

			// Item Classes
			foreach ($object->class as $class)
			{
				if (in_array($class, array_keys($this->disciples)))
					foreach ($this->disciples[$class] as $cls)
						$classes[] = array(
							'item_id' => $item_id,
							'job_id' => $this->jobs[$cls]
						);
				else
					$classes[] = array(
						'item_id' => $item_id,
						'job_id' => $this->jobs[$class]
					);
			}

			// Item Stats

			$attributes = (Array) $object->attributes;

			if (isset($attributes['Duration']))
				unset($attributes['Duration']);

			foreach ($attributes as $a_name => $value)
			{
				$maximum = 0;

				// Amount looks like this: 10%(Max: 10)
				if ($object->slot == 'Food' && preg_match('/^(\d+).*\s(\d+)\)$/', $value, $matches))
					list($ignore, $value, $maximum) = $matches;

				$stats[] = array(
					'item_id' => $item_id,
					'stat_id' => $this->stats[$a_name],
					'amount' => $value,
					'maximum' => $maximum
				);
			}

			// Attempt a batch insert every $this->batch_limit

			if (count($items) > $this->batch_limit)
				$items = $this->_batch_insert($items, 'items');

			if (count($classes) > $this->batch_limit)
				$classes = $this->_batch_insert($classes, 'item_job');

			if (count($stats) > $this->batch_limit)
				$stats = $this->_batch_insert($stats, 'item_stat');
		}

		// Insert the straglers
		if ($items)
			$this->_batch_insert($items, 'items');

		if ($classes)
			$this->_batch_insert($classes, 'item_job');

		if ($stats)
			$this->_batch_insert($stats, 'item_stat');
	}

	private function _batch_insert($data = array(), $table = '')
	{
		static $count = 0;
		$count++;

		echo '.';
		if ($count % 46 == 0)
			echo "\n";

		if ( ! $table)
			return array();

		$values = $pdo = array();
		foreach ($data as $row)
		{
			$values[] = '(' . str_pad('', count($row) * 2 - 1, '?,') . ')';
			
			foreach ($row as $value)
				$pdo[] = $value;
		}

		# cli crashing... debug
		#file_put_contents('app/storage/logs/query-' . $count . '-statement.txt', 'INSERT INTO ' . $table . ' (`' . implode('`,`', array_keys($data[0])) . '`) VALUES ' . implode(',', $values));
		#file_put_contents('app/storage/logs/query-' . $count . '-pdo.txt', json_encode($pdo)); 

		DB::insert('INSERT INTO ' . $table . ' (`' . implode('`,`', array_keys($data[0])) . '`) VALUES ' . implode(',', $values), $pdo);

		// Return a blank array on purpose, to represent an empty $data
		return array();
	}

}

class QuestSeeder extends Seeder
{
	public $manual_items = array(
		'5599' => 'Grade 1 Carbonized Matter',
		'4874' => 'Harbor Herring',
		'4963' => 'Shadow Catfish',
		'5033' => 'Desert Catfish',
		'4917' => 'Mazlaya Marlin',
		'4564' => 'Antidote',
		'4597' => 'Potion of Intelligence',
		'4595' => 'Potion of Dexterity',
		'4575' => 'Weak Blinding Potion',
		'4870' => 'Lominsan Anchovy',
		'1123' => 'Ether',
		'4599' => 'Hi-Potion of Strength',
		'4607' => 'Mega-Potion of Intelligence',
		'4606' => 'Mega-Potion of Vitality',
	);

	public function run()
	{
		// Get all jobs
		foreach (Job::all() as $job)
			$jobs[$job->abbreviation] = $job->id;

		// Insert the manual items (Didn't crawl for them)
		foreach ($this->manual_items as $id => $name)
			DB::table('items')->insert(array(
				'id' => $id,
				'name' => $name
			));

		// Insert quest items
		$quest_items = json_decode(file_get_contents(storage_path() . '/raw-data/quest_items.json'));

		foreach ($quest_items as $item)
			DB::table('quest_items')->insert(array(
				'item_id' => $item->id,
				'job_id' => $jobs[$item->job],
				'level' => $item->level,
				'amount' => $item->amount,
				'quality' => $item->quality,
				'notes' => $item->notes,
			));
	}

}
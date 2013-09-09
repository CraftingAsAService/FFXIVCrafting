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
			'Materia',
			'Food'
		);

		foreach ($slots as $key => $slot)
			Slot::create(array(
				'name' => $slot,
				'rank' => $key,
				'type' => in_array($slot, array('Materia', 'Food')) ? strtolower($slot) : 'equipment'
			));
	}
	
}

class DataTablesSeeder extends Seeder
{

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

		// Import Materia file
		$this->items(json_decode(file_get_contents(storage_path() . '/raw-data/items.json')));
	}

	private function items($data = array())
	{
		// We just cleared the database, so we're starting at 1;
		static $item_id = 0; // Will be incremented to 1

		$items = array();
		$classes = array();
		$stats = array();

		foreach ($data as $key => $object)
		{
			$item_id++;

			// Cleanup
			if (in_array($object->slot, array('Left', 'Right')))
				$object->slot = 'Ring';

			// Base Item
			$items[] = array(
				'id' => $item_id,
				'name' => $object->name,
				'href' => $object->href,
				'level' => $object->level ?: 0,
				'vendors' => $object->vendors ?: 0,
				'gil' => $object->gil ?: 0,
				'crafted_by' => $object->crafted_by ? $this->job_names[trim($object->crafted_by)] : 0,
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

			// Attempt a batch insert every 100

			if (count($items) > 250)
				$items = $this->_batch_insert($items, 'items');

			if (count($classes) > 250)
				$classes = $this->_batch_insert($classes, 'item_job');

			if (count($stats) > 250)
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
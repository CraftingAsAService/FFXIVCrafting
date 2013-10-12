<?php

class DatabaseSeeder extends Seeder
{

	public function run()
	{
		Eloquent::unguard();

		// Don't bother logging queries
		DB::connection()->disableQueryLog();

		echo '** Jobs Table **' . "\n";
		$this->call('JobTableSeeder');

		echo '** Item Tables **' . "\n";
		$this->call('ItemTablesSeeder');

		echo '** Recipe Table **' . "\n";
		$this->call('RecipeTablesSeeder');

		echo '** Quests Table **' . "\n";
		$this->call('QuestSeeder');

		echo '** Leve Table **' . "\n";
		$this->call('LeveSeeder');

		echo '** XP Table **' . "\n";
		$this->call('XPSeeder');

		echo "\n";
	}

}

class _CommonSeeder extends Seeder
{

	public $batch_limit = 100;

	public
		$jobs = array(),
		$job_names = array();

	public function __construct()
	{
		parent::__construct();

		foreach (Job::all() as $job)
			$this->jobs[$job->abbreviation] = $this->job_names[$job->name] = $job->id;
	}

	public function _batch_insert($original_data = array(), $table = '')
	{
		static $count = 0;

		foreach(array_chunk($original_data, $this->batch_limit) as $data)
		{
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
				
				// Cleanup value, if FALSE set to NULL
				foreach ($row as $value)
					$pdo[] = $value === FALSE ? NULL : $value;
			}

			# cli crashing... debug
			#file_put_contents('app/storage/logs/query-' . $count . '-statement.txt', 'INSERT INTO ' . $table . ' (`' . implode('`,`', array_keys($data[0])) . '`) VALUES ' . implode(',', $values));
			#file_put_contents('app/storage/logs/query-' . $count . '-pdo.txt', json_encode($pdo)); 

			DB::insert('INSERT IGNORE INTO ' . $table . ' (`' . implode('`,`', array_keys($data[0])) . '`) VALUES ' . implode(',', $values), $pdo);
		}
	}
}

class JobTableSeeder extends _CommonSeeder
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

			'ALL' => array(
				'ALL' => 'All',
			)

		);

		$batch_jobs = array();

		foreach ($jobs as $disciple => $classes)
			foreach ($classes as $abbr => $job)
				$batch_jobs[] = array(
					'abbreviation' => $abbr,
					'name' => $job,
					'disciple' => $disciple
				);

		$this->_batch_insert($batch_jobs, 'jobs');
		unset($batch_jobs);
	}
	
}

class ItemTablesSeeder extends _CommonSeeder
{
	
	public function run()
	{
		// Stats
		$stats = json_decode(file_get_contents(storage_path() . '/seed-data/stats.json'), TRUE); // Decode to Array instead of Object
		$this->_batch_insert($stats, 'stats');
		unset($stats);

		// Item Stat
		$item_stat = json_decode(file_get_contents(storage_path() . '/seed-data/item_stat.json'), TRUE); // Decode to Array instead of Object
		$this->_batch_insert($item_stat, 'item_stat');
		unset($item_stat);

		// Locations
		$locations = json_decode(file_get_contents(storage_path() . '/seed-data/locations.json'), TRUE); // Decode to Array instead of Object
		$this->_batch_insert($locations, 'locations');
		unset($locations);

		// Gathering Nodes
		$gathering_nodes = json_decode(file_get_contents(storage_path() . '/seed-data/gathering_nodes.json'), TRUE); // Decode to Array instead of Object
		foreach ($gathering_nodes as &$node)
		{
			$node['job_id'] = $this->jobs[$node['class']];
			unset($node['class']);
		}
		$this->_batch_insert($gathering_nodes, 'gathering_nodes');
		unset($gathering_nodes);

		// Gathering Node Items
		$gathering_node_item = json_decode(file_get_contents(storage_path() . '/seed-data/gathering_node_item.json'), TRUE); // Decode to Array instead of Object
		$this->_batch_insert($gathering_node_item, 'gathering_node_item');
		unset($gathering_node_item);

		// Vendors
		$vendors = json_decode(file_get_contents(storage_path() . '/seed-data/vendors.json'), TRUE); // Decode to Array instead of Object
		$this->_batch_insert($vendors, 'vendors');
		unset($vendors);

		// Item Vendor
		$item_vendor = json_decode(file_get_contents(storage_path() . '/seed-data/item_vendor.json'), TRUE); // Decode to Array instead of Object
		$this->_batch_insert($item_vendor, 'item_vendor');
		unset($item_vendor);

		// Import item files
		$items = json_decode(file_get_contents(storage_path() . '/seed-data/items.json')), TRUE);
		
		$item_job = array();
		foreach ($items as &$item)
		{
			//"requires":["GLA","PLD"]
			foreach ($item['requires'] as $r)
				$item_job[] = array(
					'item_id' => $item['id'],
					'job_id' => $this->jobs[$node['class']]
				);

			unset($item['requires']);
			unset($item['is_gm']);
		}
		
		$this->_batch_insert($items, 'items');
		unset($items);
	}

}

class RecipeTablesSeeder extends _CommonSeeder
{
	public function run()
	{
		// Import recipes
		$recipes = json_decode(file_get_contents(storage_path() . '/seed-data/recipes.json'), TRUE);

		foreach ($recipes as &$recipe)
		{
			// Transalte class
			$recipe['job_id'] = $this->job_names[$recipe['class']];
			unset($recipe['class']);
		}

		$this->_batch_insert($recipes, 'recipes');
		unset($recipes);

		// Recipe Reagents
		$item_recipe = json_decode(file_get_contents(storage_path() . '/seed-data/item_recipe.json'), TRUE);
		$this->_batch_insert($item_recipe, 'item_recipe');
		unset($item_recipe);
	}

}

class QuestSeeder extends _CommonSeeder
{

	public function run()
	{
		// Insert quest items
		$quest_items = json_decode(file_get_contents(storage_path() . '/seed-data/quest_items.json'), TRUE); // As array

		foreach ($quest_items as &$item)
		{
			$item['job_id'] = $jobs[$item->job];
			unset($item['job']);
		}
		
		$this->_batch_insert($quest_items, 'quest_items');
		unset($quest_items);
	}
}

class LeveSeeder extends _CommonSeeder
{
	public $locations = array(),
			$location_id = 0;

	public function get_location_id($location_name = '')
	{
		if ( ! isset($this->locations[$location_name]))
		{
			DB::table('locations')->insert(array(
				'id' => ++$this->location_id,
				'name' => $location_name
			));

			$this->locations[$location_name] = $this->location_id;
		}

		return $this->locations[$location_name];
	}

	public function run() 
	{
		// Get all locations
		foreach (Location::all() as $location)
			$this->locations[$location->name] = $location->id;

		// Set the max id, incase new ones need to be added (which they will be)
		$this->location_id = max($this->locations);


		// Import leves
		$leves = json_decode(file_get_contents(storage_path() . '/seed-data/leves.json'), TRUE);
		
		foreach ($leves as &$leve)
		{
			// "class":"Blacksmith",
			$leve['job_id'] = $this->job_names[$leve['class']];
			unset($leve['class']);

			// "major_location":"Limsa Lominsa",
			// "minor_location":"",
			// "location":"",
			foreach (array('major_', 'minor_', '') as $prefix)
			{
				$var = $prefix . 'location';
				$val = NULL;

				if ($leve[$var])
					$val = $this->get_location_id($leve[$var]);

				$leve[$var . '_id'] = $val;

				unset($leve[$var]);
			}

			// "item_name":"Bronze Hatchet",
				# OR
			// "item_id":2703
			if (isset($leve['item_name']))
			{
				$item = Item::where('name', $leve['item_name'])->first();

				$leve['item_id'] = $item->id;

				unset($leve['item_name']);
			}
		}

		$this->_batch_insert($leves, 'leves');
		unset($leves);
	}

}

class XPSeeder extends _CommonSeeder
{

	public function run() 
	{
		// Insert experience records
		$experience = json_decode(file_get_contents(storage_path() . '/seed-data/experience.json'), TRUE);
		$this->_batch_insert($experience, 'experience');
		unset($experience);
	}

}
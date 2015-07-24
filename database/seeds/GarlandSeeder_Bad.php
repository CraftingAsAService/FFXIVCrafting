<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use DB;

class GarlandSeeder_Bad extends Seeder
{

	protected $ffxivcrafting_repo = 'ffxivcrafting',
				$aspir_repo = 'aspir',
				$garland_path = '',
				$data_path = '',
				$core = [];

	public function run()
	{
		/**
		 * Decided against this version
		 */
		return;


		set_time_limit(0);

		$start = new DateTime('now');

		if (app()->environment('local'))
			$this->local();
		else
			$this->server();
		
		echo "\n" . 'Time Elapsed: ' . $start->diff(new DateTime('now'))->format('%I:%S') . "\n";

		return;
	}

	private function server()
	{
		echo "\n" . 'Run reload_server instead!' . "\n";
	}

	private function local()
	{
		Model::unguard();

		// Don't bother logging queries
		DB::connection()->disableQueryLog();

		echo "\n" . '** Initializing Translations **' . "\n";
		TranslationsMapper::init();

		// We want the storage path in another repository
		// It exists at the same level as this repository
		// Just replace the names
		$storage_path = preg_replace('/' . $this->ffxivcrafting_repo . '/', $this->aspir_repo, storage_path());
		$this->garland_path = $storage_path . '/garland';

		$this->core = json_decode(file_get_contents($this->garland_path . '/core.json'));

		// dd($this->core->mob->index);
		// dd($this->core->npc->index);
		// dd($this->core->node->index);
		// dd($this->core);

		// Items
		$this->items();





	}

	private function items()
	{
		$item_image_path = base_path() . '/resources/images/items';

		// Loop through the items
		foreach ($this->core->gItemIndex as $item)
		{
			$item_id = $item->i;
			$item = [];

			// Item Data
			$item_json_path = $this->garland_path . '/db/data/item/' . $item_id . '.json';
			if ( ! is_file($item_json_path))
				exit('Item Not Found: ' . __LINE__);
			$i = (array) $this->get_cleaned_json($item_json_path)->item;

			// Basic data
			$item['id'] = $item_id;
			$item['name_en'] = TranslationsMapper::get($i['name']);
			$item['level'] = $i['ilvl'];
			$item['min_price'] = $i['sell_price'];
			$item['max_price'] = $i['price'];
			$item['untradable'] = ! $i['tradeable'];
			$item['rarity'] = $i['rarity'];

			// May change later
			$item['dungeon_drop'] = 0;
			$item['rewarded'] = 0;

			// Connect to the mobs
			if (isset($i['drops']))
			{
				foreach ($i['drops'] as $mob_id)
				{
					$mob_data = $this->core->mob->index->$mob_id;
					
					// 'id','int(10) unsigned','NO','PRI',NULL,'auto_increment'
					// 'npcs_id','bigint(20) unsigned','NO','MUL',NULL,''
					// 'item_id','int(10) unsigned','NO','',NULL,''

					$mob = \App\Models\CAAS\NPC::findOrNew($mob_id);
					if ( ! $mob->exists)
					{
						$mob->genus = 'beast';
						$mob->name_en = TranslationsMapper::get($mob_data['name']);
						$mob->save();
					}

					// Attach the item to the npc
					$count = DB::table('npcs_items')
						->where('npcs_id', $mob_id)
						->where('item_id', $item_id)
						->count();

					if ( ! $count)
						DB::table('npcs_items')->insert([
							'npcs_id' => $mob_id,
							'item_id' => $item_id,
						]);
				}

				$item['dungeon_drop'] = 1;

				// Cleanup
				unset($mob, $count, $mob_data, $i['drops']);
			}

			if (isset($i['nodes']))
			{
				foreach ($i['nodes'] as $node)
				{
					$node = $this->core->node->index->$node;
					dd($node);

					// Clusters
					// 
					// 'id','int(10) unsigned','NO','PRI',NULL,'auto_increment'
					// 'placename_id','int(11)','NO','MUL',NULL,''
					// 'classjob_id','int(11)','NO','MUL',NULL,''
					// 'level','smallint(6)','NO','',NULL,''
					// 'icon','varchar(10)','NO','',NULL,''
					// 'x','decimal(7,4)','NO','',NULL,''
					// 'y','decimal(7,4)','NO','',NULL,''
					// 
					// Cluster Items
					// 'id','int(10) unsigned','NO','PRI',NULL,'auto_increment'
					// 'cluster_id','int(11)','NO','MUL',NULL,''
					// 'item_id','int(11)','NO','',NULL,''

					// Identify placename
					



					// $node['lvl']



				}
			}


			// ventures ??
			// 
// 'achievable','tinyint(1)','NO','',NULL,''

			if (isset($i['quests']))
			{
				$item['rewarded'] = 1;
				unset($i['quests']);
			}


			// Item Category
			$category = $this->core->item->categoryIndex->$i['category']->name;
			// Garland Categories != Libra Categories, translate the id
			$translate_id = TranslationsMapper::get($category);
			$item['itemuicategory_id'] = DB::table('item_ui_category')->select('id')->where('name_en', $translate_id)->pluck('id');

			if (empty($item['itemuicategory_id']))
				exit('Category Not Found: ' . __LINE__); // Todo, insert new category

			// Item Icon
			$file = $this->garland_path . '/db/icons/item/' . $i['icon'] . '.png';
			$file2 = $this->garland_path . '/db/icons/item/' . $i['icon'] . '.png';

			if ( ! is_file($file))
				$file = $file2;

			if (is_file($file))
				copy($file, $item_image_path . '/nq/' . $item_id . '.png');
			



			// Unset some basic fields
			unset($i['id'], $i['ver'], $i['help'], $i['category'], $i['name'], $i['ilvl'], $i['sell_price'], $i['price'], $i['tradeable'], $i['rarity'], $i['icon']);

			if (count($i))
				dd($item_id, $i, '^ Remaining items to convert or mark as ignored.');

			$i = \App\Models\CAAS\Item::findOrNew($item_id);
			$i->fill($item)->save();

			echo 'Saved Item ' . $i->id . PHP_EOL;
		}
	}













	/**
	 * Helper Functions
	 */

	private function get_cleaned_json($path)
	{
		$content = stripslashes(file_get_contents($path));

		// http://stackoverflow.com/questions/17219916/json-decode-returns-json-error-syntax-but-online-formatter-says-the-json-is-ok
		for ($i = 0; $i <= 31; ++$i) { 
			$content = str_replace(chr($i), "", $content); 
		}
		$content = str_replace(chr(127), "", $content);

		// This is the most common part
		$content = $this->binary_fix($content);

		return json_decode($content);
	}

	private function binary_fix($string)
	{
		// Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
		// here we detect it and we remove it, basically it's the first 3 characters 
		if (0 === strpos(bin2hex($string), 'efbbbf')) {
		   $string = substr($string, 3);
		}
		return $string;
	}

}

class TranslationsMapper extends Seeder
{
	private static $t = [],
					$new = [],
					$id = 0;

	public static function init()
	{
		// Load all existing translations, put them in array
		// Term is the 'key', id is the value
		self::$t = DB::table('translations')->lists('id', 'term');
		self::$id = (count(self::$t) ? max(self::$t) : 0) + 1;
	}

	// Receive a term, return an id
	public static function get($term)
	{
		// If the term starts with a digit...
		if (preg_match('/^\d/', $term))
			$term = '\x20' . $term; // Add a space to the beginning

		if ( ! in_array($term, array_keys(self::$t)))
		{
			self::$new[$term] = ++self::$id;
			self::$t[$term] = self::$id;
		}

		return self::$t[$term];
	}

	// set the new ones
	public static function set()
	{
		$batch = [];
		foreach (self::$new as $term => $id)
			$batch[] = [
				'id' => $id,
				'term' => $term
			];

		$batch = Batch::insert($batch, 'translations');
	}

}

class Batch extends Seeder
{
	public static $batch_limit = 300;

	public static function insert($original_data = [], $table = '', $tsv = FALSE)
	{
		$count = 0;

		if ( ! $table || empty($original_data))
			return [];

		if ($tsv)
			$original_data = explode("\n", $original_data);

		// foreach(array_chunk($original_data, $bl) as $data)
		foreach(array_chunk($original_data, self::$batch_limit) as $data)
		{
			$count++;

			echo '.';
			if ($count % 46 == 0)
				echo "\n";

			$values = $pdo = [];
			foreach ($data as $row)
			{
				if ($tsv)
					$row = explode("\t", $row);

				$values[] = '(' . str_pad('', count($row) * 2 - 1, '?,') . ')';
				
				// Cleanup value, if FALSE set to NULL
				foreach ($row as $value)
					$pdo[] = $value === FALSE ? NULL : $value;
			}

			# cli crashing... debug
			#file_put_contents('app/storage/logs/query-' . $count . '-statement.txt', 'INSERT INTO ' . $table . ' (`' . implode('`,`', array_keys($data[0])) . '`) VALUES ' . implode(',', $values));
			#file_put_contents('app/storage/logs/query-' . $count . '-pdo.txt', json_encode($pdo)); 

			$keys = $tsv ? '' : ' (`' . implode('`,`', array_keys($data[0])) . '`)';

			DB::insert('INSERT IGNORE INTO ' . $table . $keys . ' VALUES ' . implode(',', $values), $pdo);
		}
	
		echo "\n";

		return [];
	}

}

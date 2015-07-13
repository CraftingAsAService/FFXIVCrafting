<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{

	public function run()
	{
		echo 'RIP Libra App' . PHP_EOL;
		return;

		
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
		Eloquent::unguard();

		// Don't bother logging queries
		\DB::connection()->disableQueryLog();

		echo "\n" . '** Initializing Translations **' . "\n";
		TranslationsMapper::init();

		echo "\n" . '** Race **' . "\n";
		$this->call('RaceSeeder');
		echo "\n" . '** RecipeElement **' . "\n";
		$this->call('RecipeElementSeeder');
		echo "\n" . '** NotebookDivision **' . "\n";
		$this->call('NotebookDivisionSeeder');
		echo "\n" . '** GuardianDeity **' . "\n";
		$this->call('GuardianDeitySeeder');
		echo "\n" . '** ClassJob **' . "\n";
		$this->call('ClassJobSeeder');
		echo "\n" . '** ClassJobCategory **' . "\n";
		$this->call('ClassJobCategorySeeder');
		echo "\n" . '** PlaceName **' . "\n";
		$this->call('PlaceNameSeeder');
		echo "\n" . '** ItemUIKind **' . "\n";
		$this->call('ItemUIKindSeeder');
		echo "\n" . '** ItemUICategory **' . "\n";
		$this->call('ItemUICategorySeeder');
		echo "\n" . '** ItemCategory **' . "\n";
		$this->call('ItemCategorySeeder');
		echo "\n" . '** ItemSeries **' . "\n";
		$this->call('ItemSeriesSeeder');
		echo "\n" . '** ItemSpecialBonus **' . "\n";
		$this->call('ItemSpecialBonusSeeder');
		echo "\n" . '** BaseParam **' . "\n";
		$this->call('BaseParamSeeder');
		echo "\n" . '** Item **' . "\n";
		$this->call('ItemSeeder');
		echo "\n" . '** BNpcName **' . "\n";
		$this->call('BNpcNameSeeder');
		echo "\n" . '** ENpcResident **' . "\n";
		$this->call('ENpcResidentSeeder');
		echo "\n" . '** Shop **' . "\n";
		$this->call('ShopSeeder');
		echo "\n" . '** Recipe **' . "\n";
		$this->call('RecipeSeeder');
		echo "\n" . '** Experience **' . "\n";
		$this->call('XPSeeder');
		echo "\n" . '** Careers **' . "\n";
		$this->call('CareerSeeder');
		echo "\n" . '** Clusters **' . "\n";
		$this->call('ClusterSeeder');

		// Setting translations early as Leves and Quests need that data
		echo "\n" . '** Setting Translations **' . "\n";
		TranslationsMapper::set();

		echo "\n" . '** Crafting Quests **' . "\n";
		$this->call('QuestSeeder');
		echo "\n" . '** Crafting Leves **' . "\n";
		$this->call('LeveSeeder');
	}

}

// Useful SQL

// select bp.id, t_en.term from baseparam as bp join translations as t_en on t_en.id = bp.name_en


class _LibraSeeder extends Seeder
{

	public function get_json($filename = '', $directory = 'app/libra')
	{
		return json_decode(file_get_contents(storage_path() . '/' . $directory . '/' . $filename . '.json'), TRUE); // Decode to Array instead of Object
	}

	public function common_run($table_name = '', $data = array())
	{
		$batch = array();

		foreach ($data as $row)
			$batch[] = array(
				'id' => $row['id'],
				'name_en' => TranslationsMapper::get($row['name']['en']),
				'name_ja' => TranslationsMapper::get($row['name']['ja']),
				'name_fr' => TranslationsMapper::get($row['name']['fr']),
				'name_de' => TranslationsMapper::get($row['name']['de'])
			);

		$batch = Batch::insert($batch, $table_name);
	}

}

class TranslationsMapper extends _LibraSeeder
{
	private static $t = array(),
					$new = array(),
					$id = 0;

	public static function init()
	{
		// Load all existing translations, put them in array
		// Term is the 'key', id is the value
		self::$t = \DB::table('translations')->lists('id', 'term');
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
		$batch = array();
		foreach (self::$new as $term => $id)
			$batch[] = array(
				'id' => $id,
				'term' => $term
			);

		$batch = Batch::insert($batch, 'translations');
	}

}

class Batch extends Seeder
{
	public static $batch_limit = 300;

	public static function insert($original_data = array(), $table = '', $tsv = FALSE)
	{
		$count = 0;

		if ( ! $table || empty($original_data))
			return array();

		if ($tsv)
			$original_data = explode("\n", $original_data);

		// foreach(array_chunk($original_data, $bl) as $data)
		foreach(array_chunk($original_data, self::$batch_limit) as $data)
		{
			$count++;

			echo '.';
			if ($count % 46 == 0)
				echo "\n";

			$values = $pdo = array();
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

			\DB::insert('INSERT IGNORE INTO ' . $table . $keys . ' VALUES ' . implode(',', $values), $pdo);
		}
	
		echo "\n";

		return array();
	}

}

class ClassJobSeeder extends _LibraSeeder
{

	public function run()
	{
		$batch = array();

		$data = $this->get_json('ClassJob')['data'];

		foreach ($data as $row)
			$batch[] = array(
				'id' => $row['id'],
				'is_job' => $row['is_job'],
				'rank' => $row['rank'],
				'name_en' => TranslationsMapper::get($row['name']['en']),
				'name_ja' => TranslationsMapper::get($row['name']['ja']),
				'name_fr' => TranslationsMapper::get($row['name']['fr']),
				'name_de' => TranslationsMapper::get($row['name']['de']),
				'abbr_en' => TranslationsMapper::get($row['abbr']['en']),
				'abbr_ja' => TranslationsMapper::get($row['abbr']['ja']),
				'abbr_fr' => TranslationsMapper::get($row['abbr']['fr']),
				'abbr_de' => TranslationsMapper::get($row['abbr']['de']),
			);

		$batch = Batch::insert($batch, 'classjob');
	}

}

class ClassJobCategorySeeder extends _LibraSeeder
{
	
	public function run()
	{
		// Will handle both classjob_category and classjob_classjob_category
		$cjc_batch = $cjcjc_batch = array();

		$data = $this->get_json('ClassJobCategory')['data'];

		foreach ($data as $row)
		{
			$cjc_batch[] = array(
				'id' => $row['id'],
				'name_en' => TranslationsMapper::get($row['name']['en']),
				'name_ja' => TranslationsMapper::get($row['name']['ja']),
				'name_fr' => TranslationsMapper::get($row['name']['fr']),
				'name_de' => TranslationsMapper::get($row['name']['de']),
			);

			foreach (explode(',', $row['classjob']) as $cj_id)
				$cjcjc_batch[] = array(
					'classjob_id' => $cj_id,
					'classjob_category_id' => $row['id']
				);
		}

		$cjc_batch = Batch::insert($cjc_batch, 'classjob_category');
		$cjcjc_batch = Batch::insert($cjcjc_batch, 'classjob_classjob_category');
	}

}

class PlaceNameSeeder extends _LibraSeeder
{

	public function run()
	{
		$batch = array();

		$data = $this->get_json('PlaceName')['data'];

		foreach ($data as $row)
			$batch[] = array(
				'id' => $row['id'],
				'region' => $row['region'],
				'name_en' => TranslationsMapper::get($row['name']['en']),
				'name_ja' => TranslationsMapper::get($row['name']['ja']),
				'name_fr' => TranslationsMapper::get($row['name']['fr']),
				'name_de' => TranslationsMapper::get($row['name']['de']),
			);

		$batch = Batch::insert($batch, 'place_name');
	}

}

class ItemUIKindSeeder extends _LibraSeeder
{

	public function run()
	{
		$this->common_run('item_ui_kind', $this->get_json('ItemUIKind')['data']);
	}

}

class ItemUICategorySeeder extends _LibraSeeder
{

	public function run()
	{
		$batch = array();

		$data = $this->get_json('ItemUICategory')['data'];

		foreach ($data as $row)
			$batch[] = array(
				'id' => $row['id'],
				'itemuikind_id' => $row['kind'],
				'rank' => $row['rank'],
				'name_en' => TranslationsMapper::get($row['name']['en']),
				'name_ja' => TranslationsMapper::get($row['name']['ja']),
				'name_fr' => TranslationsMapper::get($row['name']['fr']),
				'name_de' => TranslationsMapper::get($row['name']['de']),
			);

		$batch = Batch::insert($batch, 'item_ui_category');
	}

}

class ItemCategorySeeder extends _LibraSeeder
{

	public function run()
	{
		$this->common_run('item_category', $this->get_json('ItemCategory')['data']);
	}

}

class ItemSeriesSeeder extends _LibraSeeder
{

	public function run()
	{
		$this->common_run('item_series', $this->get_json('ItemSeries')['data']);
	}

}

class ItemSpecialBonusSeeder extends _LibraSeeder
{

	public function run()
	{
		$this->common_run('item_special_bonus', $this->get_json('ItemSpecialBonus')['data']);
	}

}

class BaseParamSeeder extends _LibraSeeder
{

	public function run()
	{
		$this->common_run('baseparam', $this->get_json('BaseParam')['data']);
	}

}

class ItemSeeder extends _LibraSeeder
{

	public function run()
	{
		$i_batch = $bpi_batch = $cji_batch = array();

		$data = $this->get_json('Item')['data'];

		// Reset icons file
		// $icons_file = storage_path() . '/app/libra/icons.txt';
		// file_put_contents($icons_file, '');

		// Manual transition for stat names
		$stat_names = array(
			'damage' => 'Physical Damage',
			'magic_damage' => 'Magic Damage',
			'defense' => 'Defense',
			'magic_defense' => 'Magic Defense',
			'shield_rate' => 'Block Rate',
			'shield_block_rate' => 'Block Strength',
			'attack_interval' => 'Attack Speed',
			'auto_attack' => 'Attack Power',
		);

		// Items is a very long process
		// Display that progress
		$i = 0;

		foreach ($data as $k => $row)
		{
			// Memory cleanup, we don't need this entry anymore in $data
			unset($data[$k]);

			if ($i++ % 20 == 0)
				echo 'i';
			// We don't need to worry about \n's, because we're inserting every 900

			$tmp = array(
				'id' => $row['id'],
				'itemcategory_id' => $row['category'],
				'itemuicategory_id' => $row['ui_category'],
				'classjobcategory_id' => isset($row['extra']['classjob_category']) ? $row['extra']['classjob_category'] : null,
				'name_en' => TranslationsMapper::get($row['ui_name']['en']),
				'name_ja' => TranslationsMapper::get($row['ui_name']['ja']),
				'name_fr' => TranslationsMapper::get($row['ui_name']['fr']),
				'name_de' => TranslationsMapper::get($row['ui_name']['de']),
				'level' => $row['level'],
				'equip_level' => $row['equip_level'],
				'rarity' => $row['rarity'],
				'has_hq' => $row['hq'],
				'itemseries_id' => $row['series'],
				'itemspecialbonus_id' => $row['special_bonus'],
				'slot' => $row['slot'],
				'min_price' => isset($row['price']) && isset($row['price']['min']) ? $row['price']['min'] : null,
				'max_price' => isset($row['price']) && isset($row['price']['max']) ? $row['price']['max'] : null,
				'untradable' => isset($row['extra']['untradable']) ? $row['extra']['untradable'] : null,
				'unique' => isset($row['extra']['unique']) ? $row['extra']['unique'] : null,
				'achievable' => isset($row['extra']['achievable']) ? $row['extra']['achievable'] : null,
				'rewarded' => isset($row['extra']['rewarded']) ? $row['extra']['rewarded'] : null,
				'dungeon_drop' => isset($row['extra']['dungeon_drop']) ? $row['extra']['dungeon_drop'] : null,
				'color' => isset($row['extra']['color']) ? $row['extra']['color'] : null,
				'materia' => isset($row['extra']['materia']) ? $row['extra']['materia'] : null,
				'rank' => $row['rank'],
			);
	
			// Class Job Items
			if ($row['classjob'])
				foreach (explode(',', $row['classjob']) as $cj_id)
					$cji_batch[] = array(
						'item_id' => $row['id'],
						'classjob_id' => $cj_id
					);

			// Icons
			// if ($row['icon']['nq'])
			// 	file_put_contents($icons_file, $row['id'] . "\tnq\t" . $row['icon']['nq'] . "\n", FILE_APPEND);
			// if ($row['icon']['hq'])
			// 	file_put_contents($icons_file, $row['id'] . "\thq\t" . $row['icon']['hq'] . "\n", FILE_APPEND);

			// Stats
			foreach ($row['stats']['nq'] as $name => $value)
			{
				$nq_val = $value;
				$hq_val = $row['stats']['hq'][$name];

				if ($nq_val == 0 && $hq_val == 0)
					continue;

				$bpi_batch[] = array(
					'item_id' => $row['id'],
					'baseparam_id' => TranslationsMapper::get($stat_names[$name]),
					'nq_amount' => $nq_val == 0 ? null : $nq_val,
					'hq_amount' => $hq_val == 0 ? null : $hq_val,
					'nq_limit' => null,
					'hq_limit' => null,
					'bonus' => null
				);
			}

			if (isset($row['extra']['stats']))
				foreach ($row['extra']['stats'] as $baseparam_id => $stat)
				{
					$nq_val = isset($stat['nq']) ? $stat['nq'] : 0;
					$hq_val = isset($stat['hq']) ? $stat['hq'] : 0;

					if ($nq_val == 0 && $hq_val == 0)
						continue;

					$bpi_batch[] = array(
						'item_id' => $row['id'],
						'baseparam_id' => $baseparam_id,
						'nq_amount' => $nq_val == 0 ? null : $nq_val,
						'hq_amount' => $hq_val == 0 ? null : $hq_val,
						'nq_limit' => null,
						'hq_limit' => null,
						'bonus' => null
					);
				}

			if (isset($row['extra']['bonus_stats']))
				foreach ($row['extra']['bonus_stats'] as $ignore => $bonus)
					foreach ($bonus as $baseparam_id => $value)
						$bpi_batch[] = array(
							'item_id' => $row['id'],
							'baseparam_id' => $baseparam_id,
							'nq_amount' => $value,
							'hq_amount' => 0,
							'nq_limit' => null,
							'hq_limit' => null,
							'bonus' => 1
						);
					
			if (isset($row['extra']['boost']))
				foreach ($row['extra']['boost'] as $baseparam_id => $boost)
					if ( ! is_array($boost))
						$bpi_batch[] = array(
							'item_id' => $row['id'],
							'baseparam_id' => $baseparam_id,
							'nq_amount' => $boost,
							'hq_amount' => null,
							'nq_limit' => null,
							'hq_limit' => null,
							'bonus' => null
						);
					else
					{
						if (isset($boost['nq']['rate']))
							$bpi_batch[] = array(
								'item_id' => $row['id'],
								'baseparam_id' => $baseparam_id,
								'nq_amount' => $boost['nq']['rate'],
								'hq_amount' => isset($boost['hq']) ? $boost['hq']['rate'] : null,
								'nq_limit' => $boost['nq']['limit'],
								'hq_limit' => isset($boost['hq']) ? $boost['hq']['limit'] : null,
								'bonus' => null
							);
						else
							$bpi_batch[] = array(
								'item_id' => $row['id'],
								'baseparam_id' => $baseparam_id,
								'nq_amount' => $boost['nq'][0],
								'hq_amount' => isset($boost['hq']) ? $boost['hq'][0] : null,
								'nq_limit' => null,
								'hq_limit' => null,
								'bonus' => null
							);
					}

			$i_batch[] = $tmp;

			// Due to memory issues, insert every 1000
			if (count($i_batch) >= 600)
				$i_batch = Batch::insert($i_batch, 'items');

			// Again, memory issues, but to a lesser extent
			if (count($bpi_batch) > 3000)
				$bpi_batch = Batch::insert($bpi_batch, 'baseparam_items');
			if (count($cji_batch) > 3000)
				$cji_batch = Batch::insert($cji_batch, 'classjob_items');
		}

		// Catch any stragglers
		$i_batch = Batch::insert($i_batch, 'items');
		$bpi_batch = Batch::insert($bpi_batch, 'baseparam_items');
		$cji_batch = Batch::insert($cji_batch, 'classjob_items');
		
	}

}

class RaceSeeder extends _LibraSeeder
{

	public function run()
	{
		$this->common_run('races', $this->get_json('Race')['data']);
	}

}

class BNpcNameSeeder extends _LibraSeeder
{

	public function run()
	{
		$npc_batch = $drop_batch = $loc_batch = array();

		$data = $this->get_json('BNpcName')['data'];

		foreach ($data as $row)
		{
			$npc_batch[] = array(
				'id' => $row['id'],
				'name_en' => TranslationsMapper::get($row['name']['en']),
				'name_ja' => TranslationsMapper::get($row['name']['ja']),
				'name_fr' => TranslationsMapper::get($row['name']['fr']),
				'name_de' => TranslationsMapper::get($row['name']['de']),
				'genus' => 'beast'
			);

			// Drops
			if (isset($row['extra']['drops']))
				foreach ($row['extra']['drops'] as $item_id)
					$drop_batch[] = array(
						'npcs_id' => $row['id'],
						'item_id' => $item_id
					);

			// Locations & Levels
			if (isset($row['extra']['location']))
				foreach ($row['extra']['location'] as $region => $areas)
					foreach ($areas as $area => $levels)
						foreach ($levels as $level_range)
							$loc_batch[] = array(
								'npcs_id' => $row['id'],
								'placename_id' => rtrim($area, '*'),
								'x' => null,
								'y' => null,
								'levels' => $level_range,
								'triggered' => preg_match('/\*/', $area) ? 1 : 0
							);
		}

		$npc_batch = Batch::insert($npc_batch, 'npcs');
		$drop_batch = Batch::insert($drop_batch, 'npcs_items');
		$loc_batch = Batch::insert($loc_batch, 'npcs_place_name');

		// echo "\n" . '** Setting Translations **' . "\n";
		// TranslationsMapper::set();
		// exit;
	}

}

class ENpcResidentSeeder extends _LibraSeeder
{

	public function run()
	{
		$npc_batch = $loc_batch = $shop_batch = $si_batch = array();

		$data = $this->get_json('ENpcResident')['data'];

		$i = 0;

		foreach ($data as $row)
		{
			// Not interested in non-shop npcs
			if ( ! isset($row['has_shop']) || $row['has_shop'] != '1')
				continue;

			$npc_batch[] = array(
				'id' => $row['id'],
				'name_en' => TranslationsMapper::get($row['name']['en']),
				'name_ja' => TranslationsMapper::get($row['name']['ja']),
				'name_fr' => TranslationsMapper::get($row['name']['fr']),
				'name_de' => TranslationsMapper::get($row['name']['de']),
				'genus' => 'shop'
			);

			// Location
			$x = $y = null;
			if (isset($row['extra']['coords']))
				// Only grabbing first coord entry, I don't think there's more than one
				list($x, $y) = explode(',', $row['extra']['coords'][0]);
			
			$loc_batch[] = array(
				'npcs_id' => $row['id'],
				'placename_id' => $row['area'],
				'x' => $x,
				'y' => $y,
				'levels' => null,
				'triggered' => 0
			);

			// Shops and Items
			if (isset($row['extra']['shop']))
				foreach ($row['extra']['shop'] as $shop_id => $inventory)
				{
					// Inserting now to get the resulted ID
					echo 'n'; if ($i++ % 46 == 0) echo "\n";
					$npcs_shop_id = \DB::table('npcs_shops')->insertGetId(
						array(
							'npcs_id' => $row['id'],
							'shop_id' => $shop_id
						)
					);

					// TODO? Idea: store color as R G B in three different tinyint columns

					foreach ($inventory as $item_id => $color)
						$si_batch[] = array(
							'item_id' => $item_id,
							'npcs_shop_id' => $npcs_shop_id,
							'color' => empty($color) ? null : $color
						);
				}
		}

		$npc_batch = Batch::insert($npc_batch, 'npcs');
		$loc_batch = Batch::insert($loc_batch, 'npcs_place_name');
		$si_batch = Batch::insert($si_batch, 'items_npcs_shops');
	}

}

class ShopSeeder extends _LibraSeeder
{

	public function run()
	{
		$this->common_run('shops', $this->get_json('Shop')['data']);
	}

}

class RecipeElementSeeder extends _LibraSeeder
{

	public function run()
	{
		$this->common_run('recipe_elements', $this->get_json('RecipeElement')['data']);
	}

}

class NotebookDivisionSeeder extends _LibraSeeder
{

	public function run()
	{
		$this->common_run('notebook_division', $this->get_json('NotebookDivision')['data']);
	}

}

class RecipeSeeder extends _LibraSeeder
{

	public function run()
	{
		$r_batch = $rr_batch = array();

		$data = $this->get_json('Recipe')['data'];

		foreach ($data as $row)
		{
			$r_batch[] = array(
				'id' => $row['id'],
				'item_id' => $row['item_id'],
				'classjob_id' => $row['classjob'],
				'element_id' => $row['element'],
				'can_hq' => $row['can_hq'],
				'yields' => $row['yields'],
				'level' => $row['level'],
				'level_view' => $row['levelView'],
				'stars' => $row['stars'],
				'req_craftsmanship' => $row['required']['craftsmanship'],
				'req_control' => $row['required']['control'],
				'durability' => $row['extra']['durability'],
				'max_quality' => $row['extra']['quality_max'],
				'difficulty' => $row['extra']['difficulty'],
				'rank' => $row['rank']
			);

			// Assumption, all extra items exist, not sure why I put them as extra...

			// Reagents
			foreach ($row['extra']['reagents'] as $item_id => $amount)
				$rr_batch[] = array(
					'recipe_id' => $row['id'],
					'item_id' => $item_id,
					'amount' => $amount
				);
		}

		$r_batch = Batch::insert($r_batch, 'recipes');
		$rr_batch = Batch::insert($rr_batch, 'recipe_reagents');
	}

}

class GuardianDeitySeeder extends _LibraSeeder
{

	public function run()
	{
		$this->common_run('guardians', $this->get_json('GuardianDeity')['data']);
	}

}

// Non-Libra data

class XPSeeder extends Seeder
{

	public function run() 
	{
		Batch::insert(
			json_decode(file_get_contents(storage_path() . '/app/libra/experience.json'), TRUE), 
			'experience'
		);
	}

}

class CareerSeeder extends Seeder
{

	public function run()
	{
		Batch::insert(
			json_decode(file_get_contents(storage_path() . '/app/libra/careers.json'), TRUE)['data'], 
			'careers'
		);
		Batch::insert(
			json_decode(file_get_contents(storage_path() . '/app/libra/career_classjob.json'), TRUE)['data'], 
			'career_classjob'
		);
	}

}

class ClusterSeeder extends Seeder
{

	public function run()
	{
		Batch::insert(
			file_get_contents(storage_path() . '/app/libra/clusters.tsv'),
			'clusters',
			TRUE
		);
		Batch::insert(
			file_get_contents(storage_path() . '/app/libra/cluster_nodes.tsv'),
			'cluster_nodes',
			TRUE
		);
		Batch::insert(
			file_get_contents(storage_path() . '/app/libra/cluster_items.tsv'),
			'cluster_items',
			TRUE
		);
	}

}

class QuestSeeder extends Seeder
{

	public function run()
	{
		// Get all Jobs abbreviations to id
		// MIN => #
		$job_to_id = \DB::table('classjob')
			->join('translations', 'translations.id', '=', 'classjob.abbr_en')
			->select('classjob.id', 'translations.term')
			->lists('id', 'term');

		// Insert quest items
		$quest_items = json_decode(file_get_contents(storage_path() . '/app/libra/quest_items.json'), TRUE); // As array

		foreach ($quest_items as &$item)
		{
			$item['classjob_id'] = $job_to_id[$item['job']];
			unset($item['job']);
		}
		
		$quest_items = Batch::insert($quest_items, 'quest_items');
	}
	
}

class LeveSeeder extends Seeder
{

	public function run() 
	{
		// Get all Jobs to id
		// Mining => #
		$job_name_to_id = \DB::table('classjob')
			->join('translations', 'translations.id', '=', 'classjob.name_en')
			->select('classjob.id', 'translations.term')
			->lists('id', 'term');

		// // Get all Jobs abbreviations to id
		// // MIN => #
		// $job_abbr_to_id = \DB::table('classjob')
		// 	->join('translations', 'translations.id', '=', 'classjob.abbr_en')
		// 	->select('classjob.id', 'translations.term')
		// 	->lists('id', 'term');

		// Get all Item names to id
		$item_to_id = \DB::table('items')
			->join('translations', 'translations.id', '=', 'items.name_en')
			->select('items.id', 'translations.term')
			->lists('id', 'term');

		$this->leves($job_name_to_id, $item_to_id);
		$this->leve_rewards($job_name_to_id, $item_to_id);
	}

	public function leves($job_name_to_id, $item_to_id)
	{
		// Import leves
		$leves = json_decode(file_get_contents(storage_path() . '/app/libra/improved-leves.json'), TRUE);
		
		foreach ($leves as &$leve)
		{
			// "class":"Blacksmith",
			$leve['classjob_id'] = $job_name_to_id[$leve['class']];
			unset($leve['class']);

			// "item_name":"Bronze Hatchet",
				# OR
			// "item_id":2703
			if (isset($leve['item_name']))
			{
				$leve['item_id'] = $leve['item_name'] && isset($item_to_id[$leve['item_name']])
					? $item_to_id[$leve['item_name']]
					: null;

				unset($leve['item_name']);
			}

			// Improved added min_xp and max_xp, as well as gil versions of that
			// Convert it to xp (a median) and xp_spread (+/-)
			$leve['xp'] = (int) (($leve['xp_max'] + $leve['xp_min']) / 2);
			$leve['xp_spread'] = (int) (($leve['xp_max'] - $leve['xp_min']) / 2);
			unset($leve['xp_max'], $leve['xp_min']);
			$leve['gil'] = (int) (($leve['gil_max'] + $leve['gil_min']) / 2);
			$leve['gil_spread'] = (int) (($leve['gil_max'] - $leve['gil_min']) / 2);
			unset($leve['gil_max'], $leve['gil_min']);
		}

		$leves = Batch::insert($leves, 'leves');
	}

	public function leve_rewards($job_name_to_id, $item_to_id)
	{
		// Import leves rewards
		$leve_rewards = json_decode(file_get_contents(storage_path() . '/app/libra/improved-leve-rewards.json'), TRUE);

		$rewards = array();
		foreach ($leve_rewards as $class => $levels)
			foreach ($levels as $level => $items)
				foreach ($items as $item_name => $amounts)
					foreach ($amounts as $amount)
					{
						$iid = isset($item_to_id[$item_name]) ? $item_to_id[$item_name] : NULL;
						$rewards[] = array(
							'item_id' => $iid,
							'item_name' => $iid ? NULL : $item_name,
							'classjob_id' => $job_name_to_id[$class],
							'level' => $level,
							'amount' => $amount
						);
					}

		$rewards = Batch::insert($rewards, 'leve_rewards');
	}

}
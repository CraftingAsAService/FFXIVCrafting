<?php

// php artisan migrate:refresh
// php artisan db:seed --class=XIVAPISeeder
// and as needed
// php artisan cache:clear

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class XIVAPISeeder extends Seeder
{

	protected $data = [];

	private $api;

	public function run()
	{
		set_time_limit(0);
		Model::unguard();
		\DB::connection()->disableQueryLog();

		$this->api = new \XIVAPI\XIVAPI();
		$this->api->environment->key(config('services.xivapi.key'));

		// KEEP
		// If you're interested in more translation, it can take quite a bit of processing to get all the useful data out.  All my stuff is open source here, https://github.com/ufx/GarlandTools/blob/master/Garland.Data/Modules/Nodes.cs.  For the most part the s prefix stands for "SaintCoinach" and generally represents what you'll find on xivapi.

		$runList = [
			'location',
			'node',
			'fishing',

			// ...How do I connect a BNPC to a zone and their level?
			// 'mob',

			// 'npc',
			// 'instance',
			// 'quest',
			// 'job_category',
			// 'job',
			// 'venture',
			// 'leve',
			// 'item_category',
			// 'item',
			// 'career',
		];

		$keys = [];
		foreach ($runList as $item)
		{
			$prevKeys = $keys;

			// Run the actual function
			$this->$item();

			$keys = array_keys($this->data);

			foreach (array_diff($keys, $prevKeys) as $k)
				$this->command->info($k . ', ' . count($this->data[$k]) . ' rows');

			// Enable as needed for debugging
			// $this->outputMemory();
		}

		$this->command->line('TODO - Actually Import Data');
		// $this->batchInsert();
	}

	private function location()
	{
		$endpoint = 'placename';

		$this->data['location'] = [];

		$request = $this->listRequest($endpoint, ['columns' => ['ID']]);
		foreach ($request->chunk(100) as $chunk)
		{
			$ids = $chunk->map(function($item) {
				return $item->ID;
			})->join(',');

			$chunk = $this->request($endpoint, ['ids' => $ids, 'columns' => [
				'ID',
				'Name',
				'Maps.0.PlaceNameRegionTargetID',
			]]);

			foreach ($chunk->Results as $data)
			{
				// Skip empty names
				if ($data->Name == '')
					continue;

				$this->setData('location', [
					'id'          => $data->ID,
					'name'        => $data->Name,
					'location_id' => $data->Maps[0]->PlaceNameRegionTargetID ?? null,
				]);
			}
		}
	}

	private function node()
	{
		// You must be looking at gathering items.  What you're looking for there is the GatheringPoint table, which has a PlaceName (i.e., Cedarwood) and a TerritoryType.  The TerritoryType then has the PlaceName you're looking for - Lower La Noscea.
		// Be warned that what I referred to as a 'node' is really a GatheringPointBase.  There are lots of gathering points with the same items because they appear in different places on the map.
		$endpoint = 'gatheringpointbase';

		$this->data['node'] = [];
		$this->data['item_node'] = [];

		// Gather all locations, ID => LocationID for the parental relationship
		$areaFinder = collect($this->data['location'])->pluck('name', 'id');

		// Coordinate data is manually grabbed from a separate location
		$coordinates = $this->getNodeCoordinates();

		// GatheringPointName Conversions, for coordinate matching
		$typeList = [
			// Type => Matching Text
			0 => 'Mineral Deposit', // via Mining
			1 => 'Rocky Outcrop', // via Quarrying
			2 => 'Mature Tree', // via Logging
			3 => 'Lush Vegetation Patch', // via Harvesting
		];

		$request = $this->listRequest($endpoint, ['columns' => ['ID']]);
		foreach ($request->chunk(100) as $chunk)
		{
			$ids = $chunk->map(function($item) {
				return $item->ID;
			})->join(',');

			$chunk = $this->request($endpoint, ['ids' => $ids, 'columns' => [
				'ID',
				'GatheringType.ID',
				'GatheringLevel',
				'GameContentLinks.GatheringPoint.GatheringPointBase.0',
				// Items go from Item0 to Item7; There's rumor of this being Array'd
				'Item0',
				'Item1',
				'Item2',
				'Item3',
				'Item4',
				'Item5',
				'Item6',
				'Item7',
			]]);

			foreach ($chunk->Results as $data)
			{
				if ($data->GameContentLinks->GatheringPoint->GatheringPointBase['0'] == null)
					continue;

				$gp = $this->request('gatheringpoint/' . $data->GameContentLinks->GatheringPoint->GatheringPointBase['0'], ['columns' => [
					'PlaceName.ID',
					'PlaceName.Name',
					'TerritoryType.PlaceName.ID',
					'TerritoryType.PlaceName.Name',
				]]);

				$this->setData('node', [
					'id'          => $data->ID,
					'name'        => $gp->PlaceName->Name,
					'type'        => $data->GatheringType->ID,
					'level'       => $data->GatheringLevel,
					// 'bonus_id'    => isset($data->bonus) ? (is_array($data->bonus) ? $data->bonus[0] : $data->bonus) : null,
					'zone_id'     => $gp->TerritoryType->PlaceName->ID,
					'area_id'     => $gp->PlaceName->ID,
					'coordinates' => isset($areaFinder[$gp->TerritoryType->PlaceName->ID]) && isset($typeList[$data->GatheringType->ID])
						? $coordinates
							->where('location', $areaFinder[$gp->TerritoryType->PlaceName->ID])
							->where('level', $data->GatheringLevel)
							->where('type', $typeList[$data->GatheringType->ID])
							->pluck('coordinates')->join(', ', ' or ')
						: null,
				]);

				// Loop through Item#
				foreach (range(0,7) as $i)
					if ($data->{'Item' . $i})
						$this->setData('item_node', [
							'item_id' => $data->{'Item' . $i},
							'node_id' => $data->ID,
						]);
			}
		}
	}

	private function getNodeCoordinates()
	{
		// Coordinates file is manually built
		$coordinateFile = storage_path('app/osmose/nodeCoordinates.tsv');
		$tsv = array_map(function($l) { return str_getcsv($l, '	'); }, file($coordinateFile));
		array_walk($tsv, function(&$a) use ($tsv) {
			$a = array_combine($tsv[0], $a);
		});
		array_shift($tsv);
		return collect($tsv);
	}

	private function fishing()
	{
		$endpoint = 'fishingspot';

		$this->data['fishing'] = [];
		$this->data['fishing_item'] = [];

		$request = $this->listRequest($endpoint, ['columns' => ['ID']]);
		foreach ($request->chunk(100) as $chunk)
		{
			$ids = $chunk->map(function($item) {
				return $item->ID;
			})->join(',');

			$chunk = $this->request($endpoint, ['ids' => $ids, 'columns' => [
				'ID',
				'PlaceName.Name',
				'FishingSpotCategory',
				'GatheringLevel',
				'Radius',
				'X',
				'Z',
				'TerritoryType.PlaceName.ID',
				'PlaceName.ID',
				// Items go from Item0 to Item9; There's rumor of this being Array'd
				'Item0.ID',
				'Item0.LevelItem',
				'Item1.ID',
				'Item1.LevelItem',
				'Item2.ID',
				'Item2.LevelItem',
				'Item3.ID',
				'Item3.LevelItem',
				'Item4.ID',
				'Item4.LevelItem',
				'Item5.ID',
				'Item5.LevelItem',
				'Item6.ID',
				'Item6.LevelItem',
				'Item7.ID',
				'Item7.LevelItem',
				'Item8.ID',
				'Item8.LevelItem',
				'Item9.ID',
				'Item9.LevelItem',
			]]);

			foreach ($chunk->Results as $data)
			{
				// Skip empty names
				if ($data->PlaceName->Name == '')
					continue;

				$this->setData('fishing', [
					'id'          => $data->ID,
					'name'        => $data->PlaceName->Name,
					'category_id' => $data->FishingSpotCategory,
					'level'       => $data->GatheringLevel,
					'radius'      => $data->Radius,
					'x'           => 1 + ($data->X / 50), // Translate a number like 1203 to something like 25.06
					'y'           => 1 + ($data->Z / 50),
					'zone_id'     => $data->TerritoryType->PlaceName->ID,
					'area_id'     => $data->PlaceName->ID,
				]);

				// Loop through Item#
				foreach (range(0,9) as $i)
					if ($data->{'Item' . $i}->ID)
						$this->setData('fishing_item', [
							'item_id'    => $data->{'Item' . $i}->ID,
							'fishing_id' => $data->ID,
							'level'      => $data->{'Item' . $i}->LevelItem,
						]);
			}
		}

	}

	private function mob()
	{
		$endpoint = 'bnpcname';

		$this->data['mob'] = [];
		$this->data['item_mob'] = [];

		$request = $this->listRequest($endpoint, ['columns' => ['ID']]);
		foreach ($request->chunk(100) as $chunk)
		{
			$ids = $chunk->map(function($item) {
				return $item->ID;
			})->join(',');

			$chunk = $this->request($endpoint, ['ids' => $ids, 'columns' => [
				'ID',

			]]);

			foreach ($chunk->Results as $data)
			{
				// Skip empty names
				if ($data->PlaceName->Name == '')
					continue;

				$this->setData('mob', [
					'id'      => $data->ID,
					'name'    => $data->TODO,
					'quest'   => $data->TODO,
					'level'   => $data->TODO,
					'zone_id' => $data->TODO,
				]);

				// Loop through Item#
				foreach (range(0,9) as $i)
					if ($data->{'Item' . $i})
						$this->setData('item_mob', [
							'item_id' => $data->{'Item' . $i},
							'mob_id' => $data->ID,
						]);
			}
		}

	}

	private function npc()
	{
		$endpoint = 'enpcresident';

		$this->data['npc'] = [];
		$this->data['npc_shop'] = [];
		$this->data['npc_quest'] = [];
		$this->data['shop'] = [];
		$this->data['item_shop'] = [];

		$request = $this->listRequest($endpoint, ['columns' => ['ID']]);
		foreach ($request->chunk(100) as $chunk)
		{
			$ids = $chunk->map(function($item) {
				return $item->ID;
			})->join(',');

			$chunk = $this->request($endpoint, ['ids' => $ids, 'columns' => [
				'ID',
				'Name',

			]]);

			foreach ($chunk->Results as $data)
			{
				$this->setData('npc', [
					'id'      => $data->ID,
					'name'    => $data->Name,
					// 'zone_id' => $data->TODO,
					// 'approx'  => $data->TODO,
					// 'x'       => $data->TODO,
					// 'y'       => $data->TODO,
				]);

			}

		}

	}


	private function npcx()
	{
		$this->data['npc'] = [];
		$this->data['npc_shop'] = [];
		$this->data['npc_quest'] = [];
		$this->data['shop'] = [];
		$this->data['item_shop'] = [];

		$shopId = 0;

		$used_shop = [];

		// Loop through npcs
		foreach (array_diff(scandir(base_path() . '/../garlandtools/db/data/en/npc'), ['.', '..']) as $file)
		{
			// Get /db/data/quest/#.json
			$json_file = base_path() . '/../garlandtools/db/data/en/npc/' . $file;
			if ( ! is_file($json_file))
				continue;

			$n = $this->getCleanedJson($json_file);
			$n = $n->npc;

			$this->setData('npc', [
				'id' => $n->id,
				'name' => isset($n->en) ? $n->en->name : $n->name,
				'zone_id' => isset($n->zoneid) ? $n->zoneid : null,
				'approx' => isset($n->approx) ? $n->approx : null,
				'x' => isset($n->coords) ? $n->coords[0] : null,
				'y' => isset($n->coords) ? $n->coords[1] : null,
			]);

			if (isset($n->shops))
				foreach ($n->shops as $shop)
				{
					$shop->id = $shopId++;

					$this->setData('npc_shop', [
						'shop_id' => $shop->id,
						'npc_id' => $n->id,
					]);

					$this->setData('shop', [
						'id' => $shop->id,
						'name' => $shop->name,
					]);

					if (isset($shop->entries))
						foreach ($shop->entries as $item_id)
						{
							if (gettype($item_id) == 'object')
							{
								foreach ($item_id->item as $iii)
								{
									$this->setData('item_shop', [
										'item_id' => $iii->id,
										'shop_id' => $shop->id,
									]);
								}
							}
							else
							{
								$this->setData('item_shop', [
									'item_id' => $item_id,
									'shop_id' => $shop->id,
								]);
							}
						}
				}

			if (isset($n->quests))
				foreach ($n->quests as $quest_id)
				{
					$this->setData('npc_quest', [
						'quest_id' => $quest_id,
						'npc_id' => $n->id,
					]);
				}
		}

		echo __FUNCTION__ . ', ' . count($this->data['npc']) . ' rows' . PHP_EOL;
		echo 'npc_shop, ' . count($this->data['npc_shop']) . ' rows' . PHP_EOL;
		echo 'npc_quest, ' . count($this->data['npc_quest']) . ' rows' . PHP_EOL;
		echo 'shop, ' . count($this->data['shop']) . ' rows' . PHP_EOL;
		echo 'item_shop, ' . count($this->data['item_shop']) . ' rows' . PHP_EOL;
	}

	// private function npc_base($npc_base)
	// {
	// 	$this->data['npc_base'] = [];
	// 	$this->data['npc_npc_base'] = [];

	// 	// Loop through bases
	// 	foreach ($npc_base as $name => $list)
	// 	{
	// 		$row = [
	// 			'name' => $name, // id is actually the name
	// 			// 'title' => isset($nb->title) ? $nb->title : null,
	// 		];

	// 		$given_id = $this->setData('npc_base', $row);

	// 		if (isset($list->x))
	// 			foreach ($list->x as $npc_id)
	// 			{
	// 				$row = [
	// 					'npc_id' => $npc_id,
	// 					'npc_base_id' => $given_id,
	// 				];

	// 				$this->setData('npc_npc_base', $row);
	// 			}
	// 	}

	// 	echo __FUNCTION__ . ', ' . count($this->data['npc_base']) . ' rows' . PHP_EOL;
	// 	echo 'npc_npc_base, ' . count($this->data['npc_npc_base']) . ' rows' . PHP_EOL;
		// $this->outputMemory();
	// }

	// private function shop_name($shop_name)
	// {
	// 	$this->data['shop_name'] = [];

	// 	// Loop through nodes
	// 	foreach ($shop_name as $id => $name)
	// 	{
	// 		$row = compact('id', 'name');

	// 		$this->setData('shop_name', $row);
	// 	}

	// 	echo __FUNCTION__ . ', ' . count($this->data['shop_name']) . ' rows' . PHP_EOL;
		// $this->outputMemory();
	// }

	// private function shop($shop)
	// {
	// 	$this->data['shop'] = [];
	// 	$this->data['item_shop'] = [];

	// 	// Loop through nodes
	// 	foreach ($shop as $s)
	// 	{
	// 		// Don't count trade shops
	// 		if (isset($s->trade) && $s->trade == 1)
	// 			continue;

	// 		$row = [
	// 			'id' => $s->id,
	// 			'name_id' => $s->nameId,
	// 		];

	// 		$this->setData('shop', $row);

	// 		foreach ($s->entries as $item_id)
	// 		{
	// 			if (gettype($item_id) == 'object')
	// 			{
	// 				foreach ($item_id->item as $iii)
	// 				{
	// 					$row = [
	// 						'item_id' => $iii->id,
	// 						'shop_id' => $s->id,
	// 					];

	// 					$this->setData('item_shop', $row);
	// 				}
	// 			}
	// 			else
	// 			{
	// 				$row = [
	// 					'item_id' => $item_id,
	// 					'shop_id' => $s->id,
	// 				];

	// 				$this->setData('item_shop', $row);
	// 			}
	// 		}
	// 	}

	// 	echo __FUNCTION__ . ', ' . count($this->data['shop']) . ' rows' . PHP_EOL;
	// 	echo 'item_shop, ' . count($this->data['item_shop']) . ' rows' . PHP_EOL;
		// $this->outputMemory();
	// }

	private function instance()
	{
		$this->data['instance'] = [];
		$this->data['instance_item'] = [];
		$this->data['instance_mob'] = [];

		// Loop through instance
		foreach (array_diff(scandir(base_path() . '/../garlandtools/db/data/en/instance'), ['.', '..']) as $file)
		{
			// Get /db/data/instance/#.json
			$json_file = base_path() . '/../garlandtools/db/data/en/instance/' . $file;
			$i = $this->getCleanedJson($json_file);
			$i = $i->instance;

			// if ( ! isset($i->fullIcon))
			// 	dd($i);

			$row = [
				'id' => $i->id,
				'name' => isset($i->en) ? $i->en->name : $i->name,
				'type' => $i->type ?? null,
				'zone_id' => isset($i->zoneid) ? $i->zoneid : null,
				'icon' => $i->fullIcon ?? '',
			];

			$this->setData('instance', $row);

			if (isset($i->fights))
				foreach ($i->fights as $f)
				{
					if (isset($f->coffer))
						foreach ($f->coffer->items as $item_id)
						{
							$row = [
								'item_id' => $item_id,
								'instance_id' => $i->id,
							];

							$this->setData('instance_item', $row);
						}

					foreach ($f->mobs as $mob_id)
					{
						$row = [
							'mob_id' => $mob_id,
							'instance_id' => $i->id,
						];

						$this->setData('instance_mob', $row);
					}
				}

			if (isset($i->rewards))
				foreach ($i->rewards as $item_id)
				{
					$row = [
						'item_id' => $item_id,
						'instance_id' => $i->id,
					];

					$this->setData('instance_item', $row);
				}

			if (isset($i->coffers))
				foreach ($i->coffers as $c)
				{
					foreach ($c->items as $item_id)
					$row = [
						'item_id' => $item_id,
						'instance_id' => $i->id,
					];

					$this->setData('instance_item', $row);
				}
		}

		echo __FUNCTION__ . ', ' . count($this->data['instance']) . ' rows' . PHP_EOL;
		echo 'instance_item, ' . count($this->data['instance_item']) . ' rows' . PHP_EOL;
		echo 'instance_mob, ' . count($this->data['instance_mob']) . ' rows' . PHP_EOL;
	}

	private function quest()
	{
		$this->data['quest'] = [];
		$this->data['quest_reward'] = [];
		$this->data['quest_required'] = [];

		// Loop through quest
		foreach (array_diff(scandir(base_path() . '/../garlandtools/db/data/en/quest'), ['.', '..']) as $file)
		{
			// Get /db/data/quest/#.json
			$json_file = base_path() . '/../garlandtools/db/data/en/quest/' . $file;
			$q = $this->getCleanedJson($json_file);
			$q = $q->quest;

			$row = [
				'id' => $q->id,
				'name' => $q->name ?? $q->en->name,
				'job_category_id' => isset($q->reqs) && isset($q->reqs->jobs) ? $q->reqs->jobs[0]->id : null,
				'level' => isset($q->reqs) && isset($q->reqs->jobs) ? $q->reqs->jobs[0]->lvl : 1,
				'sort' => $q->sort,
				'zone_id' => isset($q->zoneid) ? $q->zoneid : null,
				'icon' => isset($q->icon) ? $q->icon : null,
				'issuer_id' => isset($q->issuer_id) ? $q->issuer_id : null,
				'target_id' => isset($q->target_id) ? $q->target_id : null,
				'genre' => $q->genre,
			];

			$this->setData('quest', $row);

			if (isset($q->usedItems))
				foreach ($q->usedItems as $item_id)
				{
					$row = [
						'item_id' => $item_id,
						'quest_id' => $q->id,
					];

					$this->setData('quest_required', $row);
				}

			if (isset($q->reward) && isset($q->reward->items))
				foreach ($q->reward->items as $i)
				{
					$row = [
						'item_id' => $i->id,
						'quest_id' => $q->id,
						'amount' => isset($i->num) ? $i->num : null,
					];

					$this->setData('quest_reward', $row);
				}
		}

		echo __FUNCTION__ . ', ' . count($this->data['quest']) . ' rows' . PHP_EOL;
		echo 'quest_reward, ' . count($this->data['quest_reward']) . ' rows' . PHP_EOL;
		echo 'quest_required, ' . count($this->data['quest_required']) . ' rows' . PHP_EOL;
	}

	// private function achievement($achievement)
	// {
	// 	$this->data['achievement'] = [];

	// 	// Loop through achievements
	// 	foreach ($achievement as $a)
	// 	{
	// 		// Get /db/data/quest/#.json
	// 		$json_file = base_path() . '/../garlandtools/db/data/en/achievement/' . $a->i . '.json';
	// 		$a = $this->getCleanedJson($json_file, TRUE);
	// 		$a = $a->achievement;

	// 		$row = [
	// 			'id' => $a->id,
	// 			'name' => isset($a->en) ? $a->en->name : $a->name,
	// 			'item_id' => isset($a->item) ? $a->item : null,
	// 			'icon' => $a->icon,
	// 		];

	// 		$this->setData('achievement', $row);
	// 	}

	// 	echo __FUNCTION__ . ', ' . count($this->data['achievement']) . ' rows' . PHP_EOL;
		// $this->outputMemory();
	// }

	private function fate($fate)
	{
		$this->data['fate'] = [];

		// Loop through fates
		foreach ($fate as $f)
		{
			// Get /db/data/quest/#.json
			$json_file = base_path() . '/../garlandtools/db/data/en/fate/' . $f->i . '.json';
			$f = $this->getCleanedJson($json_file, TRUE);
			$f = $f->fate;

			$row = [
				'id' => $f->id,
				'name' => isset($f->en) ? $f->en->name : $f->name,
				'type' => $f->type,
				'level' => $f->lvl,
				'max_level' => $f->maxlvl,
				'zone_id' => isset($f->zoneid) ? $f->zoneid : null,
				'x' => isset($f->coords) ? $f->coords[0] : null,
				'y' => isset($f->coords) ? $f->coords[1] : null,
			];

			$this->setData('fate', $row);
		}

		echo __FUNCTION__ . ', ' . count($this->data['fate']) . ' rows' . PHP_EOL;
	}

	private function job_category($job_category)
	{
		$this->data['job_category'] = [];
		$this->data['job_job_category'] = [];

		// Loop through nodes
		foreach ($job_category as $jc)
		{
			$row = [
				'id' => $jc->id,
				'name' => $jc->name,
			];

			$this->setData('job_category', $row);

			// Loop through job_category items
			foreach ($jc->jobs as $j)
			{
				$row = [
					'job_id' => $j,
					'job_category_id' => $jc->id,
				];

				$this->setData('job_job_category', $row);
			}
		}

		echo __FUNCTION__ . ', ' . count($this->data['job_category']) . ' rows' . PHP_EOL;
		echo 'job_job_category, ' . count($this->data['job_job_category']) . ' rows' . PHP_EOL;
	}

	private function job()
	{
		$this->data['job'] = [];

		$job = [
			['1','Gladiator','GLA'],
			['2','Pugilist','PGL'],
			['3','Marauder','MRD'],
			['4','Lancer','LNC'],
			['5','Archer','ARC'],
			['6','Conjurer','CNJ'],
			['7','Thaumaturge','THM'],
			['8','Carpenter','CRP'],
			['9','Blacksmith','BSM'],
			['10','Armorer','ARM'],
			['11','Goldsmith','GSM'],
			['12','Leatherworker','LTW'],
			['13','Weaver','WVR'],
			['14','Alchemist','ALC'],
			['15','Culinarian','CUL'],
			['16','Miner','MIN'],
			['17','Botanist','BTN'],
			['18','Fisher','FSH'],
			['19','Paladin','PLD'],
			['20','Monk','MNK'],
			['21','Warrior','WAR'],
			['22','Dragoon','DRG'],
			['23','Bard','BRD'],
			['24','White Mage','WHM'],
			['25','Black Mage','BLM'],
			['26','Arcanist','ACN'],
			['27','Summoner','SMN'],
			['28','Scholar','SCH'],
			['29','Rogue','ROG'],
			['30','Ninja','NIN'],
			['31', 'Machinist', 'MCH'],
			['32', 'Dark Knight', 'DRK'],
			['33', 'Astrologian', 'AST'],
			['34', 'Samurai', 'SAM'],
			['35', 'Red Mage', 'RDM'],
			['36', 'Blue Mage', 'BLU'],
		];

		// Loop through nodes
		foreach ($job as $j)
		{
			$row = [
				'id' => $j[0],
				'name' => $j[1],
				'abbr' => $j[2],
			];

			$this->setData('job', $row);
		}

		echo __FUNCTION__ . ', ' . count($this->data['job']) . ' rows' . PHP_EOL;
	}

	private function venture($venture)
	{
		$this->data['venture'] = [];

		// Loop through nodes
		foreach ($venture as $v)
		{
			$row = [
				'id' => $v->id,
				'amounts' => isset($v->amounts) ? implode(',', $v->amounts) : null,
				'job_category_id' => $v->jobs,
				'level' => $v->lvl,
				'cost' => $v->cost,
				'minutes' => $v->minutes,
			];

			$this->setData('venture', $row);
		}

		echo __FUNCTION__ . ', ' . count($this->data['venture']) . ' rows' . PHP_EOL;
	}

	private function leve()
	{
		$this->data['leve'] = [];
		$this->data['leve_reward'] = [];
		$this->data['leve_required'] = [];

		// Get some bonus data from the wiki
		// For an update, run /osmose
		$gamerescapewiki_data = json_decode(file_get_contents(storage_path() . '/app/osmose/cache/leves/leves.json'));

		$gamerescapewiki_leves = [];
		foreach ($gamerescapewiki_data as $gewd)
		{
			$search_name = trim(preg_replace("/\s|\-|\(.*\)| /", '', strtolower($gewd->name)));
			$gamerescapewiki_leves[$search_name] = $gewd;
		}

		// Loop through leves
		foreach (array_diff(scandir(base_path() . '/../garlandtools/db/data/en/leve'), ['.', '..']) as $file)
		{
			// Get /db/data/leve/#.json
			$json_file = base_path() . '/../garlandtools/db/data/en/leve/' . $file;
			$l = $this->getCleanedJson($json_file);

			$rewards = isset($l->rewards) && isset($l->rewards->entries) ? $l->rewards->entries : [];
			$l = $l->leve;

			$search_name = trim(preg_replace("/\s|\-|\(.*\)| /", '', strtolower($l->name)));

			$row = [
				'id' => $l->id,
				'name' => $l->name,
				'type' => isset($gamerescapewiki_leves[$search_name]) ? $gamerescapewiki_leves[$search_name]->issuing_npc_information : null,
				'level' => $l->lvl,
				'job_category_id' => $l->jobCategory,
				'area_id' => $l->areaid,
				'repeats' => isset($l->repeats) ? $l->repeats : null,
				'xp' => isset($l->xp) ? $l->xp : null,
				'gil' => isset($l->gil) ? $l->gil : null,
				'plate' => $l->plate,
				'frame' => $l->frame,
				'area_icon' => $l->areaicon,
			];

			$this->setData('leve', $row);

			foreach ($rewards as $r)
			{
				$row = [
					'item_id' => $r->item,
					'leve_id' => $l->id,
					'rate' => $r->rate * 100,
					'amount' => isset($r->amount) ? $r->amount : null,
				];

				$this->setData('leve_reward', $row);
			}

			if (isset($l->requires))
				foreach ($l->requires as $r)
				{
					$row = [
						'item_id' => $r->item,
						'leve_id' => $l->id,
						'amount' => isset($r->amount) ? $r->amount : 1,
					];

					$this->setData('leve_required', $row);
				}
		}

		echo __FUNCTION__ . ', ' . count($this->data['leve']) . ' rows' . PHP_EOL;
		echo 'leve_reward, ' . count($this->data['leve_reward']) . ' rows' . PHP_EOL;
		echo 'leve_required, ' . count($this->data['leve_required']) . ' rows' . PHP_EOL;
	}

	private function item_category($item_category)
	{
		$this->data['item_category'] = [];

		// Loop through nodes
		foreach ($item_category as $ic)
		{
			$row = [
				'id' => $ic->id,
				'name' => $ic->name,
				'attribute' => isset($ic->attr) ? $ic->attr : null,
			];

			$this->setData('item_category', $row);
		}

		echo __FUNCTION__ . ', ' . count($this->data['item_category']) . ' rows' . PHP_EOL;
	}

	private function item()
	{
		$this->data['item'] = [];
		$this->data['item_venture'] = [];
		$this->data['item_attribute'] = [];
		$this->data['recipe'] = [];
		$this->data['recipe_reagents'] = [];

		// Eorzea Name Translations
		$translations = (array) json_decode(file_get_contents(storage_path() . '/app/osmose/i18n_names.json'));

		// Loop through items
		foreach (array_diff(scandir(base_path() . '/../garlandtools/db/data/en/item'), ['.', '..']) as $file)
		{
			// Get /db/data/item/#.json
			$json_file = base_path() . '/../garlandtools/db/data/en/item/' . $file;
			$i = $this->getCleanedJson($json_file);
			$i = $i->item;

			$frI = $this->getCleanedJson(base_path() . '/../garlandtools/db/data/fr/item/' . $file)->item;
			$jaI = $this->getCleanedJson(base_path() . '/../garlandtools/db/data/ja/item/' . $file)->item;
			$deI = $this->getCleanedJson(base_path() . '/../garlandtools/db/data/de/item/' . $file)->item;

			// TMP handler
			// $whitelist = [
			// 	63,59,34,35,36,37,38,39,2,84,1,3,5,4,8,58,
			// ];
			// if ( ! in_array($i->category, $whitelist))
			// 	dd($i, 'Category OK?  Whitelist it.', $i->category);
			// if ($i->category == 58)
			// 	dd($i);
			// if ($i->id == 1609)
			// 	dd($i);

			$row = [
				'id' => $i->id,
				'eorzea_id' => null,//isset($translations[$i->en->name]) ? $translations[$i->en->name]->eid : null,
				'name' => $this->cleanName($i->name), //$i->en->name,
				'de_name' => $this->cleanName($deI->name ?? null), //isset($i->de->name) ? $i->de->name : $i->id, //isset($translations[$i->name]) ? $translations[$i->name]->de : $i->name,
				'fr_name' => $this->cleanName($frI->name ?? null), //isset($i->fr->name) ? $i->fr->name : $i->id, //isset($translations[$i->name]) ? $translations[$i->name]->fr : $i->name,
				'jp_name' => $this->cleanName($jaI->name ?? null), //isset($i->ja->name) ? $i->ja->name : $i->id, //isset($translations[$i->name]) ? $translations[$i->name]->jp : $i->name,
				'help' => isset($i->help) ? $i->help : null,
				'price' => isset($i->price) ? $i->price : null,
				'sell_price' => isset($i->sell_price) ? $i->sell_price : null,
				'ilvl' => $i->ilvl,
				'elvl' => isset($i->elvl) ? $i->elvl : null,
				'item_category_id' => $i->category,
				'unique' => isset($i->unique) ? $i->unique : null,
				'tradeable' => isset($i->tradeable) ? $i->tradeable : null,
				'desynthable' => isset($i->desynthable) ? $i->desynthable : null,
				'projectable' => isset($i->projectable) ? $i->projectable : null,
				'crestworthy' => isset($i->crestworty) ? $i->crestworty : null,
				'delivery' => isset($i->delivery) ? $i->delivery : null,
				'equip' => isset($i->equip) ? $i->equip : null,
				'repair' => isset($i->repair) ? $i->repair : null,
				'slot' => isset($i->slot) ? $i->slot : null,
				'rarity' => isset($i->rarity) ? $i->rarity : null,
				'icon' => $i->icon,
				'sockets' => isset($i->sockets) ? $i->sockets : null,
				'job_category_id' => isset($i->jobs) ? $i->jobs : null,
			];

			$this->setData('item', $row);

			if (isset($i->ventures))
				foreach ($i->ventures as $venture_id)
				{
					$row = [
						'venture_id' => $venture_id,
						'item_id' => $i->id,
					];

					$this->setData('item_venture', $row);
				}

			if (isset($i->attr))
				foreach ((array) $i->attr as $attr => $amount)
				{
					if ($attr == 'action')
					{
						foreach ($amount as $attr => $data)
						{
							$row = [
								'item_id' => $i->id,
								'attribute' => $attr,
								'quality' => 'nq',
								'amount' => isset($data->rate) ? $data->rate : null,
								'limit' => isset($data->limit) ? $data->limit : null,
							];

							$this->setData('item_attribute', $row);
						}

						continue;
					}

					$row = [
						'item_id' => $i->id,
						'attribute' => $attr,
						'quality' => 'nq',
						'amount' => $amount,
						'limit' => null,
					];

					$this->setData('item_attribute', $row);
				}

			if (isset($i->attr_hq))
				foreach ((array) $i->attr_hq as $attr => $amount)
				{
					if ($attr == 'action')
					{
						foreach ($amount as $attr => $data)
						{
							$row = [
								'item_id' => $i->id,
								'attribute' => $attr,
								'quality' => 'hq',
								'amount' => isset($data->rate) ? $data->rate : null,
								'limit' => isset($data->limit) ? $data->limit : null,
							];

							$this->setData('item_attribute', $row);
						}

						continue;
					}

					$row = [
						'item_id' => $i->id,
						'attribute' => $attr,
						'quality' => 'hq',
						'amount' => $amount,
						'limit' => null,
					];

					$this->setData('item_attribute', $row);
				}

			if (isset($i->attr_max))
				foreach ((array) $i->attr_max as $attr => $amount)
				{
					$row = [
						'item_id' => $i->id,
						'attribute' => $attr,
						'quality' => 'max',
						'amount' => $amount,
						'limit' => null,
					];

					$this->setData('item_attribute', $row);
				}

			if (isset($i->materia))
			{
				$row = [
					'item_id' => $i->id,
					'attribute' => $i->materia->attr,
					'quality' => 'nq',
					'amount' => $i->materia->value,
					'limit' => null,
				];

				$this->setData('item_attribute', $row);
			}

			if (isset($i->craft))
				foreach ($i->craft as $r)
				{
					// We don't know the Recipe ID, so let the system give it one later.

					$row = [
						'item_id' => $i->id,
						'job_id' => $r->job,
						'recipe_level' => $r->rlvl,
						'level' => $r->lvl,
						'durability' => isset($r->durability) ? $r->durability : null,
						'quality' => isset($r->quality) ? $r->quality : null,
						'progress' => isset($r->progress) ? $r->progress : null,
						'yield' => isset($r->yield) ? $r->yield : 1,
						'quick_synth' => isset($r->quickSynth) ? $r->quickSynth : null,
						'hq' => isset($r->hq) ? $r->hq : null,
						'fc' => isset($r->fc) ? $r->fc : null,
					];

					$recipe_id = $this->setData('recipe', $row);

					foreach ($r->ingredients as $in)
					{
						$row = [
							'item_id' => $in->id,
							'recipe_id' => $recipe_id,
							'amount' => $in->amount,
						];

						$this->setData('recipe_reagents', $row);
					}
				}
		}

		echo __FUNCTION__ . ', ' . count($this->data['item']) . ' rows' . PHP_EOL;
		echo 'item_venture, ' . count($this->data['item_venture']) . ' rows' . PHP_EOL;
		echo 'item_attribute, ' . count($this->data['item_attribute']) . ' rows' . PHP_EOL;
		echo 'recipe, ' . count($this->data['recipe']) . ' rows' . PHP_EOL;
		echo 'recipe_reagents, ' . count($this->data['recipe_reagents']) . ' rows' . PHP_EOL;
	}

	public $recipes = [],
			$item_to_recipe = [],
			$career_recipes = [],
			$career_reagents = [];
	private function career()
	{
		$this->data['career'] = [];
		$this->data['career_job'] = [];

		foreach ($this->data['recipe'] as $recipe_id => $recipe)
		{
			// Prepare the recipe
			$recipe['id'] = $recipe_id;
			$recipe['reagents'] = [];
			// Copy the recipe
			$this->recipes[$recipe_id] = $recipe;
			// Save the item produced in relation to the recipe
			$this->item_to_recipe[$recipe['item_id']][] = $recipe_id;
		}

		foreach ($this->data['recipe_reagents'] as $reagent)
			$this->recipes[$reagent['recipe_id']]['reagents'][] = ['item_id' => $reagent['item_id'], 'amount' => $reagent['amount']];

		foreach ($this->recipes as $recipe)
			$this->_recursive_career($recipe, $recipe['recipe_level'], $recipe['job_id'], $recipe['yield']);

		foreach (['recipe' => 'career_recipes', 'item' => 'career_reagents'] as $type => $key)
		{
			$data =& $this->$key;

			foreach ($data as $identifier => $i)
				foreach ($i as $level => $j)
				{
					$row = [
						'type' => $type,
						'identifier' => $identifier,
						'level' => $level,
					];

					$career_id = $this->setData('career', $row);

					foreach ($j as $job_id => $amount)
					{
						$row = [
							'career_id' => $career_id,
							'job_id' => $job_id,
							'amount' => $amount,
						];

						$this->setData('career_job', $row);
					}
				}
			unset($data);
		}

		echo __FUNCTION__ . ', ' . count($this->data['career']) . ' rows' . PHP_EOL;
		echo 'career_job, ' . count($this->data['career_job']) . ' rows' . PHP_EOL;
	}

	private function _recursive_career($recipe = [], $parent_level = 0, $parent_class = '', $make_this_many = 0, $depth = 0)
	{
		##echo str_pad('', $level * 2, "\t") . 'MAKING ' . ($make_this_many / $recipe->yields) . ' (' . $make_this_many . '/' . $recipe->yields . ') ' . $recipe->name . "\n";
		// For recipe W, At level X, to fulfil a Y class objective, make this many.
		// If I only need one item, but the recipe makes more than that, do the division.
		@$this->career_recipes[$recipe['id']][$parent_level][$parent_class] += $make_this_many / $recipe['yield'];

		// Loop through the reagents
		// Either add treat it as a recipe or add them to career reagents
		foreach ($recipe['reagents'] as $reagent)
			// Recipe
			if (isset($this->item_to_recipe[$reagent['item_id']]))
			{
				// Loop through the item's recipe. If the recipe is made in multiple (like bronze ingot for BSM/ARM), divide by two, because it will be reported on both. (or three, four, etc);
				foreach($this->item_to_recipe[$reagent['item_id']] as $reagent_recipe_id)
				{
					##echo str_pad('', ($level + 1) * 2, "\t") . 'LOOKING ' . ($reagent->amount * $make_this_many / $recipe->yields / count($this->item_to_recipe[$reagent->item_id])) . ' (' . $reagent->amount . '*' . $make_this_many . '/' . $recipe->yields . '/' . count($this->item_to_recipe[$reagent->item_id]) . ') ' . $this->recipes[$reagent_recipe_id]->name . "\n";
					$this->_recursive_career($this->recipes[$reagent_recipe_id], $parent_level, $parent_class, $reagent['amount'] * $make_this_many / $recipe['yield'] / count($this->item_to_recipe[$reagent['item_id']]), $depth + 1);
				}
			}
			// Reagent
			else
			{
				##echo str_pad('', ($level + 1) * 2, "\t") . 'ADDING ' . ($reagent->amount * $make_this_many / $recipe->yields) . ' (' . $reagent->amount . '*' . $make_this_many . '/' . $recipe->yields . ') ' . $reagent->item_id . "\n";
				// For item W, at level X, to fulfil a Y class objective, gather this many
				@$this->career_reagents[$reagent['item_id']][$parent_level][$parent_class] += $reagent['amount'] * $make_this_many / $recipe['yield'];
			}
	}

	/**
	 * Helper Functions
	 */

	private function batchInsert()
	{
		$batch_limit = 300;

		foreach ($this->data as $table => $rows)
		{
			echo '--------------------------------------------------' . PHP_EOL;
			// $count = 0;
			foreach (array_chunk($rows, $batch_limit) as $batch_id => $data)
			{
				echo 'Inserting ' . count($data) . ' rows for ' . $table . ' (' . ($batch_id + 1) . ')' . PHP_EOL;

				$values = $pdo = [];
				foreach ($data as $row)
				{
					$values[] = '(' . str_pad('', count($row) * 2 - 1, '?,') . ')';

					// Cleanup value, if FALSE set to NULL
					foreach ($row as $value)
						$pdo[] = $value === FALSE ? NULL : $value;
				}

				$keys = ' (`' . implode('`,`', array_keys($data[0])) . '`)';

				\DB::insert('INSERT IGNORE INTO ' . $table . $keys . ' VALUES ' . implode(',', $values), $pdo);
			}
		}
	}

	private function outputMemory()
	{
		$this->command->comment('@' . $this->humanReadable(memory_get_usage()));
	}

	private function getData($table, $id)
	{
		return isset($this->data[$table][$id]) ? $this->data[$table][$id] : false;
	}

	private function setData($table, $row, $id = null)
	{
		// If id is null, use the length of the existing data, or check in the $row for it
		$id = $id ?: (isset($row['id']) ? $row['id'] : count($this->data[$table]) + 1);

		$this->data[$table][$id] = $row;

		return $id;
	}

	private function humanReadable($size)
	{
		$filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
		return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) .$filesizename[$i] : '0 Bytes';
	}

	private function getCleanedJson($path, $debug = false)
	{
		// $content = stripslashes(file_get_contents($path));
		$content = file_get_contents($path);

		// if ($debug)
		// 	echo mb_strlen($content) . PHP_EOL . strlen($content) . PHP_EOL;

		// http://stackoverflow.com/questions/17219916/json-decode-returns-json-error-syntax-but-online-formatter-says-the-json-is-ok
		for ($i = 0; $i <= 31; ++$i) {
			$content = str_replace(chr($i), "", $content);
		}
		$content = str_replace(chr(127), "", $content);

		// This is the most common part
		$content = $this->binaryFix($content);

		// Trim null content
		// $content = trim($content, "\x0");

		// if ($debug)
		// 	dd(mb_strlen($content), strlen($content),json_decode($content),
		// 		mb_check_encoding($content, 'utf-8'), json_last_error_msg(),
		// 		$content);

		return json_decode($content);
	}

	private function binaryFix($string)
	{
		// Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
		// here we detect it and we remove it, basically it's the first 3 characters
		if (0 === strpos(bin2hex($string), 'efbbbf')) {
		   $string = substr($string, 3);
		}

		// Remove UTF-8 BOM if present, json_decode() does not like it.
		// if(substr($string, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) {
		//     $string = substr($string, 3);
		// }

		return $string;
	}

	private function cleanName($string)
	{
		return preg_replace('/\<SoftHyphen\/\>/', '', $string);
	}

	private function listRequest($content, $queries = [])
	{
		$queries['limit'] = 3000; // Maximum allowed per https://xivapi.com/docs/Game-Data#lists
		$queries['page'] = 1;

		$results = [];

		while (true)
		{
			// $response now contains ->Pagination and ->Results
			$response = $this->request($content, $queries);

			$results = array_merge($results, $response->Results);

			if ($response->Pagination->PageTotal == $response->Pagination->Page)
				break;

			$queries['page'] = $response->Pagination->PageNext;
		}

		return collect($results);
	}

	private function request($content, $queries = [])
	{
		$thisObject = $this;
		return Cache::rememberForever($content . serialize($queries), function() use ($content, $queries, $thisObject)
		{
			$thisObject->command->info(
				'Querying: ' . $content .
				(isset($queries['ids']) ? ' ' . preg_replace('/,.+,/', '-', $queries['ids']) : '')
			);
			return $thisObject->api->queries($queries)->content->{$content}()->list();
		});
	}

}
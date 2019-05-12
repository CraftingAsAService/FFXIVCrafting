<?php

/**
 * XIVAPI
 * 	Get data from XIVAPI
 */

namespace App\Models\Aspir;

use Cache;

class XIVAPI
{

	public $aspir;

	private $api;

	public $limit = null;

	public function __construct(&$aspir)
	{
		$this->api = new \XIVAPI\XIVAPI();
		$this->api->environment->key(config('services.xivapi.key'));

		$this->aspir =& $aspir;
	}

	public function achievements()
	{
		$this->loopEndpoint('achievement', [
			'ID',
			'Name',
			'ItemTargetID',
			'IconID',
		], function($data) {
			// We only care about achievements that provide an item
			if ( ! $data->ItemTargetID)
				return;

			$this->aspir->setData('achievement', [
				'id'      => $data->ID,
				'name'    => $data->Name,
				'item_id' => $data->ItemTargetID,
				'icon'    => $data->IconID,
			], $data->ID);
		});
	}

	public function locations()
	{
		$this->loopEndpoint('placename', [
			'ID',
			'Name',
			'Maps.0.PlaceNameRegionTargetID',
		], function($data) {
			// Skip empty names
			if ($data->Name == '')
				return;

			$this->aspir->setData('location', [
				'id'          => $data->ID,
				'name'        => $data->Name,
				'location_id' => $data->Maps[0]->PlaceNameRegionTargetID ?? null,
			], $data->ID);
		});
	}

	public function nodes()
	{
		// You must be looking at gathering items.  What you're looking for there is the GatheringPoint table, which has a PlaceName (i.e., Cedarwood) and a TerritoryType.  The TerritoryType then has the PlaceName you're looking for - Lower La Noscea.
		// Be warned that what I referred to as a 'node' is really a GatheringPointBase.  There are lots of gathering points with the same items because they appear in different places on the map.
		$this->loopEndpoint('gatheringpointbase', [
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
		], function($data) {
			if ($data->GameContentLinks->GatheringPoint->GatheringPointBase['0'] == null)
				return;

			// Loop through Item#
			$hasItems = false;
			foreach (range(0,7) as $i)
				if ($data->{'Item' . $i})
				{
					$hasItems = true;
					$this->aspir->setData('item_node', [
						'item_id' => $data->{'Item' . $i},
						'node_id' => $data->ID,
					]);
				}

			// Don't include the node if there aren't any items attached
			if ( ! $hasItems)
				return;

			$gp = $this->request('gatheringpoint/' . $data->GameContentLinks->GatheringPoint->GatheringPointBase['0'], ['columns' => [
				'PlaceName.ID',
				'PlaceName.Name',
				'TerritoryType.PlaceName.ID',
				'TerritoryType.PlaceName.Name',
			]]);

			$this->aspir->setData('node', [
				'id'          => $data->ID,
				'name'        => $gp->PlaceName->Name,
				'type'        => $data->GatheringType->ID,
				'level'       => $data->GatheringLevel,
				'zone_id'     => $gp->TerritoryType->PlaceName->ID,
				'area_id'     => $gp->PlaceName->ID,
				'coordinates' => null, // Filled in later
			], $data->ID);
		});
	}

	public function fishingSpots()
	{
		$this->loopEndpoint('fishingspot', [
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
		], function($data) {
			// Skip empty names
			if ($data->PlaceName->Name == '')
				return;

			// Loop through Item#
			$hasItems = false;
			foreach (range(0,9) as $i)
				if ($data->{'Item' . $i}->ID)
				{
					$hasItems = true;
					$this->aspir->setData('fishing_item', [
						'item_id'    => $data->{'Item' . $i}->ID,
						'fishing_id' => $data->ID,
						'level'      => $data->{'Item' . $i}->LevelItem,
					]);
				}

			// Don't include the fishing node if there aren't any items attached
			if ( ! $hasItems)
				return;

			$this->aspir->setData('fishing', [
				'id'          => $data->ID,
				'name'        => $data->PlaceName->Name,
				'category_id' => $data->FishingSpotCategory,
				'level'       => $data->GatheringLevel,
				'radius'      => $data->Radius,
				'x'           => 1 + ($data->X / 50), // Translate a number like 1203 to something like 25.06
				'y'           => 1 + ($data->Z / 50),
				'zone_id'     => $data->TerritoryType->PlaceName->ID,
				'area_id'     => $data->PlaceName->ID,
			], $data->ID);
		});
	}

	public function mobs()
	{
		$this->loopEndpoint('bnpcname', [
			'ID',
			'Name',
		], function($data) {
			// Skip empty names
			if ($data->Name == '')
				return;

			$this->aspir->setData('mob', [
				'id'      => $data->ID,
				'name'    => $data->Name,
				'quest'   => null, // Filled in later
				'level'   => null, // Filled in later
				'zone_id' => null, // Filled in later
			], $data->ID);
		});
	}

	public function npcs()
	{
		// 3000 calls were taking over the allotted 10s call limit imposed by XIVAPI's Guzzle Implementation
		$this->limit = 1000;

		$this->loopEndpoint('enpcresident', [
			'ID',
			'Name',
			'Quests.*.ID',
			'GilShop.*.ID',
			'GilShop.*.Name',
			'SpecialShop.*.ID',
			'SpecialShop.*.Name',
		], function($data) {
			// Skip empty names
			if ($data->Name == '')
				return;

			$this->aspir->setData('npc', [
				'id'      => $data->ID,
				'name'    => $data->Name,
				'zone_id' => null, // Filled in later
				'approx'  => null, // Filled in later
				'x'       => null, // Filled in later
				'y'       => null, // Filled in later
			], $data->ID);

			if ($data->Quests[0]->ID)
				foreach ($data->Quests as $quest)
					if ($quest->ID)
						$this->aspir->setData('npc_quest', [
							'quest_id' => $quest->ID,
							'npc_id' => $data->ID,
						]);

			foreach (['GilShop', 'SpecialShop'] as $shopType)
				if ($data->$shopType)
					foreach ($data->$shopType as $shop)
						if ($shop->ID)
						{
							$this->aspir->setData('shop', [
								'id'   => $shop->ID,
								'name' => $shop->Name,
							], $shop->ID);

							$this->aspir->setData('npc_shop', [
								'shop_id' => $shop->ID,
								'npc_id' => $data->ID,
							]);
						}
		}, [
			'ids' => function($value, $key) {
				// After ID 1028800, Quests, GilShop and SpecialShop all disappear, causing errors
				return $value < 1028800;
			}
		]);

		$limit = null;
	}

	public function quests()
	{
		// 3000 calls were taking over the allotted 10s call limit imposed by XIVAPI's Guzzle Implementation
		$this->limit = 500;

		$this->loopEndpoint('quest', [
			'ID',
			'Name',
			'ClassJobCategory0TargetID',
			'ClassJobLevel0',
			'SortKey',
			'PlaceNameTargetID',
			'IconID',
			'IssuerStart',
			'TargetEnd',
			'JournalGenreTargetID',
			// Rewards; 00-05 are guaranteed. 10-14 are choices. Catalysts are likely guaranteed as well.
			// 	Make sure the Target's are "Item"
			'ItemReward00',
			'ItemCountReward00',
			'ItemReward01',
			'ItemCountReward01',
			'ItemReward02',
			'ItemCountReward02',
			'ItemReward03',
			'ItemCountReward03',
			'ItemReward04',
			'ItemCountReward04',
			'ItemReward05',
			'ItemCountReward05',
			'ItemReward10Target',
			'ItemReward10TargetID',
			'ItemCountReward10',
			'ItemReward11Target',
			'ItemReward11TargetID',
			'ItemCountReward11',
			'ItemReward12Target',
			'ItemReward12TargetID',
			'ItemCountReward12',
			'ItemReward13Target',
			'ItemReward13TargetID',
			'ItemCountReward13',
			'ItemReward14Target',
			'ItemReward14TargetID',
			'ItemCountReward14',
			'ItemCatalyst0Target',
			'ItemCatalyst0TargetID',
			'ItemCountCatalyst0',
			'ItemCatalyst1Target',
			'ItemCatalyst1TargetID',
			'ItemCountCatalyst1',
			'ItemCatalyst2Target',
			'ItemCatalyst2TargetID',
			'ItemCountCatalyst2',
			// Required; There's like 40 of these, but I'm only going to go for 10
			'ScriptInstruction0',
			'ScriptArg0',
			'ScriptInstruction1',
			'ScriptArg1',
			'ScriptInstruction2',
			'ScriptArg2',
			'ScriptInstruction3',
			'ScriptArg3',
			'ScriptInstruction4',
			'ScriptArg4',
			'ScriptInstruction5',
			'ScriptArg5',
			'ScriptInstruction6',
			'ScriptArg6',
			'ScriptInstruction7',
			'ScriptArg7',
			'ScriptInstruction8',
			'ScriptArg8',
			'ScriptInstruction9',
			'ScriptArg9',
		], function($data) {
			// Skip empty names
			if ($data->Name == '')
				return;

			$this->aspir->setData('quest', [
				'id'              => $data->ID,
				'name'            => $data->Name,
				'job_category_id' => $data->ClassJobCategory0TargetID,
				'level'           => $data->ClassJobLevel0,
				'sort'            => $data->SortKey,
				'zone_id'         => $data->PlaceNameTargetID,
				'icon'            => $data->IconID,
				'issuer_id'       => $data->IssuerStart,
				'target_id'       => $data->TargetEnd,
				'genre'           => $data->JournalGenreTargetID,
			], $data->ID);

			// Required Items
			foreach (range(0, 9) as $slot)
				if (substr($data->{'ScriptInstruction' . $slot}, 0, 5) == 'RITEM')
					$this->aspir->setData('quest_required', [
						'item_id'  => $data->{'ScriptArg' . $slot},
						'quest_id' => $data->ID,
					]);

			// Reward Items, Guaranteed, 00-05
			foreach (range(0, 5) as $slot)
				if ($data->{'ItemReward0' . $slot})
					$this->aspir->setData('quest_reward', [
						'item_id'  => $data->{'ItemReward0' . $slot},
						'quest_id' => $data->ID,
						'amount'   => $data->{'ItemCountReward0' . $slot},
					]);

			// Reward Items, Optional, 10-14
			foreach (range(10, 14) as $slot)
				if ($data->{'ItemReward' . $slot . 'TargetID'} && $data->{'ItemReward' . $slot . 'Target'} == 'Item')
					$this->aspir->setData('quest_reward', [
						'item_id'  => $data->{'ItemReward' . $slot . 'TargetID'},
						'quest_id' => $data->ID,
						'amount'   => $data->{'ItemCountReward' . $slot},
					]);

			// Reward Items/Catalyst Items
			foreach (range(0, 2) as $slot)
				if ($data->{'ItemCatalyst' . $slot . 'TargetID'} && $data->{'ItemCatalyst' . $slot . 'Target'} == 'Item')
					$this->aspir->setData('quest_reward', [
						'item_id'  => $data->{'ItemCatalyst' . $slot . 'TargetID'},
						'quest_id' => $data->ID,
						'amount'   => $data->{'ItemCountCatalyst' . $slot},
					]);
		});

		$limit = null;
	}








	/**
	 * loopEndpoint - Loop around an XIVAPI Endpoint
	 * @param  string   $endpoint Any type of `/content`
	 * @param  array    $columns  Specific columns to reduce XIVAPI Load
	 * @param  function $callback $data is passed into here
	 * @param  array    $filters  An array of callback functions; A way to reduce identifiers even more
	 */
	private function loopEndpoint($endpoint, $columns, $callback, $filters = [])
	{
		$request = $this->listRequest($endpoint, ['columns' => ['ID']]);
		foreach ($request->chunk(100) as $chunk)
		{
			$ids = $chunk->map(function($item) {
				return $item->ID;
			});

			if (isset($filters['ids']))
				$ids = $ids->filter($filters['ids']);

			if (empty($ids))
				continue;

			$chunk = $this->request($endpoint, ['ids' => $ids->join(','), 'columns' => $columns]);

			foreach ($chunk->Results as $data)
				$callback($data);
		}
	}

	private function listRequest($content, $queries = [])
	{
		$queries['limit'] = $this->limit !== null ? $this->limit : 3000; // Maximum allowed per https://xivapi.com/docs/Game-Data#lists
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
		$command =& $this->aspir->command;
		$api =& $this->api;

		return Cache::rememberForever($content . serialize($queries), function() use ($content, $queries, $api, $command) {
			$command->info(
				'Querying: ' . $content .
				(isset($queries['ids']) ? ' ' . preg_replace('/,.+,/', '-', $queries['ids']) : '')
			);
			return $api->queries($queries)->content->{$content}()->list();
		});
	}

}
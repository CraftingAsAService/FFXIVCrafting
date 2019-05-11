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

	public function fishing()
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

	public function mob()
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













	private function loopEndpoint($endpoint, $columns, $callback)
	{
		$request = $this->listRequest($endpoint, ['columns' => ['ID']]);
		foreach ($request->chunk(100) as $chunk)
		{
			$ids = $chunk->map(function($item) {
				return $item->ID;
			})->join(',');

			$chunk = $this->request($endpoint, ['ids' => $ids, 'columns' => $columns]);

			foreach ($chunk->Results as $data)
				$callback($data);
		}
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
		$api =& $this->api;
		return Cache::rememberForever($content . serialize($queries), function() use ($content, $queries, $api)
		{
			return $api->queries($queries)->content->{$content}()->list();
		});
	}

}
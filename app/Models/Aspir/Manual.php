<?php

/**
 * Manual
 * 	Manually parsed data; by hand
 */

namespace App\Models\Aspir;

class Manual
{

	public $aspir;

	private $path;

	public function __construct(&$aspir)
	{
		$this->path = storage_path('app/osmose/');

		$this->aspir =& $aspir;
	}

	public function nodeCoordinates()
	{
		// Node Coordinates file is manually built
		$coordinates = $this->readTSV($this->path . 'nodeCoordinates.tsv');

		// Gather all locations, ID => LocationID for the parental relationship
		$areaFinder = collect($this->aspir->data['location'])->pluck('name', 'id');

		// GatheringPointName Conversions, for coordinate matching
		$typeConverter = [
			// Type => Matching Text
			0 => 'Mineral Deposit', // via Mining
			1 => 'Rocky Outcrop', // via Quarrying
			2 => 'Mature Tree', // via Logging
			3 => 'Lush Vegetation Patch', // via Harvesting
		];

		foreach ($this->aspir->data['node'] as &$node)
			$node['coordinates'] = isset($areaFinder[$node['zone_id']]) && isset($typeConverter[$node['type']])
				? $coordinates
					->where('location', $areaFinder[$node['zone_id']])
					->where('level', $node['level'])
					->where('type', $typeConverter[$node['type']])
					->pluck('coordinates')->join(', ', ' or ')
				: null;
	}

	public function randomVentureItems()
	{
		// Random Venture Items file is manually built
		$randomVentureItems = $this->readTSV($this->path . 'randomVentureItems.tsv')
			->pluck('items', 'venture');

		// The `venture` column should match against a `venture.name`
		//  Likewise, exploding the `items` column on a comma, then looping those against the `item.name` should produce a match
		//  And voila, populate `item_venture`
		foreach ($randomVentureItems as $venture => $items)
		{
			$items = explode(',', str_replace(', ', ',', $items));
			dd($venture, $items);



			// $this->aspir->setData('item_venture', [
			// 	'venture_id' => $venture->id,
			// 	'item_id'    => $item->id,
			// ]);
		}

	}

	private function readTSV($filename)
	{
		$tsv = array_map(function($l) { return str_getcsv($l, '	'); }, file($filename));

		array_walk($tsv, function(&$a) use ($tsv) {
			$a = array_combine($tsv[0], $a);
		});
		array_shift($tsv);

		return collect($tsv);
	}

}
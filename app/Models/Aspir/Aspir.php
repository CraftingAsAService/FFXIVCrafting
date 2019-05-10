<?php

/**
 * Aspir
 * 	This spell absorbs DATA from ANYWHERE
 * 	By scanning multiple sources of data, it builds CSV files matching database structure
 */

namespace App\Models\Aspir;

use App\Models\Aspir\XIVAPI;

class Aspir
{

	protected $storage;
	protected $xivapi;

	public $data = [
		'achievement' => [],
		'location' => [],
		'node' => [],
		'item_node' => [],
		'fishing' => [],
		'fishing_item' => [],
	];

	public function __construct()
	{
		$this->xivapi = new XIVAPI($this->data);
	}

	public function run()
	{
		set_time_limit(0);

		//
		// KEEP
		//
		// If you're interested in more translation, it can take quite a bit of processing to get all the useful data out.  All my stuff is open source here, https://github.com/ufx/GarlandTools/blob/master/Garland.Data/Modules/Nodes.cs.  For the most part the s prefix stands for "SaintCoinach" and generally represents what you'll find on xivapi.
		// > I've spent hours looking for a way to connect BNPCs to zone and level
		// This isn't in the client data files so xivapi can't provide it. My site uses a combination of a private server with packet-scraped data, and the defunct Libra Eorzea database for this. See: https://github.com/ufx/GarlandTools/blob/master/Garland.Data/Modules/Mobs.cs
		// > ENPC to zone
		// There's an algorithm you can use to connect a lot of these via the Level table. A significant amount still come from the defunct Libra Eorzea, but I've been working on an alternative method to pull them from the binary world data. See: https://github.com/ufx/GarlandTools/blob/master/Garland.Data/Modules/Territories.cs and https://github.com/ufx/SaintCoinach/blob/master/SaintCoinach/Xiv/Map.cs#L205
		// > Items to an instance
		// Those also mostly come from Libra and won't be updated anymore. Best alternative is scraping the lodestone HTML, but I haven't had time for this.
		// To be honest, crafters rely on lots of disparate data sources that I put a lot of work into bringing together. The raw game data is just one piece of the puzzle - there's manual input sources (https://docs.google.com/spreadsheets/d/1hEj9KCDv0TT1NiGJ0S7afS4hfGMPb6tetqXQetYETUE/edit#gid=953424709), reverse-engineered algorithms acting on the data, a few piles of hacks for weird stuff, that defunct Libra Eorzea database, and some web scraping to bring it all together. You may have better luck picking up my data imports via my open source Garland code. There's a setup & contribution guide if you're interested: https://github.com/ufx/GarlandTools/blob/master/CONTRIBUTING.md. Happy to help with any questions you've got for it.

		$this->xivapi->achievements();

		$this->xivapi->locations();

		$this->xivapi->nodes();
		$this->nodeCoordinates();

		$this->xivapi->fishing();




		foreach ($this->data as $filename => $data)
			$this->writeToJSON($filename, $data);
	}

	private function nodeCoordinates()
	{
		// Coordinates file is manually built
		$coordinateFile = storage_path('app/osmose/nodeCoordinates.tsv');
		$tsv = array_map(function($l) { return str_getcsv($l, '	'); }, file($coordinateFile));
		array_walk($tsv, function(&$a) use ($tsv) {
			$a = array_combine($tsv[0], $a);
		});
		array_shift($tsv);

		$coordinates = collect($tsv);

		// Gather all locations, ID => LocationID for the parental relationship
		$areaFinder = collect($this->data['location'])->pluck('name', 'id');

		// GatheringPointName Conversions, for coordinate matching
		$typeConverter = [
			// Type => Matching Text
			0 => 'Mineral Deposit', // via Mining
			1 => 'Rocky Outcrop', // via Quarrying
			2 => 'Mature Tree', // via Logging
			3 => 'Lush Vegetation Patch', // via Harvesting
		];

		foreach ($this->data['node'] as &$node)
			$node['coordinates'] = isset($areaFinder[$node['zone_id']]) && isset($typeConverter[$node['type']])
				? $coordinates
					->where('location', $areaFinder[$node['zone_id']])
					->where('level', $node['level'])
					->where('type', $typeConverter[$node['type']])
					->pluck('coordinates')->join(', ', ' or ')
				: null;
	}

	private function writeToJSON($filename, $list)
	{
		file_put_contents(storage_path('app/aspir/' . $filename . '.json'), json_encode($list, JSON_PRETTY_PRINT));
	}

	// private function getColumnNames($table)
	// {
	// 	return \Schema::getColumnListing($table);
	// }

}
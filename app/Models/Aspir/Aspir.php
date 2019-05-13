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

	// The AspirSeeder also depends on this list
	public $data = [
		'achievement'      => [],
		'location'         => [],
		'node'             => [],
		'item_node'        => [],
		'fishing'          => [],
		'fishing_item'     => [],
		'mob'              => [],
		'item_mob'         => [],
		'npc'              => [],
		'npc_quest'        => [],
		'shop'             => [],
		'npc_shop'         => [],
		'quest'            => [],
		'quest_reward'     => [],
		'quest_required'   => [],
		'instance'         => [],
		'instance_item'    => [],
		'instance_mob'     => [],
		'job_category'     => [],
		'job_job_category' => [],
		'venture'          => [],
		'item_venture'     => [],
		// 'item_shop'      => [],
	];

	protected $xivapi;
	protected $garlandtools;
	protected $manual;

	public $command;

	public function __construct(&$command)
	{
		$this->command =& $command;

		$this->xivapi       = new XIVAPI($this);
		$this->garlandtools = new GarlandTools($this);
		$this->manual       = new Manual($this);
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

		$xivapiCalls = [
			// 'achievements',
			// 'locations',
			// 'nodes',
			// 'fishingSpots',
			// 'mobs',
			// 'npcs',
			// 'quests',
			// 'instances',
			// 'jobs',
			// 'job_categories',
			// 'ventures',
		];

		$garlandtoolsCalls = [
			// 'mobs',
			// 'npcs',
			// 'instances',
		];

		$manualCalls = [
			// 'nodeCoordinates',
			// 'randomVentureItems', // TODO, COME BACK TO WHEN ITEMS EXIST
		];

		$rowCounts = [];
		foreach (['xivapi', 'garlandtools', 'manual'] as $type)
		{
			$this->command->comment('Beginning ' . $type . ' Calls');
			foreach (${$type . 'Calls'} as $function)
			{
				$prevRowCounts = $rowCounts;

				$this->$type->$function();

				$rowCounts = array_filter(array_map(function($values) {
					return count($values);
				}, $this->data), function($amount) {
					return $amount > 0;
				});

				foreach (array_diff($rowCounts, $prevRowCounts) as $k => $count)
					$this->command->info($k . ' now has ' . $count . ' rows');
			}
		}

			// $this->venture($core->ventureIndex);
			// $this->leve();
			// $this->item_category($core->item->categoryIndex);
			// $this->item();
		// TODO - Shop Items ^ Here
		    // "GameContentLinks": {
      //   "GilShopItem": {
      //       "Item": [
      //           "262157.0", // <-- TAKE OFF THE . and anything after
		//     "GameContentLinks": {
        // "SpecialShop": {
        //     "ItemCost***": [
                // 1769514,

			// // Custom Data Manipulation, careers section
			// $this->career();

		$this->saveData();
	}

	public function setData($table, $row, $id = null)
	{
		// If id is null, use the length of the existing data, or check in the $row for it
		$id = $id ?: (isset($row['id']) ? $row['id'] : count($this->data[$table]) + 1);

		if (isset($this->data[$table][$id]))
			$this->data[$table][$id] = array_merge($this->data[$table][$id], $row);
		else
			$this->data[$table][$id] = $row;

		return $id;
	}

	private function saveData()
	{
		$this->command->comment('Saving Data');

		foreach ($this->data as $filename => $data)
			$this->writeToJSON($filename, $data);
	}

	private function writeToJSON($filename, $list)
	{
		if (empty($list))
			$this->command->comment('No data for ' . $filename);
		else
			$this->command->info('Saving ' . $filename . '.json');

		file_put_contents(storage_path('app/aspir/' . $filename . '.json'), json_encode($list, JSON_PRETTY_PRINT));
	}

	// private function getColumnNames($table)
	// {
	// 	return \Schema::getColumnListing($table);
	// }

}
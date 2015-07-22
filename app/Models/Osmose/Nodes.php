<?php

namespace App\Models\Osmose;

class Nodes 
{

	public function run()
	{
		// Relevant XIVDB files
		$locations = json_decode(file_get_contents(FileHandler::path() . 'locations.json'));
		$gn = json_decode(file_get_contents(FileHandler::path() . 'gathering_nodes.json'));
		$gni = json_decode(file_get_contents(FileHandler::path() . 'gathering_node_item.json'));

		// Relevant Libra files
		$maps = json_decode(file_get_contents(FileHandler::path() . 'maps.json'));
		$placenames = FileHandler::get_data('PlaceName');

		// We want Placenames to be the "system of record", so we're making everything else conform

		// Let's link up our PlaceName and locations.json
		$location_to_placename = [];

		foreach ($locations as $l)
			foreach ($placenames as $p)
				if (strtolower($l->name) == strtolower($p->name->en))
				{
					$location_to_placename[$l->id] = $p->id;
					continue 2;
				}

		// And we need to link up PlaceName to Map ID
		$map_to_placename = [];

		// Some weren't found, but they were inconsequential
		foreach ($maps as $id => $regions)
			foreach ($regions as $region)
				foreach ($placenames as $p)
					if (strtolower($region->REGION) == strtolower($p->name->en))
					{
						$map_to_placename[$id] = $p->id;
						continue 3;
					}

		// Regions have Clusters
		// Clusters have Nodes
		// Clusters have Items
		// - Nodes semantically have Items, but they're all the same in a Cluster

		$clusters = $icons = [];

		$action_list = array(
			'Logging' => 'mature tree',
			'Harvesting' => 'lush vegetation',
			'Quarrying' => 'rocky outcrop',
			'Mining' => 'mineral deposit'
		);

		// $gn defines out clusters
		foreach ($gn as $cluster)
		{
			$cluster->placename_id = $location_to_placename[$cluster->location_id];

			// Figure out the cluster nodes
			$cluster->icon = '';
			$cluster->nodes = [];

			foreach ($maps->{$cluster->placename_id} as $map_id => $section)
			{
				if ( ! is_array($section->{substr($cluster->class, 0, 1) . 'POINT'}))
					continue;
				
				foreach ($section->{substr($cluster->class, 0, 1) . 'POINT'} as $node)
				{
					if ($node->description == '')
						continue;

					if (preg_match('/bugged/', $node->description))
						continue;

					// Get the type and level
					preg_match('/^lv(\.?\s?)?(\d+)\b(.*)$/', strtolower($node->description), $matches);

					if (count($matches) != 4)
					{
						// handle special cases
						// Description may be junk, but it'll be kicked out later
						$type = $node->description;
						$level = 50;
					}
					else
						list($ignore, $ignore, $level, $type) = $matches;

					// Compare Levels
					if ($level != $cluster->level)
						continue;

					$type = trim(ltrim($type, ' -'));

					// Tie Action to Type

					// Rule out bad apples, nonmatching types
					$continue = true;
					foreach ($action_list as $a => $t)
						if ($cluster->action == $a && preg_match('/' . $t . '/', $type))
							$continue = false;

					if ($continue)
						continue;

					// We've passed all tests, this node point belongs to this cluster

					// $icons[] = $node->iconUrl;

					$cluster->icon = preg_replace('/^.*\/(\d+\.png)$/', '$1', $node->iconUrl);

					$cluster->nodes[] = array(
						'id' => $node->id,
						'description' => ucwords($type),
						'x' => number_format($node->locX, 4, '.', ','),
						'y' => number_format($node->locY, 4, '.', ',')
					);
				}
			}

			// Find out the "center" of the nodes, and the radius
			$xs = $ys = [];
			$radius = $center_x = $center_y = 0;

			foreach ($cluster->nodes as $n)
			{
				$xs[] = $n['x'];
				$ys[] = $n['y'];
			}

			// sort($xs);
			// sort($ys);

			$cluster->center_x = count($xs) ? number_format(array_sum($xs) / count($xs), 4, '.', ',') : 0;
			$cluster->center_y = count($ys) ? number_format(array_sum($ys) / count($ys), 4, '.', ',') : 0;

			// var_dump(
			// 	abs(max($xs)) - abs($center_x),
			// 	$center_x,
			// 	abs(min($xs)) - abs($center_x),
			// 	abs(max($ys)) - abs($center_y), 
			// 	$center_y,
			// 	abs(min($ys)) - abs($center_y)
			// );

			// $radius = max(
			// 		// Max between x and y
			// 		max(abs(max($xs)) - abs($center_x), abs(min($xs)) - abs($center_x)),
			// 		max(abs(max($ys)) - abs($center_y), abs(min($ys)) - abs($center_y))
			// 	);

			// var_dump($xs, $ys, $center_x, $center_y, $radius);

			// exit;

			// Figure out the cluster items
			$cluster->items = [];

			foreach ($gni as $ni)
				if ($ni->gathering_node_id == $cluster->id)
					$cluster->items[] = $ni->item_id;

			$cluster->items = array_unique($cluster->items);

			unset($cluster->area_id, $cluster->location_id);

			$clusters[] = $cluster;
		}

		// Test code to make sure I have available icons
		// $icons = array_unique($icons);
		// foreach ($icons as $icon)
		// 	echo '<img src="http://xivdbimg.zamimg.com/' . $icon . '">';

		// Now put all these in flat TSV's, easy for importing

		// Manual Class to classjob_id translations (as it relates to app_data.sqlite)
		$MIN = 16;
		$BTN = 17;

		$cluster_items_id = 1;

		$flat_clusters = $flat_cluster_nodes = $flat_cluster_items = [];
		foreach ($clusters as $cluster)
		{
			// Decided "action" was pointless
			// id, placename_id, classjob_id, level, icon, x, y
			$flat_clusters[] = implode("\t", array(
				$cluster->id,
				$cluster->placename_id,
				${$cluster->class},
				$cluster->level,
				$cluster->icon,
				$cluster->center_x,
				$cluster->center_y
			));

			foreach ($cluster->nodes as $node)
				// id, cluster_id, description, x, y
				$flat_cluster_nodes[] = implode("\t", array(
					$node['id'],
					$cluster->id,
					$node['description'],
					$node['x'],
					$node['y']
				));

			foreach ($cluster->items as $item_id)
				// id, cluster_id, item_id
				$flat_cluster_items[] = implode("\t", array(
					$cluster_items_id++,
					$cluster->id,
					$item_id
				));
		}

		// NOTICE
		// Manual Overriding!

		// Patch 2.1

		// Miner
		// Marble / Fine Sand / Potter's Clay / Limestone / Granite


		// Marble
		$flat_clusters[] = "7010	1	$MIN				";
		$flat_cluster_items[] = $cluster_items_id++ . "	7010	7010";

		// Fine Sand
		$flat_clusters[] = "5267	1	$MIN				";
		$flat_cluster_items[] = $cluster_items_id++ . "	5267	5267";

		// Potter's Clay
		$flat_clusters[] = "5513	1	$MIN				";
		$flat_cluster_items[] = $cluster_items_id++ . "	5513	5513";

		// Limestone
		$flat_clusters[] = "5230	1	$MIN				";
		$flat_cluster_items[] = $cluster_items_id++ . "	5230	5230";

		// Granite
		$flat_clusters[] = "7008	1	$MIN				";
		$flat_cluster_items[] = $cluster_items_id++ . "	7008	7008";


		// Botanist
		// Humus / Bloodgrass / Maiden Grass / Island Seedling / Shroud Seedling / Desert Seedling

		// Humus
		$flat_clusters[] = "5514	1	$BTN				";
		$flat_cluster_items[] = $cluster_items_id++ . "	5514	5514";

		// Bloodgrass 
		$flat_clusters[] = "7011	1	$BTN				";
		$flat_cluster_items[] = $cluster_items_id++ . "	7011	7011";

		// Maiden Grass
		$flat_clusters[] = "7012	1	$BTN				";
		$flat_cluster_items[] = $cluster_items_id++ . "	7012	7012";

		// Island Seedling
		$flat_clusters[] = "7029	1	$BTN				";
		$flat_cluster_items[] = $cluster_items_id++ . "	7029	7029";

		// Shroud Seedling
		$flat_clusters[] = "7030	1	$BTN				";
		$flat_cluster_items[] = $cluster_items_id++ . "	7030	7030";

		// Desert Seedling
		$flat_clusters[] = "7031	1	$BTN				";
		$flat_cluster_items[] = $cluster_items_id++ . "	7031	7031";
		
		// END MANUAL INSERTIONS


		file_put_contents(FileHandler::path() . 'clusters.tsv', implode("\n", $flat_clusters));
		file_put_contents(FileHandler::path() . 'cluster_nodes.tsv', implode("\n", $flat_cluster_nodes));
		file_put_contents(FileHandler::path() . 'cluster_items.tsv', implode("\n", $flat_cluster_items));
	}

}
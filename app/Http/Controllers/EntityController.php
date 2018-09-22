<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Garland\Item;
use App\Models\Garland\Job;

class EntityController extends Controller {

	/**
	 * Display the specified resource.
	 *  This is an ajax call, and it outputs a modal
	 *
	 * @param  int  $id
	 * @param  int  $id
	 * @return Response
	 */
	public function show(Item $item, $type)
	{
		return $this->$type($item);
	}

	private function leves($item)
	{
		$item->load('leve_rewards', 'leve_rewards.job_category', 'leve_rewards.job_category.jobs');

		$leves = [];
		foreach ($item->leve_rewards as $leve)
			$leves[$leve->pivot->rate * 100][$leve->pivot->amount ?: 1][$leve->level][] = [
				'name' => $leve->name,
				'job_count' => $leve->job_category->jobs->count(),
				'job_category_name' => $leve->job_category->name,
				'job' => $leve->job_category->jobs[0],
			];

		krsort($leves);
		foreach (array_keys($leves) as $percent)
		{
			krsort($leves[$percent]);
			foreach (array_keys($leves[$percent]) as $amount)
				krsort($leves[$percent][$amount]);
		}

		return view('entity.leves', compact('item', 'leves'));
	}

	private function quests($item)
	{
		$item->load('quest_rewards', 'quest_rewards.location', 'quest_rewards.location.location', 'quest_rewards.npcs');

		return view('entity.quests', compact('item'));
	}

	private function shops($item)
	{
		$item->load('shops', 'shops.npcs', 'shops.npcs.location');

		$shops = [];
		foreach ($item->shops as $shop)
			foreach ($shop->npcs as $npc)
				$shops[isset($shop->name) ? $shop->name : ''][] = [
					'name' => $npc->name,
					'location' => is_null($npc->location) ? '' : $npc->location->name,
					'x' => (int) $npc->x,
					'y' => (int) $npc->y,
				];

		return view('entity.shops', compact('item', 'shops'));
	}

	private function mobs($item)
	{
		$item->load('mobs', 'mobs.location', 'mobs.instances');

		return view('entity.mobs', compact('item', 'shops'));
	}

	private function instances($item)
	{
		$item->load('instances', 'instances.location', 'instances.location.location');

		return view('entity.instances', compact('item', 'shops'));
	}

	// private function nodes($item)
	// {
	// 	return $this->_nodes($item, null, range(0,3));
	// }

	private function minnodes($item)
	{
		$job = Job::where('abbr', 'min')->first();
		return $this->_nodes($item, $job, [0, 1]); // 0 == Mineral Deposit, 1 == Rocky Outcropping
	}

	private function btnnodes($item)
	{
		$job = Job::where('abbr', 'btn')->first();
		return $this->_nodes($item, $job, [2, 3]); // 2 == Mature Tree, 3 == Lush Vegetation
	}

	private function _nodes($item, $job, $allowed_types)
	{
		$item->load('nodes', 'nodes.zone', 'nodes.area', 'nodes.bonuses');

		$type_translation = [
			// Uses natural indexes of 0 - 3
			'Mineral Deposit',
			'Rocky Outcropping',
			'Mature Tree',
			'Lush Vegetation',
		];

		// Sort the nodes into zones, then areas
		$nodes = [];
		foreach ($item->nodes as $node)
		{
			if ( ! in_array($node->type, $allowed_types))
				continue;

			if ( ! isset($node->zone->name) || ! isset($node->area->name))
				continue;

			$nodes[$node->zone->name][$node->area->name][$type_translation[$node->type]] = true;
		}

		return view('entity.nodes', compact('item', 'job', 'nodes'));
	}

	private function recipes($item)
	{
		$item->load('recipes', 'recipes.job', 'recipes.reagents');

		return view('entity.recipes', compact('item'));
	}

}

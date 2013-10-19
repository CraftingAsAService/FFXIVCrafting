<?php

class FoodController extends BaseController {

	public function getIndex()
	{
		// Items that are Food
		$results = Item::with('stats')
			->where('role', 'meal')
			->orderBy('id')
			->get();

		// Group the food up
		$food_groups = array();
		foreach($results as $item)
		{
			$stats = array();
			foreach ($item->stats as $stat)
			{
				if (in_array($stat->name, array('Duration', 'EXP Bonus')))
					continue;

				$stats[$stat->name] = array(
					'amount' => $stat->pivot->amount,
					'max' => $stat->pivot->maximum,
					'threshold' => round($stat->pivot->maximum / ($stat->pivot->amount / 100))
				);
			}

			$names = array_keys($stats);
			sort($names);

			$food_groups[implode('|', $names)][] = array(
				'id' => $item->id,
				'icon' => $item->icon,
				'name' => $item->name,
				'stats' => $stats
			);
		}
		ksort($food_groups);
		
		#	uasort($food_groups[$key], array($this, 'food_stat_sort'));

		#dd($food_groups);

		return View::make('food')
			->with('active', 'food')
			->with('food_groups', $food_groups);
	}

	// private function food_stat_sort($a, $b)
	// {
	// 	// Multi part sort, depending on how many elements are there
	// 	foreach($a['stats'] as $key => $values)
	// 	{
	// 		$return = $values['amount'] < $b['stats'][$key]['amount'] ? -1 : 1;
	// 		break;
	// 	}

	// 	dd($a, $b);

	// }

}
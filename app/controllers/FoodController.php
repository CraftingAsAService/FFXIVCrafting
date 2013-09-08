<?php

class FoodController extends BaseController {

	public function getIndex()
	{
		// Items that are Food
		$results = Slot::with(array('items', 'items.stats' => function($query) {
				$query->orderBy('item_stat.amount');
			}))
			->where('type', 'food')
			->first();

		// Flatten materia list
		$food_list = array();
		foreach ($results->items as $item)
		{
			$food_list[$item->name] = array(
				'href' => $item->href,
				'stats' => array()
			);

			foreach ($item->stats as $stat)
			{
				$food_list[$item->name]['stats'][] = array(
					'stat' => $stat->name,
					'amount' => rtrim(rtrim(rtrim($stat->pivot->amount, '0'), '0'), '.'),
					'max' => $stat->pivot->maximum
				);
			}
		}

		// Group the food into similar buff buckets
		$food_groups = array(); // Heh

		foreach ($food_list as $name => $attributes)
		{
			$stats = array();
			foreach ($attributes['stats'] as $stat)
				$stats[] = $stat['stat'];

			sort($stats);
			$stats = implode(',', $stats);

			if ( ! isset($food_groups[$stats]))
				$food_groups[$stats] = array();

			$food_groups[$stats][] = $name;
		}

		// var_dump($food_groups);
		// var_dump($food_list);
		// exit;

		return View::make('food')
			->with('active', 'food')
			->with('food_list', $food_list)
			->with('food_groups', $food_groups);
	}
}
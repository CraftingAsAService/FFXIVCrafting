<?php

class FoodController extends BaseController
{

	public function __construct()
	{
		View::share('active', 'food');
	}

	public function getIndex()
	{
		// Items that are Food
		$results = Item::with('name', 'baseparam', 'baseparam.name', 'vendors')
			->where('itemcategory_id', 5)
			->orderBy('id')
			->get();

		// Group the food up
		$food_groups = array();
		foreach($results as $item)
		{
			$stats = array();
			foreach ($item->baseparam as $baseparam)
			{
				$stats[$baseparam->name->term] = array(
					'nq' => array(
						'amount' => (int) $baseparam->pivot->nq_amount,
						'limit' => $baseparam->pivot->nq_limit,
						'threshold' => round($baseparam->pivot->nq_limit / ($baseparam->pivot->nq_amount / 100))
					), 
					'hq' => array(
						'amount' => (int) $baseparam->pivot->hq_amount,
						'limit' => $baseparam->pivot->hq_limit,
						'threshold' => $baseparam->pivot->hq_amount == 0 ? 0 : round($baseparam->pivot->hq_limit / ($baseparam->pivot->hq_amount / 100))
					)
				);
			}

			$names = array_keys($stats);
			sort($names);

			if (empty($names))
				continue;

			$food_groups[implode('|', $names)][] = array(
				'id' => $item->id,
				'name' => $item->name->term,
				'min_price' => $item->min_price,
				'vendor_count' => count($item->vendors),
				'stats' => $stats
			);
		}
		ksort($food_groups);
		
		#dd($food_groups);

		return View::make('pages.food')
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
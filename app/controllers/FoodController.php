<?php

class FoodController extends BaseController
{

	public function __construct()
	{
		View::share('active', 'food');
	}

	public function getIndex()
	{
		list($sections, $translations) = Cache::get('food_sections_' . Config::get('language'), function() {
			// Items that are Food
			$results = Item::with('name', 'baseparam', 'baseparam.name',  'baseparam.en_name', 'vendors')
				->where('itemcategory_id', 5)
				->orderBy('id')
				->get();

			// Group the food up
			$food_groups = $translations = array();
			foreach($results as $item)
			{
				$stats = $names = $key_name = array();
				foreach ($item->baseparam as $baseparam)
				{
					$translations[$baseparam->en_name->term] = $baseparam->name->term;
					$stats[$baseparam->en_name->term] = array(
						'name' => $baseparam->name->term,
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
					'has_hq' => $item->has_hq,
					'name' => $item->name->term,
					'min_price' => $item->min_price,
					'vendor_count' => count($item->vendors),
					'stats' => $stats
				);
			}
			ksort($food_groups);

			// Break these up into groups
			$sections_base = array('data' => array(), 'headers' => array(), 'intersections' => array());
			// This order determines how they show up on the page, so it's kind of important to keep it this way
			$sections = array(
				'Crafting' => $sections_base,
				'Gathering' => $sections_base,
				'Battle' => $sections_base,
				'Resistances' => $sections_base
			);
			foreach ($food_groups as $key => $value)
			{
				$keys = explode('|', $key);

				$belongs_to = 'Battle';
				
				if (count(array_intersect(array('CP', 'Control', 'Craftsmanship'), $keys)) > 0)
					$belongs_to = 'Crafting';
				elseif (count(array_intersect(array('GP', 'Gathering', 'Perception'), $keys)) > 0)
					$belongs_to = 'Gathering';
				elseif (preg_match('/Resistance/', $key))
					$belongs_to = 'Resistances';
				
				$sections[$belongs_to]['data'][$key] = $value;
			}
			
			foreach ($sections as $section_key => $section_array)
			{
				$single_keys = array();
				foreach (array_keys($section_array['data']) as $keys)
					$single_keys = array_merge($single_keys, explode('|', $keys));
				$single_keys = array_unique($single_keys);
				sort($single_keys);
				$sections[$section_key]['headers'] = $single_keys;

				// Also get the Intersections
				$intersections = array();
				foreach ($single_keys as $i)
				{
					if ( ! isset($intersections[$i])) $intersections[$i] = array();
					foreach ($single_keys as $j)
					{
						if ( ! isset($intersections[$i][$j])) $intersections[$i][$j] = 0;
						$looking_for = $i == $j ? 1 : 2;

						foreach ($section_array['data'] as $key => $value)
						{
							$keys = explode('|', $key);
							$matched = 0;
							$bonus = 0;

							if (in_array($i, $keys))
								$matched++;
							if ($i != $j && in_array($j, $keys))
								$matched++;
							if (preg_match('/Vitality/', $key))
							{
								$bonus = 1;
								$matched++;
							}

							if ($matched == $looking_for + $bonus && count($keys) == $looking_for + $bonus)
							{
								// Count base items
								$intersections[$i][$j] += count($value);
								// Count HQ items
								foreach ($value as $x)
									if ($x['has_hq'])
										$intersections[$i][$j]++;
							}
						}
					}
				}
				$sections[$section_key]['intersections'] = $intersections;
			}

			unset($food_groups);

			return array($sections, $translations);
		});

		return View::make('pages.food')
			->with('sections', $sections)
			->with('translations', $translations);
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
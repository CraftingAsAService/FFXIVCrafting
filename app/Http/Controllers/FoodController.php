<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Cache;
use Config;

use App\Models\Garland\Item;
use App\Models\CAAS\Stat;

class FoodController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		view()->share('active', 'food');
	}

	public function getIndex()
	{
		$sections = Cache::get('food_sections_' . Config::get('language'), function() {

			$core_crafting_headers = ['CP', 'Control', 'Craftsmanship', 'Careful Desynthesis'];
			$core_gathering_headers = ['GP', 'Gathering', 'Perception'];
			$core_battle_headers = ['Direct Hit Rate', 'Critical Hit', 'Determination', 'Tenacity', 'Piety', 'Skill Speed', 'Spell Speed', 'Vitality'];
			$core_resistances_headers = ['Reduced Durability Loss'];
			// Items that are Food
			$results = Item::with('attributes', 'shops', 'recipes')
				->where('item_category_id', 46)
				->orderBy('id')
				->get();

			// Group the food up
			$food_groups = $translations = [];
			foreach($results as $item)
			{
				$nq_limit = $hq_limit = 0;
				$stats = [];
				foreach ($item->attributes as $attribute)
				{
					if ( ! in_array($attribute->quality, ['nq', 'hq']))
						continue;

					$attribute_name = Stat::name($attribute->attribute);

					$stats[$attribute_name]['name'] = $attribute_name;
					$stats[$attribute_name][$attribute->quality] = [
						'amount' => $attribute->amount,
						'limit' => $attribute->limit,
						'threshold' => $attribute->amount > 0 ? round($attribute->limit / ($attribute->amount / 100)) : null,
					];
				}

				if (empty($stats))
					continue;

				// if ($item->id == 12844)
				// 	dd($stats);

				// For each combination of two, add a new entry
				foreach ($stats as $i => $i_stats)
				{
					foreach ($stats as $j => $j_stats)
					{
						// Put stats in alphabetical order

						$names = [$i, $j];
						sort($names);
						$names = implode('|', $names);
						list($x, $y) = explode('|', $names);

						$food_groups[$names][$item->id] = [
							'id' => $item->id,
							'icon' => $item->icon,
							'has_hq' => (boolean) (count($item->recipes) > 0 ? $item->recipes[0]->hq : false),
							'name' => $item->display_name,
							'price' => $item->price,
							'shops_count' => count($item->shops),
							'stats' => [
								$x => $stats[$x],
								$y => $stats[$y],
							],
						];
					}
				}
			}
			ksort($food_groups);

			// Break these up into groups
			$sections_base = ['data' => [], 'headers' => [], 'intersections' => []];
			// This order determines how they show up on the page, so it's kind of important to keep it this way
			$sections = [
				'Crafting' => $sections_base,
				'Gathering' => $sections_base,
				'Battle' => $sections_base,
				'Resistances' => $sections_base
			];
			foreach ($food_groups as $key => $value)
			{
				$keys = explode('|', $key);

				$belongs_to = 'Battle';

				// Core tenants of each section
				if (count(array_intersect($core_crafting_headers, $keys)) > 0)
					$belongs_to = 'Crafting';
				elseif (count(array_intersect($core_gathering_headers, $keys)) > 0)
					$belongs_to = 'Gathering';
				elseif (preg_match('/Resistance|Durability/', $key))
					$belongs_to = 'Resistances';

				$sections[$belongs_to]['data'][$key] = $value;
			}

			foreach ($sections as $section_key => $section_array)
			{
				$single_keys = [];
				foreach (array_keys($section_array['data']) as $keys)
					$single_keys = array_merge($single_keys, explode('|', $keys));
				$single_keys = array_unique($single_keys);
				sort($single_keys);
				$sections[$section_key]['headers'] = $single_keys;

				// For Crafting and Gathering, we want the "core tenants" to be first, regardless of the sort().
				if (isset(${'core_' . strtolower($section_key) . '_headers'}))
					$sections[$section_key]['headers'] = array_merge(${'core_' . strtolower($section_key) . '_headers'}, array_diff($single_keys, ${'core_' . strtolower($section_key) . '_headers'}));

				// Also get the Intersections
				$intersections = [];
				foreach ($single_keys as $i)
				{
					if ( ! isset($intersections[$i]))
						$intersections[$i] = [];

					foreach ($single_keys as $j)
					{
						if ( ! isset($intersections[$i][$j]))
							$intersections[$i][$j] = 0;

						// $looking_for = 2;//$i == $j ? 1 : 2;

						foreach ($section_array['data'] as $key => $value)
						{
							$keys = explode('|', $key);
							// $matched = 0;
							// // $bonus = 0;

							// if (in_array($i, $keys))
							// 	$matched++;
							// if (/*$i != $j && */in_array($j, $keys))
							// 	$matched++;
							// if (preg_match('/Vitality/', $key))
							// {
							// 	$bonus = 1;
							// 	$matched++;
							// }

							// if ($matched == $looking_for + $bonus && count($keys) == $looking_for + $bonus)
							if (($i == $keys[0] && $j == $keys[1]) || ($i == $keys[1] && $j == $keys[0]))//$matched == $looking_for)
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

			return $sections;
		});

		return view('pages.food', compact('sections'));
	}

}

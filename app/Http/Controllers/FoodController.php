<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Cache;
use Config;

use App\Models\CAAS\Item;

class FoodController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		view()->share('active', 'food');
	}

	public function getIndex()
	{
		list($sections, $translations) = Cache::get('food_sections_' . Config::get('language'), function() {

			$core_crafting_headers = array('CP', 'Control', 'Craftsmanship', 'Careful Desynthesis');
			$core_gathering_headers = array('GP', 'Gathering', 'Perception');
			$core_battle_headers = array('Accuracy', 'Critical Hit Rate', 'Determination', 'Parry', 'Piety', 'Skill Speed', 'Spell Speed', 'Vitality');
			$core_resistances_headers = array('Reduced Durability Loss');
			// Items that are Food
			$results = Item::with('name', 'baseparam', 'baseparam.name',  'baseparam.en_name', 'vendors')
				->where('itemcategory_id', 5)
				->orderBy('id')
				->get();

			// Group the food up
			$food_groups = $translations = [];
			foreach($results as $item)
			{
				$stats = $names = $key_name = [];
				foreach ($item->baseparam as $baseparam)
				{
					$nq_limit = $baseparam->pivot->nq_limit ?: (int) $baseparam->pivot->nq_amount;
					$hq_limit = $baseparam->pivot->hq_limit ?: (int) $baseparam->pivot->hq_amount;
					
					$translations[$baseparam->en_name->term] = $baseparam->name->term;
					$stats[$baseparam->en_name->term] = [
						'name' => $baseparam->name->term,
						'nq' => [
							'amount' => (int) $baseparam->pivot->nq_amount,
							'limit' => $nq_limit,
							'threshold' => round($nq_limit / ($baseparam->pivot->nq_amount / 100))
						], 
						'hq' => [
							'amount' => (int) $baseparam->pivot->hq_amount,
							'limit' => $hq_limit,
							'threshold' => $baseparam->pivot->hq_amount == 0 ? 0 : round($hq_limit / ($baseparam->pivot->hq_amount / 100))
						]
					];
				}

				if (empty($stats))
					continue;

				// if ($item->id == 10146)
				// 	dd($names, $item);

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
							'has_hq' => $item->has_hq,
							'name' => $item->name->term,
							'min_price' => $item->min_price,
							'vendor_count' => count($item->vendors),
							'stats' => [
								$x => $stats[$x], 
								$y => $stats[$y]
							]
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

			return array($sections, $translations);
		});

		return view('pages.food', compact('sections', 'translations'));
	}

}

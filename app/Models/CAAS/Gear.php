<?php

namespace App\Models\CAAS;

use App\Models\Garland\Job;
use App\Models\Garland\Item;

use Config;

class Gear
{

	/**
	 * Gear Profile, An overview of all gear based on the parameters
	 * @param  string/integer  $job    A string or integer is accepted
	 * @param  integer $starting_level [description]
	 * @param  integer $level_range   [description]
	 * @param  array  $options         [description]
	 * @return array  $equipment_list  an array of equipment slots
	 */
	static public function profile($job = '', $starting_level = 1, $level_range = 1, $options = [])
	{
		// Get the job, depending on string or id
		$job = is_numeric($job)
			? Job::find($job)
			: Job::get_by_abbr($job);

		// Slot data
		$slots = config('site.defined_slots');

		// Stat data
		$stat_ids_to_focus = Stat::get_ids(Stat::gear_focus($job->abbr));

		// Get all items
		$items = self::items($job->id, $starting_level - 10, $starting_level + $level_range, array_keys($slots), $stat_ids_to_focus, $options);

		// Sort those items into the appropriate buckets
		$equipment_list = self::organize($items, $job->abbr, $starting_level, $stat_ids_to_focus, $options);

		return $equipment_list;
	}

	/**
	 * Organize gear into buckets, clean it up too
	 * @param  [type] $items    [description]
	 * @param  [type] $job_abbr [description]
	 * @return [type]           [description]
	 */
	static private function organize($items, $job_abbr, $starting_level, $stat_ids_to_focus, $options)
	{
		// Prepare the result slots
		$equipment_list = array_flip(config('site.equipment_roles'));
		array_walk($equipment_list, function(&$i) { $i = []; });

		// Slot data
		$slots = config('site.defined_slots');
		$slot_alias = config('site.slot_alias');
		// $slot_cannot_equip = config('site.slot_cannot_equip');
		// foreach ($slot_cannot_equip as &$sce)
		// 	foreach ($sce as &$ce)
		// 		$ce = $slots[$ce];
		// unset($sce, $ce);
		// 

		foreach ($items as $item)
		{
			// Determine which equipment slot.  Is it an alias slot (Body & Head/etc) or regular (Body)?
			$slot_name = in_array($item->slot, array_keys($slot_alias))
				? $slots[$slot_alias[$item->slot]]
				: $slots[$item->slot];

			// Add this item to the slot
			$equipment_list[$slot_name][] = $item;
		}

		// Clean up the list, split out HQ as it's own item
		/**
		 * equipment_list
		 * 	main_hand
		 *   lvl(4)
		 *    nq
		 *    hq
		 *     item
		 */
		foreach ($equipment_list as $slot => $items)
		{
			$sorted = [];

			foreach ($items as $item)
			{
				// To prep for the future, tally up the stats
				// But only if they're in focus
				// Every stat has an equal weight
				// TODO this is unfair to alias body pieces (Body & Head, etc)
				$nq_worth = $hq_worth = 0;
				$item->has_hq = false;
				foreach ($item->attributes as $attribute)
				{
					// Test for HQ off the bat
					if ($attribute->quality == 'hq')
						$item->has_hq = true;

					if ( ! in_array($attribute->attribute, $stat_ids_to_focus))
						continue;

					if ( ! in_array($attribute->quality, ['nq', 'hq']))
						continue;

					${$attribute->quality . '_worth'} += $attribute->amount;
				}

				$item->nq_worth = $nq_worth;
				$item->hq_worth = $hq_worth;

				// If the item has no worth, don't include it
				if ($item->nq_worth == 0)
					continue;

				$sorted[$item->elvl]['nq'][] = $item;

				if ($item->has_hq && in_array('hq', $options))
					$sorted[$item->elvl]['hq'][] = $item;
			}

			ksort($sorted);

			// Go through the list and mark best in slot
			// Attempt to mark one for each level, but keep in mind the "previous" BIS.
			// Also remove any levels before $starting_level, however keep ONE that was BIS
			//   And if there's no equipment for that level specifically, keep the one closest/lower than that, and do the removal before even then

			// Determine BIS per level
			// If weights are equal, they're both BIS
			foreach ($sorted as $level => &$bucket)
			{
				$bis = [];
				foreach ($bucket as $quality => $items)
					foreach ($items as $item)
						$bis[$item->{$quality . '_worth'}][$quality][] = $item->id;

				// Highest Common Denominator
				$keys = array_keys($bis);
				$bis_hcd = end($keys);

				// $bis[$bis_hcd]

				$bucket['bis_worth'] = $bis_hcd;
				$bucket['bis_items'] = $bis[$bis_hcd];
			}
			unset($bucket);

			// Level to Keep - Lowest Common Denominator
			$level_lcd = 0;
			foreach (array_keys($sorted) as $level)
				if ($level <= $starting_level && $level > $level_lcd)
					$level_lcd = $level;

			// Go through the sorted items, only go through levels lower than the lcd
			// And of those, only keep one BIS item
			
			// Up first, it's entirely possible that something at level 22 is better than something at 23, for example.
			// compare bis_worth's across those lower-than-lcd-levels
			$level_hcd = $bis_hcd = 0;
			foreach ($sorted as $level => &$bucket)
			{
				$bucket['lcd'] = $level_lcd; // Used in the view later

				if ($level >= $level_lcd)
					continue;

				if ($bucket['bis_worth'] >= $bis_hcd)
				{
					$bis_hcd = $bucket['bis_worth'];
					$level_hcd = $level;
				}
			}
			unset($bucket);

			// Armed with those variables, go back through and unset as much as possible
			foreach ($sorted as $level => &$bucket)
			{
				if ($level >= $level_lcd)
					continue;

				if ($level != $level_hcd)
				{
					unset($sorted[$level]);
					continue;
				}

				// Try to take care of entire quality if it's not in the list
				$drop_quality = array_diff(['nq', 'hq'], array_keys($bucket['bis_items']));

				foreach ($drop_quality as $drop)
					if (isset($bucket[$drop]))
						foreach (array_keys($bucket[$drop]) as $key)
							unset($bucket[$drop][$key]);

				foreach ($bucket['bis_items'] as $quality => $bis_items)
					foreach ($bucket[$quality] as $key => $item)
						if ( ! in_array($item->id, $bis_items))
							unset($bucket[$quality][$key]);
			}
			unset($bucket);

			$equipment_list[$slot] = $sorted;
		}

		return $equipment_list;
	}

	/**
	 * Get all items matching these parameters
	 * @param  [type] $job_id            [description]
	 * @param  [type] $level_start       [description]
	 * @param  [type] $level_end         [description]
	 * @param  [type] $slot_ids          [description]
	 * @param  [type] $stat_ids_to_focus [description]
	 * @param  [type] $options           [description]
	 * @return [type]                    [description]
	 */
	static private function items($job_id, $level_start, $level_end, $slot_ids, $stat_ids_to_focus, $options)
	{
		$job_category_ids = Job::with('categories')->find($job_id)->categories->lists('id')->all();
		// Get all items where:
		// Slot isn't zero
		// The class can use it
		$query = Item::with('job_category', 'job_category.jobs', 'instances', 'achievements', 'attributes', 'mobs', 'shops', 'recipes', 'recipes.job', 'quest_rewards', 'leve_rewards', 'ventures')
			->whereIn('slot', $slot_ids)
			->whereBetween('elvl', [$level_start, $level_end])
			->whereIn('job_category_id', $job_category_ids)
			->whereHas('attributes', function($query) use ($stat_ids_to_focus) {
				$query->whereIn('attribute', $stat_ids_to_focus);
			})
			->orderBy('item.elvl', 'DESC')
			->orderBy('item.ilvl', 'DESC');

		if (in_array('craftable', $options))
		{
			if (in_array('rewardable', $options))
			{
				$query->where(function($query) {
					$query
						->whereHas('recipes', function($query) {
							$query->where('item_id', \DB::raw('item.id'));
						})
						->orHas('quest_rewards')
						->orHas('leve_rewards')
						->orHas('ventures')
						->orHas('achievements');
				});
			}
			else
				$query->whereHas('recipes', function($query) {
					$query->where('item_id', \DB::raw('item.id'));
				});
		}
		
		return $query->get();
	}

	// static private function logger() {
	// 	// \DB::connection()->enableQueryLog();
	//     $queries = \DB::getQueryLog();
	//     $formattedQueries = [];
	//     foreach( $queries as $query ) :
	//         $prep = $query['query'];
	//         foreach( $query['bindings'] as $binding ) :
	//             $prep = preg_replace("#\?#", $binding, $prep, 1);
	//         endforeach;
	//         $formattedQueries[] = $prep;
	//     endforeach;
	//     return $formattedQueries;
	// }

}
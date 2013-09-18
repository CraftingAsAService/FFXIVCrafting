<?php

class GatheringController extends BaseController 
{

	public function getIndex()
	{
		// All Jobs
		$job_list = array();
		foreach (Job::whereIn('disciple', array('DOL','DOH'))->get() as $j)
			$job_list[$j->abbreviation] = $j->name;

		return View::make('gathering')
			->with('active', 'gathering')
			->with('job_list', $job_list);
	}

	public function getList($master_class = 'MIN')
	{
		if ( ! in_array($master_class, array('MIN', 'BTN')))
			exit('invalid class'); // TODO REAL ERROR
		
		// All Jobs
		$job_list = $job_ids = array();
		foreach (Job::whereIn('disciple', array('DOH'))->get() as $j)
		{
			$job_ids[$j->abbreviation] = $j->id;
			$job_list[$j->abbreviation] = $j->name;
		}

		$level_ranges = array();
		for($i = 1; $i <= 55; $i += 5)
			$level_ranges[] = $i;
		
		$items = $this->_recipes($master_class, $job_list, $level_ranges);

		$job = Job::where('abbreviation', $master_class)->first();

		// Check for quests
		$quests = QuestItem::where('job_id', $job->id)
			->orderBy('level')
			->with('item')
			->get();

		return View::make('gathering.list')
			->with('active', 'gathering')
			->with('job', $job)
			->with('items', $items)
			->with('job_list', $job_list)
			->with('level_ranges', $level_ranges)
			->with('quests', $quests);
	}

	private function _recipes($master_class = 'MIN', $job_list = array(), $level_ranges = array())
	{
		$main_cache_key = __METHOD__ . '|' . $master_class;

		if (Cache::has($main_cache_key))
			$item_list = Cache::get($main_cache_key);
		else
		{
			set_time_limit(0);

			// Load every single recipe that is obtainable from a miner
			// Process those into reagents, then by level range, then by profession
			// Calling all recipes is a little server heavy, so split it into sections
			foreach (array_keys($job_list) as $job)
			{
				foreach ($level_ranges as $start)
				{
					$end = $start + 4;

					$cache_key = __METHOD__ . '|' . $master_class . '|' . $job . '|' . $start . '-' . $end;

					if (Cache::has($cache_key))
						$recipes = Cache::get($cache_key);
					else
					{
						$recipes = Recipe::with(array(
								'item', // The recipe's Item
								'reagents', // The reagents for the recipe
									'reagents.jobs' => function($query) use ($master_class) {
										// Only Land Disciples
										$query->where('abbreviation', $master_class);
									},
									'reagents.recipes', 
										'reagents.recipes.item', 
											'reagents.recipes.item.locations',
											'reagents.recipes.item.quest', // Is the item used as a quest turnin?
										'reagents.recipes.job' => function($query) {
											// Only Hand Disciples
											$query->where('disciple', 'DOH');
										},
							))
							->select('recipes.*', 'j.abbreviation')
							->join('jobs AS j', 'j.id', '=', 'recipes.job_id')
							->where('j.abbreviation', $job)
							->whereBetween('level', array($start, $end))
							->orderBy('level')
							->get();

						Cache::put($cache_key, $recipes, Config::get('site.cache_length'));
					}

					foreach ($recipes as $recipe)
						$item_list = $this->_reagents($recipe, $master_class, $job, $start);
				}
			}

			ksort($item_list);
			
			Cache::put($main_cache_key, $item_list, Config::get('site.cache_length'));
		}
		
		return $item_list;
	}

	private function _reagents($recipe = array(), $master_class = '', $origin_crafting_class = '', $level = 1, $multiplier = 1)
	{
		static $item_list = array();

		foreach ($recipe->reagents as $item)
		{
			// If the reagent has a recipe, just parse those
			if (isset($item->recipes[0]))
				$this->_reagents($item->recipes[0], $master_class, $origin_crafting_class, $level, $item->pivot->amount);
			else
			{
				$okay = FALSE;
				foreach ($item->jobs as $j)
					if ($j->abbreviation == $master_class)
						$okay = TRUE;

				if ( ! $okay)
					continue;

				if ( ! isset($item_list[$item->id]))
					$item_list[$item->id] = array(
						'data' => $item,
						'tally' => 0,
						'breakdown' => array()
					);

				$r =& $item_list[$item->id];

				$r['tally'] += $item->pivot->amount * $multiplier;

				if ( ! isset($r['breakdown'][$level]))
					$r['breakdown'][$level] = array();

				if ( ! isset($r['breakdown'][$level][$origin_crafting_class]))
					$r['breakdown'][$level][$origin_crafting_class] = 0;

				$r['breakdown'][$level][$origin_crafting_class] += $item->pivot->amount * $multiplier;

				unset($r);
			}
		}

		return $item_list;
	}

}
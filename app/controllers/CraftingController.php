<?php

class CraftingController extends BaseController 
{

	public function getIndex()
	{
		return View::make('crafting.index')
			->with('error', FALSE)
			->with('active', 'crafting')
			->with('job_list', ClassJob::get_name_abbr_list())
			->with('previous', Cookie::get('previous_crafting_load'));
	}

	public function postIndex()
	{
		$vars = array('class' => 'CRP', 'start' => 1, 'end' => 5, 'self_sufficient' => 0, 'misc_items' => 0);
		$values = array();
		foreach ($vars as $var => $default)
			$values[] = Input::has($var) ? Input::get($var) : $default;

		// Overwrite Class var
		if (Input::has('multi') && Input::has('classes'))
			$values[0] = implode(',', Input::get('classes'));

		$url = '/crafting/list?' . implode(':', $values);

		// Queueing the cookie, we won't need it right away, so it'll save for the next Response::
		Cookie::queue('previous_crafting_load', $url, 525600); // 1 year's worth of minutes
		
		return Redirect::to($url);
	}

	public function getList()
	{
		View::share('active', 'crafting');

		// All Jobs
		$job_list = ClassJob::get_name_abbr_list();
		View::share('job_list', $job_list);

		$include_quests = TRUE;

		if ( ! Input::all())
			return Redirect::to('/crafting');

		// Get Options
		$options = explode(':', array_keys(Input::all())[0]);

		// Parse Options              						// Defaults
		$desired_job     = isset($options[0]) ? $options[0] : 'CRP';
		$start           = isset($options[1]) ? $options[1] : 1;
		$end             = isset($options[2]) ? $options[2] : 5;
		$self_sufficient = isset($options[3]) ? $options[3] : 1;
		$misc_items	 	 = isset($options[4]) ? $options[4] : 0;

		$item_ids = $item_amounts = array();

		$top_level = TRUE;

		if ($desired_job == 'List')
		{
			$start = $end = null;
			$include_quests = FALSE;

			// Get the list
			$item_amounts = Session::get('list', array());

			$item_ids = array_keys($item_amounts);

			if (empty($item_ids))
				return Redirect::to('/list');

			View::share('item_ids', $item_ids);
			View::share('item_amounts', $item_amounts);

			$top_level = $item_amounts;
		}

		if ( ! $item_ids)
		{
			// Jobs are capital
			$desired_job = strtoupper($desired_job);

			// Make sure it's a real job, jobs might be multiple
			$job = array();
			foreach (explode(',', $desired_job) as $ds)
				$job[] = ClassJob::get_by_abbr($ds);

			// If the job isn't real, error out
			if ( ! isset($job[0]))
			{
				// Check for DOL quests
				$quests = array();
				foreach (array('MIN','BTN','FSH') as $job)
				{
					$job = ClassJob::get_by_abbr($job);
					$quests[$job] = QuestItem::where('classjob_id', $job->id)
						->orderBy('level')
						->with('item')
						->get();
				}

				return View::make('crafting')
					->with('error', TRUE)
					->with('quests', $quests);
			}

			$job_ids = array();
			foreach ($job as $j)
				$job_ids[] = $j->id;

			if (count($job) == 1)
				$job = $job[0];

			// Starting maximum of 1
			if ($start < 0) $start = 1;
			if ($start > $end) $end = $start;
			if ($end - $start > 9) $end = $start + 9;

			// Check for quests
			$quest_items = QuestItem::with('classjob', 'classjob.abbr')
				->whereBetween('level', array($start, $end))
				->whereIn('classjob_id', $job_ids)
				->orderBy('level')
				->with('item')
				->get();

			View::share(array(
				'job' => $job,
				'start' => $start,
				'end' => $end,
				'quest_items' => $quest_items,
				'desired_job' => $desired_job
			));
		}

		// Gather Recipes and Reagents

		$query = Recipes::with(
				'name',
				'classjob',
					'classjob.name',
					'classjob.abbr',
				'item', // The recipe's Item
					'item.name',
					'item.quest', // Is the recipe used as a quest turnin?
					'item.leve', // Is the recipe used to fufil a leve?
					'item.vendors',
					'item.beasts',
					'item.clusters',
						'item.clusters.classjob',
							'item.clusters.classjob.abbr',
				'reagents', // The reagents for the recipe
					'reagents.vendors',
					'reagents.beasts',
					'reagents.clusters',
						'reagents.clusters.classjob',
							'reagents.clusters.classjob.abbr',
					'reagents.recipe',
						'reagents.recipe.name',
						'reagents.recipe.item', 
							'reagents.recipe.item.name', 
							'reagents.recipe.item.vendors',
							'reagents.recipe.item.beasts',
							'reagents.recipe.item.clusters',
								'reagents.recipe.item.clusters.classjob',
									'reagents.recipe.item.clusters.classjob.abbr',
						'reagents.recipe.classjob',
							'reagents.recipe.classjob.abbr'
			)
			->groupBy('recipes.item_id')
			->orderBy('rank');

		if ($misc_items == 0 && $desired_job != 'List')
			$query
				->whereHas('item', function($query) {
					$query->whereNotIn('itemcategory_id', array(14, 15)); // ItemCategory 14 == 'Furnishing', 15 == 'Dye'
				});

		if ($item_ids)
			$query
				->whereIn('recipes.item_id', $item_ids);
		else
			$query
				->whereHas('classjob', function($query) use ($job_ids) {
					$query->whereIn('classjob.id', $job_ids);
				})
				->whereBetween('level', array($start, $end));

		$recipes = $query
			->remember(Config::get('site.cache_length'))
			->get();

		// Fix the amount of the top level to be evenly divisible by the amount the recipe yields
		if (is_array($top_level))
		{
			foreach ($recipes as $recipe)
			{
				$tl_item =& $top_level[$recipe->item_id];

				// If they're not evently divisible
				if ($tl_item % $recipe->yields != 0)
					// Make it so
					$tl_item = ceil($tl_item / $recipe->yields) * $recipe->yields;
			}
			unset($tl_item);

			View::share('item_amounts', $top_level);
		}

		$reagent_list = $this->_reagents($recipes, $self_sufficient, 1, $include_quests, $top_level);

		// Look through the list.  Is there something we're already crafting?
		// Subtract what's being made from needed reagents.
		//  Example, culinary 11 to 15, you need olive oil for Parsnip Salad (lvl 13)
		//   But you make 3 olive oil at level 11.  We don't want them crafting another olive oil.
		
		foreach ($recipes as $recipe)
		{
			if ( ! isset($reagent_list[$recipe->item_id]))
				continue;

			$reagent_list[$recipe->item_id]['both_list_warning'] = TRUE;
			$reagent_list[$recipe->item_id]['make_this_many'] += 1;
		}

		// Look through the reagent list, make sure the reagents are evently divisible by what they yield
		foreach ($reagent_list as &$reagent)
			// If they're not evently divisible
			if ($reagent['make_this_many'] % $reagent['yields'] != 0)
				// Make it so
				$reagent['make_this_many'] = ceil($reagent['make_this_many'] / $reagent['yields']) * $reagent['yields'];
		unset($reagent);

		// Let's sort them further, group them by..
		// Gathered, Then by Level
		// Other (likely mob drops)
		// Crafted, Then by level
		// Bought, by price

		$sorted_reagent_list = array(
			'Gathered' => array(),
			'Bought' => array(),
			'Other' => array(),
			'Pre-Requisite Crafting' => array(),
			'Crafting List' => array(),
		);

		foreach ($reagent_list as $reagent)
		{
			$section = 'Other';
			$level = 0;

			// Section
			if (in_array($reagent['self_sufficient'], array('MIN', 'BTN', 'FSH')))
			{
				$section = 'Gathered';
				$level = $reagent['item']->level;
			}
			elseif ($reagent['self_sufficient'])
			{
				$section = 'Pre-Requisite Crafting';
				$level = $reagent['item']->recipe[0]->level;
			}
			elseif (count($reagent['item']->vendors))
			{
				$section = 'Bought';
				$level = $reagent['item']->min_price;
			}

			if ( ! isset($sorted_reagent_list[$section][$level]))
				$sorted_reagent_list[$section][$level] = array();

			$sorted_reagent_list[$section][$level][$reagent['item']->id] = $reagent;
			ksort($sorted_reagent_list[$section][$level]);
		}

		foreach ($sorted_reagent_list as $section => $list)
			ksort($sorted_reagent_list[$section]);

		// Sort the pre-requisite crafting by rank
		// We don't need to sort them by level, just make sure it's in the proper structure
		// The keys don't matter either
		$prc =& $sorted_reagent_list['Pre-Requisite Crafting'];
		
		$new_prc = array('1' => array());
		foreach ($prc as $vals)
			foreach ($vals as $v)
				$new_prc['1'][] = $v;

		// Sort them by rank first
		usort($new_prc['1'], function($a, $b) { 
			return $a['item']->rank - $b['item']->rank; 
		});
		// Then by classjob
		usort($new_prc['1'], function($a, $b) {
			return $a['item']->recipe[0]->classjob_id - $b['item']->recipe[0]->classjob_id; 
		});
		
		$prc = $new_prc;

		


		return View::make('crafting.list')
			->with(array(
				'recipes' => $recipes,
				'reagent_list' => $sorted_reagent_list,
				'self_sufficient' => $self_sufficient,
				'misc_items' => $misc_items,
				'include_quests' => $include_quests
			));
	}

	private function _reagents($recipes = array(), $self_sufficient = FALSE, $multiplier = 1, $include_quests = FALSE, $top_level = FALSE)
	{
		static $reagent_list = array();

		foreach ($recipes as $recipe)
		{
			$inner_multiplier = $multiplier;

			// Recipe may be involved in a Guildmaster quest.  They may need to make this multiple times.
			// But only account for the top level recipes
			if ($include_quests == TRUE)
			{
				$run = 0;
				
				if ($recipe->item)
					foreach ($recipe->item->quest as $quest)
						$run += ceil($quest->amount / $recipe->yields);

				// Run everything at least once
				$inner_multiplier *= $run ?: 1;
			} 
			elseif (is_array($top_level))
			{
				$run = 0;

				if (in_array($recipe->item_id, array_keys($top_level)))
					$run += $top_level[$recipe->item_id];

				$inner_multiplier *= floor($run ?: 1);
			}

			if ( ! is_array($top_level))
				$inner_multiplier *= $recipe->yields;

			foreach ($recipe->reagents as $reagent)
			{
				$reagent_yields = isset($reagent->recipe[0]) ? $reagent->recipe[0]->yields : 1;

				if ( ! isset($reagent_list[$reagent->id]))
					$reagent_list[$reagent->id] = array(
						'make_this_many' => 0,
						'self_sufficient' => '',
						'item' => $reagent,
						'cluster_jobs' => array(),
						'yields' => 1
					);

				$make_this_many = ceil($reagent->pivot->amount * $inner_multiplier); // ceil($reagent->pivot->amount * ceil($inner_multiplier / $reagent_yields))
				$reagent_list[$reagent->id]['make_this_many'] += $make_this_many;

				if ($self_sufficient)
				{
					if (count($reagent->clusters))
					{
						// First, check here because we don't want to re-process the node data
						if ($reagent_list[$reagent->id]['self_sufficient'])
							continue;

						// Compile cluster jobs
						$cluster_jobs = array();
						foreach ($reagent->clusters as $cluster)
							@$cluster_jobs[$cluster->classjob->abbr->term]++;

						// Get the "highest" job
						asort($cluster_jobs);

						$reagent_list[$reagent->id]['self_sufficient'] = array_keys($cluster_jobs)[count($cluster_jobs) - 1];
						$reagent_list[$reagent->id]['cluster_jobs'] = $cluster_jobs;

						// Then check again here to avoid recipe stuff
						if ($reagent_list[$reagent->id]['self_sufficient'])
							continue;
					}

					if(isset($reagent->recipe[0]))
					{
						$reagent_list[$reagent->id]['yields'] = $reagent->recipe[0]->yields;
						$reagent_list[$reagent->id]['self_sufficient'] = $reagent->recipe[0]->classjob->abbr->term;
						$this->_reagents(array($reagent->recipe[0]), $self_sufficient, ceil($reagent->pivot->amount * ceil($inner_multiplier / $reagent_yields)));
					}
				}
			}
		}

		return $reagent_list;
	}

	public function postList()
	{
		return $this->getList();
	}

}
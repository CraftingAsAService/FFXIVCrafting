<?php

class CraftingController extends BaseController 
{

	public function getIndex()
	{
		// All Jobs
		$job_list = array();
		foreach (Job::all() as $j)
			$job_list[$j->abbreviation] = $j->name;

		return View::make('crafting')
			->with('error', FALSE)
			->with('active', 'crafting')
			->with('job_list', $job_list);
	}

	public function postIndex()
	{
		$vars = array('class' => 'CRP', 'start' => 1, 'end' => 5, 'self_sufficient' => 0);
		$values = array();
		foreach ($vars as $var => $default)
			$values[] = Input::has($var) ? Input::get($var) : $default;

		// Overwrite Class var
		if (Input::has('multi') && Input::has('classes'))
			$values[0] = implode(',', Input::get('classes'));
		
		return Redirect::to('/crafting/list?' . implode(':', $values));
	}

	public function getList($item_ids = array(), $self_sufficient = 1)
	{
		View::share('active', 'crafting');

		// All Jobs
		$job_list = array();
		foreach (Job::all() as $j)
			$job_list[$j->abbreviation] = $j->name;

		$include_quests = TRUE;

		View::share('job_list', $job_list);

		if ($item_ids) 
		{
			$include_quests = FALSE;
			View::share('item_ids', $item_ids);
		}
		else
		{
			// Get Options
			$options = explode(':', array_keys(Input::all())[0]);

			// Parse Options              // Defaults
			$desired_job     = isset($options[0]) ? $options[0] : 'CRP';
			$start           = isset($options[1]) ? $options[1] : 1;
			$end             = isset($options[2]) ? $options[2] : 5;
			$self_sufficient = isset($options[3]) ? $options[3] : 1;

			// Jobs are capital
			$desired_job = strtoupper($desired_job);

			// Make sure it's a real job, jobs might be multiple
			$job = Job::whereIn('abbreviation', explode(',', $desired_job))->get();

			// If the job isn't real, error out
			if ( ! isset($job[0]))
			{
				// All Jobs
				$job_list = $job_ids = array();
				foreach (Job::all() as $j)
				{
					$job_ids[$j->abbreviation] = $j->id;
					$job_list[$j->abbreviation] = $j->name;
				}

				// Check for DOL quests
				$quests = array();
				foreach (array('MIN','BTN','FSH') as $job)
					$quests[$job] = QuestItem::where('job_id', $job_ids[$job])
						->orderBy('level')
						->with('item')
						->get();

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
			$quest_items = QuestItem::with('job')
				->whereBetween('level', array($start, $end))
				->whereIn('job_id', $job_ids)
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

		$query = Recipe::with(array(
				'item', // The recipe's Item
					'item.quest', // Is the recipe used as a quest turnin?
				'reagents', // The reagents for the recipe
					'reagents.jobs' => function($query) {
						// Only Land Disciples
						$query->where('disciple', 'DOL');
					},
					'reagents.recipes', 
						'reagents.recipes.item', 
						'reagents.recipes.job' => function($query) {
							// Only Hand Disciples
							$query->where('disciple', 'DOH');
						},
			))
			->select('recipes.*', 'j.abbreviation')
			->join('jobs AS j', 'j.id', '=', 'recipes.job_id')
			->groupBy('recipes.item_id')
			->orderBy('level');

		if ($item_ids)
			$query
				->whereIn('recipes.item_id', $item_ids);
		else
			$query
				->whereIn('j.id', $job_ids)
				->whereBetween('level', array($start, $end));

		$recipes = $query->get();

		$reagent_list = $this->_reagents($recipes, $self_sufficient, 1, $include_quests);

		// Look through the list.  Is there something we're already crafting?
		// Subtract what's being made from needed reagents.
		//  Example, culinary 11 to 15, you need olive oil for Parsnip Salad (lvl 13)
		//   But you make 3 olive oil at level 11.  We don't want them crafting another olive oil.

		foreach ($recipes as $recipe)
		{
			if ( ! isset($reagent_list[$recipe->item_id]))
				continue;

			$reagent_list[$recipe->item_id]['make_this_many'] -= $recipe->yields;

			if ($reagent_list[$recipe->item_id]['make_this_many'] <= 0)
				unset($reagent_list[$recipe->item_id]);
		}

		// Let's sort them further, group them by..
		// Gathered, Then by Level
		// Other (likely mob drops)
		// Crafted, Then by level
		// Bought, by price

		$sorted_reagent_list = array(
			'Gathered' => array(),
			'Bought' => array(),
			'Other' => array(),
			'Crafted' => array(),
		);

		foreach ($reagent_list as $reagent)
		{
			$section = 'Other';
			$level = 0;

			if (in_array($reagent['self_sufficient'], array('MIN', 'BTN', 'FSH')))
			{
				$section = 'Gathered';
				$level = $reagent['item']->level;
			}
			elseif ($reagent['self_sufficient'])
			{
				$section = 'Crafted';
				$level = $reagent['item']->recipes[0]->level;
			}
			elseif ($reagent['item']->gil)
			{
				$section = 'Bought';
				$level = $reagent['item']->gil;
			}

			if ( ! isset($sorted_reagent_list[$section][$level]))
				$sorted_reagent_list[$section][$level] = array();

			$sorted_reagent_list[$section][$level][] = $reagent;
		}

		foreach ($sorted_reagent_list as $section => $list)
			ksort($sorted_reagent_list[$section]);

		// Was this their first time?
		$first_time = TRUE;

		if (Session::has('crafting_first_time'))
			$first_time = FALSE;
		else
			Session::put('crafting_first_time', TRUE);

		return View::make('crafting.list')
			->with(array(
				'recipes' => $recipes,
				'reagent_list' => $sorted_reagent_list,
				'first_time' => $first_time,
				'self_sufficient' => $self_sufficient,
				'include_quests' => $include_quests
			));
	}

	private function _reagents($recipes = array(), $self_sufficient = FALSE, $multiplier = 1, $include_quests = FALSE)
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
				foreach ($recipe->item->quest as $quest)
					$run += ceil($quest->amount / $recipe->yields);

				// Run everything at least once
				$inner_multiplier *= $run ?: 1;
			}

			foreach ($recipe->reagents as $reagent)
			{
				if ( ! isset($reagent_list[$reagent->id]))
					$reagent_list[$reagent->id] = array(
						'make_this_many' => 0,
						'self_sufficient' => '',
						'item' => $reagent,
					);

				$reagent_list[$reagent->id]['make_this_many'] += $reagent->pivot->amount * $inner_multiplier;

				if ($self_sufficient)
				{
					if (isset($reagent->jobs[0]))
					{
						// Prevent non DOH/DOL jobs from showing up
						for ($i = count($reagent->jobs) - 1; $i < 0; $i--)
						{
							$job = $reagent->jobs[$i];
							if ( ! in_array($job->disciple, array('DOH', 'DOL'))) 
								continue;

							$reagent_list[$reagent->id]['self_sufficient'] = $job->abbreviation;
						}
					}
					elseif(isset($reagent->recipes[0]))
					{
						$reagent_list[$reagent->id]['self_sufficient'] = $reagent->recipes[0]->job->abbreviation;
						$this->_reagents($reagent->recipes, $self_sufficient, $reagent->pivot->amount * $inner_multiplier);
					}
				}
			}
		}

		return $reagent_list;
	}

	public function getGear()
	{
		// Get Item IDs
		$item_ids = explode(':', array_keys(Input::all())[0]);
		$self_sufficient = count($item_ids) > 1 ? array_pop($item_ids) : 1;

		return $this->getList($item_ids, $self_sufficient);
	}

	public function postGear()
	{
		$item_ids = explode(':', Input::get('ids'));
		$self_sufficient = Input::get('self_sufficient');

		return $this->getList($item_ids, $self_sufficient);
	}

}
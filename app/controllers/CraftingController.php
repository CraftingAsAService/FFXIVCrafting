<?php

class CraftingController extends BaseController 
{

	public function getIndex()
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
			->with('error', FALSE)
			->with('active', 'crafting')
			->with('job_list', $job_list)
			->with('quests', $quests);
	}

	public function postIndex()
	{
		$vars = array('class' => 'CRP', 'start' => 1, 'end' => 5, 'self_sufficient' => 0);
		$values = array();
		foreach ($vars as $var => $default)
			$values[] = Input::has($var) ? Input::get($var) : $default;
		
		return Redirect::to('/crafting/list?' . implode(':', $values));
	}

	public function getList()
	{
		// Get Options
		$options = explode(':', array_keys(Input::all())[0]);

		// Parse Options              // Defaults
		$desired_job     = isset($options[0]) ? $options[0] : 'CRP';
		$start           = isset($options[1]) ? $options[1] : 1;
		$end             = isset($options[2]) ? $options[2] : 5;
		$self_sufficient = isset($options[3]) ? $options[3] : 1;

		View::share('active', 'crafting');

		// All Jobs
		$job_list = array();
		foreach (Job::all() as $j)
			$job_list[$j->abbreviation] = $j->name;

		View::share('job_list', $job_list);

		// Jobs are capital
		$desired_job = strtoupper($desired_job);

		// Make sure it's a real job
		$job = Job::where('abbreviation', $desired_job)->first();

		// If the job isn't real, error out
		if ( ! $job)
			return View::make('crafting')
				->with('error', TRUE);

		// Starting maximum of 1
		if ($start < 0) $start = 1;
		if ($start > $end) $end = $start;
		if ($end - $start > 9) $end = $start + 9;

		// Gather Recipes and Reagents

		$recipes = Recipe::with(array(
				'item', // The recipe's Item
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
			->select('recipes.*')
			->join('jobs AS j', 'j.id', '=', 'recipes.job_id')
			->where('j.abbreviation', $desired_job)
			->whereBetween('level', array($start, $end))
			->orderBy('level')
			->get();

		$reagent_list = array();
		
		$reagent_list = $this->_reagents($recipes, $self_sufficient);

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

		// Check for quests
		$quest_items = QuestItem::whereBetween('level', array($start, $end))
			->where('job_id', $job->id)
			->orderBy('level')
			->with('item')
			->get();

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
				'quest_items' => $quest_items,
				'job' => $job,
				'start' => $start,
				'end' => $end,
				'self_sufficient' => $self_sufficient,
				'first_time' => $first_time,
			));
	}

	private function _reagents($recipes = array(), $self_sufficient = FALSE, $multiplier = 1)
	{
		static $reagent_list = array();

		foreach ($recipes as $recipe)
			foreach ($recipe->reagents as $reagent)
			{
				if ( ! isset($reagent_list[$reagent->id]))
					$reagent_list[$reagent->id] = array(
						'make_this_many' => 0,
						'self_sufficient' => '',
						'item' => $reagent,
					);

				$reagent_list[$reagent->id]['make_this_many'] += $reagent->pivot->amount * $multiplier;

				if ($self_sufficient)
				{
					if (isset($reagent->jobs[0]))
					{
						$reagent_list[$reagent->id]['self_sufficient'] = $reagent->jobs[0]->abbreviation;
					}
					elseif(isset($reagent->recipes[0]))
					{
						$reagent_list[$reagent->id]['self_sufficient'] = $reagent->recipes[0]->job->abbreviation;
						$this->_reagents($reagent->recipes, $self_sufficient, $reagent->pivot->amount);
					}
				}
			}

		return $reagent_list;
	}

}
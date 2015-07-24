<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Config;
use Cookie;
use Session;

use App\Models\Garland\Job;
use App\Models\Garland\Quest;
use App\Models\Garland\Recipe;

class CraftingController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		view()->share('active', 'crafting');
	}

	public function getIndex($advanced = false)
	{
		$crafting_job_ids = Config::get('site.job_ids.crafting');
		$error = false;
		$job_list = Job::whereIn('id', $crafting_job_ids)->get();
		$previous = Cookie::get('previous_crafting_load');

		return view('crafting.' . ($advanced ? 'advanced' : 'basic'), compact('error', 'job_list', 'crafting_job_ids', 'previous'));
	}

	public function getAdvanced()
	{
		return $this->getIndex(true);
	}

	public function postIndex(Request $request)
	{
		$inputs = $request->all();

		$vars = ['class' => 'CRP', 'start' => 1, 'end' => 5, 'self_sufficient' => 0, 'misc_items' => 0, 'component_items' => 0];
		$values = [];
		foreach ($vars as $var => $default)
			$values[] = isset($inputs[$var]) ? $inputs[$var] : $default;

		// Overwrite Class var
		if (isset($inputs['multi']) && isset($inputs['classes']))
			$values[0] = implode(',', $inputs['classes']);

		$url = '/crafting/list?' . implode(':', $values);

		// Queueing the cookie, we won't need it right away, so it'll save for the next Response::
		Cookie::queue('previous_crafting_load', $url, 525600); // 1 year's worth of minutes
		
		return redirect($url);
	}

	public function getList()
	{
		// All Jobs
		$job_list = Job::lists('name', 'abbr');
		view()->share('job_list', $job_list);

		$include_quests = TRUE;

		if ( ! \Request::all())
			return redirect()->back();

		// Get Options
		$options = explode(':', array_keys(\Request::except('_token'))[0]);

		// Parse Options              						// Defaults
		$desired_job     = isset($options[0]) ? $options[0] : 'CRP';
		$start           = isset($options[1]) ? $options[1] : 1;
		$end             = isset($options[2]) ? $options[2] : 5;
		$self_sufficient = isset($options[3]) ? $options[3] : 1;
		$misc_items	 	 = isset($options[4]) ? $options[4] : 0;
		$component_items = isset($options[5]) ? $options[5] : 0;
		$special	 	 = isset($options[6]) ? $options[6] : 0;

		$item_ids = $item_amounts = [];

		$top_level = TRUE;

		if ($desired_job == 'List')
		{
			$start = $end = null;
			$include_quests = FALSE;

			// Get the list
			$item_amounts = Session::get('list', []);

			$item_ids = array_keys($item_amounts);

			if (empty($item_ids))
				return redirect('/list');

			view()->share(compact('item_ids', 'item_amounts'));

			$top_level = $item_amounts;
		}

		if ($desired_job == 'Item')
		{
			$item_id = $special;

			$start = $end = null;
			$include_quests = FALSE;

			// Get the list
			$item_amounts = array($item_id => 1);

			$item_ids = array_keys($item_amounts);

			if (empty($item_ids))
				return redirect('/list');

			$item_special = true;

			view()->share(compact('item_ids', 'item_amounts', 'item_special'));

			$top_level = $item_amounts;
		}

		if ( ! $item_ids)
		{
			// Jobs are capital
			$desired_job = strtoupper($desired_job);

			// Make sure it's a real job, jobs might be multiple
			$job = [];
			foreach (explode(',', $desired_job) as $ds)
				$job[] = Job::with('categories')->where('abbr', $ds)->first();

			// If the job isn't real, error out
			if ( ! isset($job[0]))
			{
				// Check for DOL quests
				$quests = [];
				foreach (array('MIN','BTN','FSH') as $job)
				{
					$job = Job::with('categories')->where('abbr', $job)->first();
					$jc_ids = $job->categories->lists('id');
					$quests[$job] = Quest::whereIn('job_category_id', $jc_ids)
						->orderBy('level')
						->with('item')
						->get();
				}

				$error = true;

				return view('crafting', compact('error', 'quests'));
			}

			$job_ids = [];
			$jc_ids = [];
			foreach ($job as $j)
			{
				$job_ids[] = $j->id;
				$ids = $j->categories->lists('id');
				$jc_ids = array_merge($ids, $jc_ids);
			}
			array_unique($jc_ids);

			$full_name_desired_job = false;
			if (count($job) == 1)
			{
				$job = $job[0];
				$full_name_desired_job = $job->name;
			}

			// Starting maximum of 1
			if ($start < 0) $start = 1;
			if ($start > $end) $end = $start;
			if ($end - $start > 9) $end = $start + 9;

			// Check for quests
			// We're only looking for proper crafting quests, so take out anything that's not between 9 and 16 (CRP to CUL)
			$jc_ids = array_intersect($jc_ids, range(9,16));
			$quest_items = Quest::with('job_category', 'requirements')
				->whereBetween('level', [$start, $end])
				->whereIn('job_category_id', $jc_ids)
				->orderBy('level')
				->get();

			view()->share(compact('job', 'start', 'end', 'quest_items', 'desired_job', 'full_name_desired_job'));
		}

		// Gather Recipes and Reagents

		$query = Recipe::with(
				'job',
				'item', // The recipe's Item
					'item.quest_rewards', // Is the recipe used as a quest turnin?
					'item.leves', // Is the recipe used to fufil a leve?
					'item.shops',
					'item.mobs',
					'item.nodes',
				'reagents', // The reagents for the recipe
					'reagents.shops',
					'reagents.mobs',
					'reagents.nodes',
					'reagents.recipes',
						'reagents.recipes.item', 
							'reagents.recipes.item.shops',
							'reagents.recipes.item.mobs',
							'reagents.recipes.item.nodes',
						'reagents.recipes.job'
			)
			->groupBy('item_id')
			->orderBy('recipe_level')
			// ->orderBy('rank')
			;

		if ($item_ids)
			$query
				->whereIn('item_id', $item_ids);
		else
		{
			$query
				->whereIn('job_id', $job_ids)
				->whereBetween('recipe_level', [$start, $end]);
		}

		$recipes = $query
			->get();

		// Do we not want miscellaneous items?
		if ($misc_items == 0 && $desired_job != 'List')
			foreach ($recipes as $key => $recipe)
			{
				if (in_array($recipe->item->item_category_id, 
					// This is any Furniture, Dyes, Other, Miscellany, Airship parts, etc etc
					array_merge(range(55,83), range(85,86), range(90,93))
				))
					unset($recipes[$key]);
			}

		// Fix the amount of the top level to be evenly divisible by the amount the recipe yield
		if (is_array($top_level))
		{
			foreach ($recipes as $recipe)
			{
				$tl_item =& $top_level[$recipe->item_id];

				// If they're not evently divisible
				if ($tl_item % $recipe->yield != 0)
					// Make it so
					$tl_item = ceil($tl_item / $recipe->yield) * $recipe->yield;
			}
			unset($tl_item);

			view()->share('item_amounts', $top_level);
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
			if ($reagent['make_this_many'] % $reagent['yield'] != 0)
				// Make it so
				$reagent['make_this_many'] = ceil($reagent['make_this_many'] / $reagent['yield']) * $reagent['yield'];
		unset($reagent);

		// Let's sort them further, group them by..
		// Gathered, Then by Level
		// Other (likely mob drops)
		// Crafted, Then by level
		// Bought, by price

		$sorted_reagent_list = [
			'Gathered' => [],
			'Bought' => [],
			'Other' => [],
			'Pre-Requisite Crafting' => [],
			'Crafting List' => [],
		];

		$gathering_class_abbreviations = Job::whereIn('id', Config::get('site.job_ids.gathering'))->lists('abbr');

		foreach ($reagent_list as $reagent)
		{
			$section = 'Other';
			$level = 0;
			
			// Section
			if (in_array($reagent['self_sufficient'], $gathering_class_abbreviations))
			{
				$section = 'Gathered';
				$level = $reagent['item']->level;
			}
			elseif ($reagent['self_sufficient'])
			{
				$section = 'Pre-Requisite Crafting';
				if ( ! isset($reagent['item']->recipe[0]))
				{
					dd($reagent['item']);
				}
				$level = $reagent['item']->recipe[0]->level;
			}
			elseif (count($reagent['item']->vendors))
			{
				$section = 'Bought';
				$level = $reagent['item']->min_price;
			}

			if ( ! isset($sorted_reagent_list[$section][$level]))
				$sorted_reagent_list[$section][$level] = [];

			$sorted_reagent_list[$section][$level][$reagent['item']->id] = $reagent;
			ksort($sorted_reagent_list[$section][$level]);
		}

		foreach ($sorted_reagent_list as $section => $list)
			ksort($sorted_reagent_list[$section]);

		// Sort the pre-requisite crafting by rank
		// We don't need to sort them by level, just make sure it's in the proper structure
		// The keys don't matter either
		$prc =& $sorted_reagent_list['Pre-Requisite Crafting'];
		
		$new_prc = array('1' => []);
		foreach ($prc as $vals)
			foreach ($vals as $v)
				$new_prc['1'][] = $v;

		// Sort them by rank first
		// TODO re-enable rank?  Currently no data
		// usort($new_prc['1'], function($a, $b) { 
		// 	return $a['item']->rank - $b['item']->rank; 
		// });
		// Then by classjob
		usort($new_prc['1'], function($a, $b) {
			return $a['item']->recipe[0]->job->id - $b['item']->recipe[0]->job->id; 
		});
		
		$prc = $new_prc;

		$reagent_list = $sorted_reagent_list;

		return view('crafting.list', compact('recipes', 'reagent_list', 'self_sufficient', 'misc_items', 'component_items', 'include_quests'));
	}

	private function _reagents($recipes = [], $self_sufficient = FALSE, $multiplier = 1, $include_quests = FALSE, $top_level = FALSE)
	{
		static $reagent_list = [];

		foreach ($recipes as $recipe)
		{
			$inner_multiplier = $multiplier;

			// Recipe may be involved in a Guildmaster quest.  They may need to make this multiple times.
			// But only account for the top level recipes
			// FIXME no data on amounts required for the quest requirements :(
			// if ($include_quests == TRUE)
			// {
			// 	$run = 0;
				
			// 	if ($recipe->item)
			// 		foreach ($recipe->item->quest_requirements as $quest)
			// 			$run += ceil($quest->pivot->amount / $recipe->yield);

			// 	// Run everything at least once
			// 	$inner_multiplier *= $run ?: 1;
			// } 
			// else
				if (is_array($top_level))
			{
				$run = 0;

				if (in_array($recipe->item_id, array_keys($top_level)))
					$run += $top_level[$recipe->item_id];

				$inner_multiplier *= floor($run ?: 1);
			}

			if ( ! is_array($top_level))
				$inner_multiplier *= $recipe->yield;

			foreach ($recipe->reagents as $reagent)
			{
				$reagent_yield = isset($reagent->recipe[0]) ? $reagent->recipe[0]->yield : 1;

				if ( ! isset($reagent_list[$reagent->id]))
					$reagent_list[$reagent->id] = array(
						'make_this_many' => 0,
						'self_sufficient' => '',
						'item' => $reagent,
						'cluster_jobs' => [],
						'yield' => 1
					);

				$make_this_many = ceil($reagent->pivot->amount * $inner_multiplier); // ceil($reagent->pivot->amount * ceil($inner_multiplier / $reagent_yield))
				$reagent_list[$reagent->id]['make_this_many'] += $make_this_many;

				if ($self_sufficient)
				{
					if (count($reagent->nodes))
					{
						// First, check here because we don't want to re-process the node data
						if ($reagent_list[$reagent->id]['self_sufficient'])
							continue;

						// '16','Miner','MIN'
						// 0 == MIN == Mineral Deposit
						// 1 == MIN == Rocky Outcropping
						// '17','Botanist','BTN'
						// 2 == BTN == Mature Tree
						// 3 == BTN == Lush Vegetation
						
						// Compile cluster jobs
						$cluster_jobs = [];
						foreach ($reagent->nodes as $node)
							@$cluster_jobs[$node->type <= 1 ? 'MIN' : 'BTN']++;

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
						$reagent_list[$reagent->id]['yield'] = $reagent->recipe[0]->yield;
						$reagent_list[$reagent->id]['self_sufficient'] = $reagent->recipe[0]->job->abbr;
						$this->_reagents(array($reagent->recipe[0]), $self_sufficient, ceil($reagent->pivot->amount * ceil($inner_multiplier / $reagent_yield)));
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

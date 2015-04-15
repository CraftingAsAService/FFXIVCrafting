<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Config;
use Cookie;
use Session;

use App\Models\CAAS\ClassJob;
use App\Models\CAAS\QuestItem;
use App\Models\CAAS\Recipes;

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
		$job_list = ClassJob::with('name', 'en_abbr', 'en_name')->whereIn('id', $crafting_job_ids)->get();
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
		$job_list = ClassJob::get_name_abbr_list();
		view()->share('job_list', $job_list);

		$include_quests = TRUE;

		if ( ! \Request::all())
			return redirect()->back();

		// Get Options
		$options = explode(':', array_keys(\Request::all())[0]);

		// Parse Options              						// Defaults
		$desired_job     = isset($options[0]) ? $options[0] : 'CRP';
		$start           = isset($options[1]) ? $options[1] : 1;
		$end             = isset($options[2]) ? $options[2] : 5;
		$self_sufficient = isset($options[3]) ? $options[3] : 1;
		$misc_items	 	 = isset($options[4]) ? $options[4] : 0;
		$component_items = isset($options[5]) ? $options[5] : 0;
		$special	 	 = isset($options[6]) ? $options[6] : 0;

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

				$error = true;

				return view('crafting', compact('error', 'quests'));
			}

			$job_ids = array();
			foreach ($job as $j)
				$job_ids[] = $j->id;

			$full_name_desired_job = false;
			if (count($job) == 1)
			{
				$job = $job[0];
				$full_name_desired_job = $job->name->term;
			}

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

			view()->share(compact('job', 'start', 'end', 'quest_items', 'desired_job', 'full_name_desired_job'));
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

		if ($item_ids)
			$query
				->whereIn('recipes.item_id', $item_ids);
		else
		{
			$query
				->whereHas('classjob', function($query) use ($job_ids) {
					$query->whereIn('classjob.id', $job_ids);
				})
				->whereBetween('level', array($start, $end));
		}

		$recipes = $query
			// ->remember(Config::get('site.cache_length'))
			->get();

		// Do we not want miscellaneous items?
		if ($misc_items == 0 && $desired_job != 'List')
			foreach ($recipes as $key => $recipe)
			{
				// Remove any Furnishings, Dyes and "Other"
				// ItemCategory 14 == 'Furnishing', 15 == 'Dye', 16 == 'Miscellany'
				// Miscellany is special, we want to keep any Miscellany if it's in a range of UI
				// ItemUICategory 12 to 32 are Tools (Primary/Secondary), keep those
				// Technically this only solves a Secondary tool issue.  Primary tools aren't part of 16/Miscellany
				if (
					in_array($recipe->item->itemcategory_id, [14, 15]) ||
					(
						$recipe->item->itemcategory_id == 16 && 
						! in_array($recipe->item->itemuicategory_id, range(12, 32))
					)
				)
					unset($recipes[$key]);
			}

		// Do we not want component items?
		if ($component_items == 0 && $desired_job != 'List')
			foreach ($recipes as $key => $recipe)
			{
				// Remove any Miscellany
				// ItemUICategory of 63, as that's "Other"
				if ($recipe->item->itemuicategory_id == 63)
					unset($recipes[$key]);
			}

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
			if ($reagent['make_this_many'] % $reagent['yields'] != 0)
				// Make it so
				$reagent['make_this_many'] = ceil($reagent['make_this_many'] / $reagent['yields']) * $reagent['yields'];
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

		$gathering_class_abbreviations = ClassJob::get_abbr_list(Config::get('site.job_ids.gathering'));

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

		$reagent_list = $sorted_reagent_list;

		return view('crafting.list', compact('recipes', 'reagent_list', 'self_sufficient', 'misc_items', 'component_items', 'include_quests'));
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

<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Config;
use Cookie;
use Session;

use App\Models\Garland\Item;
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
		$previous = session('previous-crafting-load');

		return view('crafting.index', compact('error', 'job_list', 'crafting_job_ids', 'previous'));
	}

	public function getAdvanced()
	{
		flash('The advanced page and the basic page are now one single page!')->info();
		return redirect('/crafting');
	}

	public function getList()
	{
		// TODO, outdated page link, warn, redirect
	}

	public function postIndex(Request $request)
	{
		$classes = $request->input('classes', ['CRP']);
		$start = $request->input('start', 1);
		$end = $request->input('end', 1);
		$options = $request->except('_token', 'classes', 'start', 'end');

		$url = '/crafting/by-class/' . implode(',', $classes) . '/' . $start . '/' . $end . '?' . http_build_query($options);

		session()->put('previous-crafting-load', $url);

		return redirect($url);
	}

	public function getByClass(Request $request, $classes = '', $start = '', $end = '')
	{
		if (empty($classes) || empty($start) || empty($end))
		{
			flash('Something isn\'t set right!  How am I supposed to show you any results?')->error();
			return redirect()->back();
		}

		$options = $request->all();

		if (isset($options['inclusions']) && $options['inclusions'] == '')
			unset($options['inclusions']);

		// Fix the level numbers if needed
		if ($start < 0) $start = 1; // Starting maximum of 1
		if ($start > $end) $end = $start; // End can't be less than Start

		// Jobs are capital
		$classes = explode(',', strtoupper($classes));

		// Make sure it's a real job, jobs might be multiple
		$jobs = Job::with('categories')->whereIn('abbr', $classes)->get();

		// If the job isn't real, error out
		if (count($jobs) == 0)
		{
			flash('No valid classes!  How am I supposed to show you any results?')->error();
			return redirect()->back();
		}

		$job_ids = $jobs->pluck('id')->all();

		// Check for quests
		// We're only looking for proper crafting quests, so take out anything that's not between 9 and 16 (CRP to CUL)

		$jc_ids = [];
		foreach ($jobs as $job)
			$jc_ids = array_merge($job->categories->pluck('id')->all(), $jc_ids);
		array_unique($jc_ids);
		$jc_ids = array_intersect($jc_ids, range(9,16));

		$quest_items = Quest::with('job_category', 'requirements')
			->whereBetween('level', [$start, $end])
			->whereIn('job_category_id', $jc_ids)
			->orderBy('level')
			->get();

		view()->share(compact('jobs', 'classes', 'start', 'end', 'quest_items'));

		$include_quests = $top_level = true;

		// This is any Furniture, Dyes, Other, Miscellany, Airship parts, etc etc
		$bad_item_category_ids = array_merge(range(55,83), range(85,86), range(90,93));

		// But we want to keep the specified $inclusions
		if (isset($options['inclusions']))
			$bad_item_category_ids = array_diff($bad_item_category_ids, explode(',', $options['inclusions']));

		return $this->listing(compact(
			'start', 'end', 'options', 'bad_item_category_ids',
			'jobs', 'quest_items', 'job_ids',
			'include_quests', 'top_level'
		));
	}

	public function getItem(Request $request, $item_id)
	{
		if (empty($item_id))
		{
			flash('No item id!  How am I supposed to show you any results?')->error();
			return redirect()->back();
		}

		$item = Item::find($item_id);

		if (is_null($item))
		{
			flash('That item doesn\'t exist!  How am I supposed to show you any results?')->error();
			return redirect()->back();
		}

		$options = $request->all();

		// Get the list
		$item_ids = [$item_id];
		$item_amounts = [$item_id => 1];

		view()->share(compact('item_ids', 'item_amounts', 'item'));

		$start = $end = null;
		$include_quests = false;
		$top_level = $item_amounts;

		return $this->listing(compact(
			'item_ids', 'item_amounts',
			'start', 'end', 'options',
			'include_quests', 'top_level'
		));
	}

	public function getFromList(Request $request)
	{
		// Get the list
		$item_amounts = session('list', []);
		$item_ids = array_keys($item_amounts);

		if (empty($item_ids))
		{
			flash('There\'s nothing in your list!  How am I supposed to show you any results?')->error();
			return redirect('/list');
		}

		$options = $request->all();

		view()->share(compact('item_ids', 'item_amounts'));

		$start = $end = null;
		$include_quests = false;
		$top_level = $item_amounts;

		return $this->listing(compact(
			'item_ids', 'item_amounts',
			'start', 'end', 'options',
			'include_quests', 'top_level'
		));
	}

	private function listing($configuration = [])
	{
		extract($configuration); // $item_ids, etc, now all exist individually
		unset($configuration);

		// All Jobs
		$job_list = Job::pluck('name', 'abbr')->all();

		// Gather Recipes and Reagents
		$query = Recipe::with(
				'job',
				'item', // The recipe's Item
					'item.category',
					'item.quest_rewards', // Is the recipe used as a quest turnin?
					'item.leve_required', // Is the recipe used to fufil a leve?
					'item.shops',
					// 'item.shops.location',
					'item.mobs',
					'item.nodes',
				'reagents', // The reagents for the recipe
					'reagents.category',
					'reagents.shops',
					'reagents.mobs',
					'reagents.nodes',
						'reagents.nodes.zone',
						'reagents.nodes.area',
					'reagents.recipes',
						'reagents.recipes.item',
							'reagents.recipes.item.category',
							'reagents.recipes.item.shops',
							'reagents.recipes.item.mobs',
							'reagents.recipes.item.nodes',
						'reagents.recipes.job'
			)
			->groupBy('item_id')
			->orderBy('recipe_level')
			->orderBy('id')
			// ->orderBy('rank')
			;

		if (isset($item_ids))
			$query->whereIn('item_id', $item_ids);
		else
			$query
				->whereIn('job_id', $job_ids)
				->whereBetween('recipe_level', [$start, $end]);

		$recipes = $query->get();

		if (isset($bad_item_category_ids))
			$recipes = $recipes->filter(function($recipe) use ($bad_item_category_ids) {
				return ! in_array($recipe->item->item_category_id, $bad_item_category_ids);
			});

		// Fix the amount of the top level to be evenly divisible by the amount the recipe yield
		if (isset($top_level) && is_array($top_level))
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

			// Overwrite the already shared item_amounts
			view()->share('item_amounts', $top_level);
		}

		$self_sufficient = isset($options) && isset($options['self_sufficient']);

		$reagent_list = $this->_reagents($recipes, $self_sufficient, 1, $include_quests, $top_level);

		// Look through the list.  Is there something we're already crafting?
		// Subtract what's being made from needed reagents.
		//  Example, culinary 11 to 15, you need olive oil for Parsnip Salad (lvl 13)
		//   But you make 3 olive oil at level 11.  We don't want them crafting another olive oil.

		foreach ($recipes as $recipe)
		{
			if ( ! isset($reagent_list[$recipe->item_id]))
				continue;

			$reagent_list[$recipe->item_id]['both_list_warning'] = true;
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
			'Gathered' => [
				'' => [],
			],
			'Bought' => [],
			'Other' => [],
			'Pre-Requisite Crafting' => [],
			'Crafting List' => [],
		];

		$gathering_class_abbreviations = Job::whereIn('id', Config::get('site.job_ids.gathering'))->pluck('abbr')->all();

		foreach ($reagent_list as $reagent)
		{
			$section = 'Other';
			$level = 0;

			// Section
			if (in_array($reagent['self_sufficient'], $gathering_class_abbreviations))
			{
				$section = 'Gathered';
				if ($reagent['item']->category->name == 'Crystal')
					$level = '';
				else
				{
					$zoneName = $reagent['item']->nodes->first()->zone->name ?? '';
					$areaName = $reagent['item']->nodes->first()->area->name ?? '';
					$level = $zoneName . ($areaName ? ' - ' . $areaName : '');
				}
			}
			elseif ($reagent['self_sufficient'])
			{
				$section = 'Pre-Requisite Crafting';
				if ( ! isset($reagent['item']->recipes[0]))
				{
					exit('Crafting Error');
					// dd($reagent['item']);
				}
				$level = $reagent['item']->recipes[0]->level;
			}
			elseif ($reagent['item']->vendors)
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

		$new_prc = ['1' => []];
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
			return $a['item']->recipes[0]->job->id - $b['item']->recipes[0]->job->id;
		});

		$prc = $new_prc;

		$reagent_list = $sorted_reagent_list;

		return view('crafting.list', compact(
			'recipes', 'reagent_list', 'job_list', 'options',
			'self_sufficient', 'misc_items', 'component_items', 'include_quests'
		));
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
				if ( ! isset($reagent->category))
					$reagent->load('category');

				$reagent_yield = isset($reagent->recipes[0]) ? $reagent->recipes[0]->yield : 1;

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
					if ( ! $reagent->nodes->isEmpty())
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

					if( ! $reagent->recipes->isEmpty() > 0)
					{
						$reagent_list[$reagent->id]['yield'] = $reagent->recipes[0]->yield;
						$reagent_list[$reagent->id]['self_sufficient'] = $reagent->recipes[0]->job->abbr;
						$this->_reagents(array($reagent->recipes[0]), $self_sufficient, ceil($reagent->pivot->amount * ceil($inner_multiplier / $reagent_yield)));
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

<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Cache;
use Config;

use App\Models\CAAS\Leve;
use App\Models\CAAS\LeveReward;
use App\Models\CAAS\ClassJob;
use App\Models\CAAS\Experience;

class LevequestsController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		view()->share('active', 'levequests');
	}

	public function getIndex()
	{
		$crafting_job_ids = Config::get('site.job_ids.crafting');
		$crafting_job_ids[] = Config::get('site.job_ids.fishing');

		$type_to_icon = [
			'Field' => 'leaf',
			'Courier' => 'envelope',
			'Reverse Courier' => 'plane',
			'Town' => 'home',
		];

		// All Leves
		$all_leves = Cache::remember('leves_' . Config::get('language'), 60, function() {
			return Leve::with(['classjob', 'classjob.en_abbr', 'item', 'item.name', 'item.recipe', 'item.vendors'])
				->where('item_id', '>', 0) // Avoids mining/botany "bug"
				->orderBy('classjob_id')
				->orderBy('level')
				->orderBy('triple', 'desc')
				->orderBy('xp', 'desc')
				->orderBy('gil', 'desc')
				->get();
		});

		$leves = [];
		foreach ($all_leves as $leve)
			$leves[$leve->classjob->en_abbr->term][$leve->level][] = $leve;

		$rewards = Cache::remember('rewards_' . Config::get('language'), 60, function() {
			$leve_rewards = LeveReward::with('item')
				->orderBy('classjob_id')
				->orderBy('level')
				->orderBy('item_id')
				->orderBy('amount')
				->get();

			$rewards = [];
			foreach($leve_rewards as $reward)
				if ($reward->item_id)
				{
					$rewards[$reward->classjob_id][$reward->level][$reward->item_id]['item'] = $reward->item;
					$rewards[$reward->classjob_id][$reward->level][$reward->item_id]['amounts'][] = $reward->amount;
				}

			return $rewards;
		});
		
		$crafting_job_list = ClassJob::with('name', 'en_name', 'en_abbr')->whereIn('id', $crafting_job_ids)->get();
		$opening_level = 1;
		$opening_class = 'CRP';

		return view('levequests.index', compact('crafting_job_list', 'crafting_job_ids', 'leves', 'rewards', 'type_to_icon', 'opening_level', 'opening_class'));
	}

	public function getBreakdown($leve_id = 1)
	{
		foreach ($this->_breakdown($leve_id) as $key => $value)
			$$key = $value;

		// Get other Leve's at this level
		$others = Leve::where('level', $leve->level)
			->where('classjob_id', $leve->classjob_id)
			->where('id', '!=', $leve->id)
			->get();

		return view('levequests.breakdown', compact('leve', 'chart', 'others'));
	}

	private function _breakdown($leve_id = 0)
	{
		$leve = Leve::with('classjob', 'classjob.en_name', 'item', 'item.name', 'item.recipe', 'item.recipe.reagents', 'item.recipe.reagents.name')->find($leve_id);
		$experience = Experience::whereBetween('level', array($leve->level + 1, $leve->level + 10))->get();
		
		$chart = [];
		foreach ($experience as $xp)
		{
			// NQ Turnins
			$amount = $turnins = 0;
			if ($leve->xp > 0)
				while ($amount < $xp->experience)
				{
					$amount += $leve->xp;
					$turnins++;
				}

			// HQ Turnins
			$amount = $hq_turnins = 0;
			if ($leve->xp > 0)
				while ($amount < $xp->experience)
				{
					$amount += $leve->xp * 2; // 2.1 patch changed it from 200% to 100% bonus
					$hq_turnins++;
				}

			$chart[$xp->level] = [
				'level' => $xp->level,
				'requires' => $xp->experience,
				'turnins' => $turnins,
				'hq_turnins' => $hq_turnins,
			];
		}

		return compact('leve', 'chart');
	}

	public function getVs($leveA = 1, $leveB = 1)
	{
		$a = $this->_breakdown($leveA);
		$b = $this->_breakdown($leveB);

		return view('levequests.vs', compact('a', 'b'));
	}

	/**
	 * The advanced leve form.  Most of the hard work is in the ajax request.
	 * @return View
	 */
	public function getAdvanced()
	{
		$crafting_job_ids = Config::get('site.job_ids.crafting');
		$crafting_job_ids[] = Config::get('site.job_ids.fishing');

		$crafting_job_list = ClassJob::with('name', 'en_abbr')->whereIn('id', $crafting_job_ids)->get();

		return view('levequests.advanced', compact('crafting_job_list', 'crafting_job_ids'));
	}

	/**
	 * The advanced form requests data via Ajax here
	 * @param  Request
	 * @return View
	 */
	public function getPopulateAdvanced(Request $request)
	{
		$input = $request->all();
		
		// Parse the Job IDs
		$selected_classes = $input['classes'];
		foreach (ClassJob::get_id_abbr_list(true) as $abbr => $id)
			if (in_array($abbr, $selected_classes))
				$job_ids[] = $id;

		if (empty($job_ids))
			$job_ids[] = 1;

		// All Leves
		$query = Leve::with(array(
				'classjob', 'item', 'item.name', 'item.recipe', 'item.vendors',
			))
			->where('item_id', '>', 0) // Avoids mining/botany "bug"
			->orderBy('classjob_id')
			->orderBy('level')
			->orderBy('xp')
			->orderBy('gil');

		// Job IDs
		$query->whereIn('classjob_id', $job_ids);

		// Level Range
		$min = $input['min_level'];
		$max = $input['max_level'];

		// Invert if needed
		if ($min > $max) list($max, $min) = array($min, $max);

		$query->whereBetween('level', array($min, $max));
		
		// Triple Only
		if ($input['triple_only'] == 'true')
			$query->where('triple', 1);

		// Types
		$query->whereIn('type', $input['types']);

		// Text Searches
		if ($input['leve_name'])
			$query->where('name', 'like', '%' . $input['leve_name'] . '%');

		$leves = $query
			// ->remember(Config::get('site.cache_length'))
			->get();

		$location_search = strtolower($input['leve_location']);
		$item_search = strtolower($input['leve_item']);

		$rewards = LeveReward::with('item')
			->whereBetween('level', array($min, $max))
			->whereIn('classjob_id', $job_ids)
			->get();

		$leve_rewards = [];

		foreach ($leves as $k => $row)
		{
			if ($item_search && ! preg_match('/' . $item_search . '/', strtolower($row->item->name->term)))
			{
				unset($leves[$k]);
				continue;
			}
			
			// TODO this can be moved into the query itself now, most likely
			if ($location_search)
			{
				if ( ! preg_match('/' . $location_search . '/', strtolower($row->location)) &&
					 ! preg_match('/' . $location_search . '/', strtolower($row->major_location)) && 
					 ! preg_match('/' . $location_search . '/', strtolower($row->minor_location))
				)
				{
					unset($leves[$k]);
					continue;
				}
			}

			foreach($rewards as $reward)
				if ($reward->classjob_id == $row->classjob_id && $reward->level == $row->level)
					$leve_rewards[$row->id][] = $reward;
		}
		
		return view('levequests.rows', compact('leves', 'leve_rewards', 'input'));
	}

}
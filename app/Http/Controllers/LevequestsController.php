<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Cache;
use Config;

use App\Models\Garland\Leve;
use App\Models\Garland\Job;
use App\Models\Garland\JobCategory;

class LevequestsController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		view()->share('active', 'leves');
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
			return Leve::with('job_category', 'rewards', 'requirements', 'requirements.recipes', 'requirements.shops')
				->has('requirements')
				->whereIn('job_category_id', range(9,19)) // 9-19 are solo categories for DOL/H
				->orderBy('job_category_id')
				->orderBy('level')
				->orderBy('xp', 'desc')
				->orderBy('gil', 'desc')
				->get();
		});

		$leves = [];
		foreach ($all_leves as $leve)
			$leves[$leve->job_category->jobs[0]->abbr][$leve->level][] = $leve;

		$rewards = Cache::remember('rewards_' . Config::get('language'), 60, function() use ($all_leves) {
			
			$rewards = [];
			foreach($all_leves as $leve)
			{
				$job = $leve->job_category->jobs[0]; // Gauranteed to only be one thanks to range specified above
				foreach ($leve->rewards as $reward)
				{
					$rewards[$job->id][$leve->level][$reward->id]['item'] = $reward;
					$rewards[$job->id][$leve->level][$reward->id]['amounts'][] = ($reward->pivot->amount ?: 1) . ' (' . $reward->pivot->rate . '%)';
				}
			}

			foreach ($rewards as $jid => $js)
				foreach ($js as $lid => $ls)
					foreach ($ls as $rid => $rs)
					{
						sort($rewards[$jid][$lid][$rid]['amounts']);
						$rewards[$jid][$lid][$rid]['amounts'] = array_unique($rewards[$jid][$lid][$rid]['amounts']);
					}

			return $rewards;
		});
		
		$crafting_job_list = Job::whereIn('id', $crafting_job_ids)->get();
		$opening_level = 1;
		$opening_class = 'CRP';

		return view('levequests.index', compact('crafting_job_list', 'crafting_job_ids', 'leves', 'rewards', 'type_to_icon', 'opening_level', 'opening_class'));
	}

	public function getBreakdown($leve_id)
	{
		extract($this->_breakdown($leve_id)); // Reverse a compact, sets $leve and $chart
		
		// Get other Leve's at this level
		$others = Leve::where('level', $leve->level)
			->where('job_category_id', $leve->job_category_id)
			->where('id', '!=', $leve->id)
			->get();

		return view('levequests.breakdown', compact('leve', 'chart', 'others'));
	}

	private function _breakdown($leve_id = 0)
	{
		$leve = Leve::with('job_category', 'job_category.jobs', 'requirements', 'requirements.recipes', 'requirements.recipes.reagents')->find($leve_id);
		$experience = array_intersect_key(config('experience'), array_flip(range($leve->level, $leve->level + 10)));
		
		// Leve breakdown only exists to handle Crafting (and Fishing) realted jobs
		if ( ! in_array($leve->job_category_id, range(9,16)) && $leve->job_category_id != 19) // 9-16 CRP-WVR, 19 = FSH
			abort(404);

		$chart = [];
		foreach ($experience as $xp_level => $xp)
		{
			// NQ Turnins
			$amount = $turnins = 0;
			if ($leve->xp > 0)
				while ($amount < $xp)
				{
					$amount += $leve->xp;
					$turnins++;
				}

			// HQ Turnins
			$amount = $hq_turnins = 0;
			if ($leve->xp > 0)
				while ($amount < $xp)
				{
					$amount += $leve->xp * 2; // 2.1 patch changed it from 200% to 100% bonus
					$hq_turnins++;
				}

			$chart[$xp_level] = [
				'level' => $xp_level,
				'requires' => $xp,
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

		$crafting_job_list = Job::whereIn('id', $crafting_job_ids)->get();

		return view('levequests.advanced', compact('crafting_job_list', 'crafting_job_ids'));
	}

	/**
	 * The advanced form requests data via Ajax here
	 * @param  Request
	 * @return View
	 */
	public function getPopulateAdvanced(Request $request)
	{
		// Parse the Job IDs
		$selected_classes = $request->input('classes', []);
		if (empty($selected_classes)) $selected_classes = ['CRP']; // CRP default
		$jc_ids = JobCategory::whereIn('name', $selected_classes)->lists('id')->all();

		// Level Range
		$min = $request->input('min_level');
		$max = $request->input('max_level');
		// Invert if needed
		if ($min > $max) list($max, $min) = [$min, $max];

		// All Leves
		$query = Leve::with(array(
				'job_category', 'job_category.jobs',
				'location',
				'requirements', 'requirements.recipes', 'requirements.shops',
			))
			->has('requirements')
			->whereIn('job_category_id', $jc_ids)
			->whereBetween('level', [$min, $max])
			->orderBy('job_category_id')
			->orderBy('level')
			->orderBy('xp')
			->orderBy('gil');
		
		// Repeatable Only
		if ($request->input('repeatable_only') == 'true')
			$query->where('repeats', '>', 1);


		// Text Searches
		if ($request->input('leve_name'))
			$query->where('name', 'like', '%' . $request->input('leve_name') . '%');
		if ($request->input('leve_location'))
			$query->whereHas('location', function($query) use ($request) {
				$query->where('name', 'like', '%' . $request->input('leve_location') . '%');
			});
		if ($request->input('leve_item'))
			$query->whereHas('requirements', function($query) use ($request) {
				$query->where('name', 'like', '%' . $request->input('leve_item') . '%');
			});

		// \DB::connection()->enableQueryLog();
		$leves = $query->get();

		// Filter the leves based on the types selected

		$types = $request->input('types', []);
		// array_walk($types, function(&$x) { $x = str_slug($x); });

		$leves = $leves->filter(function($leve) use ($types) {
			return in_array($leve->simple_type, $types);
		});

		// dd(self::logger());
		// dd($leves[0]->type);
		// dd($leves[0]->requirements[0]->pivot->amount);
		
		return view('levequests.rows', compact('leves'));
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
<?php

namespace App\Http\Controllers;

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

	public function index()
	{
		return view('levequests.index');
	}

	public function breakdown($leve_id)
	{
		if ( ! is_numeric($leve_id))
			abort(404);

		extract($this->buildBreakdown($leve_id)); // Reverse a compact, sets $leve and $chart

		// Get other Leve's at this level
		$others = Leve::where('level', $leve->level)
			->where('job_category_id', $leve->job_category_id)
			->where('id', '!=', $leve->id)
			->get();

		return view('levequests.breakdown', compact('leve', 'chart', 'others'));
	}

	private function buildBreakdown($leve_id = 0)
	{
		$leve = Leve::with('location', 'job_category', 'job_category.jobs', 'requirements', 'requirements.recipes', 'requirements.recipes.reagents')->find($leve_id);
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

	public function vs($leveA = 1, $leveB = 1)
	{
		if ( ! (is_numeric($leveA) && is_numeric($leveB)))
			abort(404);

		$a = $this->buildBreakdown($leveA);
		$b = $this->buildBreakdown($leveB);

		return view('levequests.vs', compact('a', 'b'));
	}

}
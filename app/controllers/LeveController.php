<?php

class LeveController extends BaseController 
{

	public function getIndex()
	{
		// All Jobs
		$job_list = array();
		foreach (Job::all() as $j)
			$job_list[$j->abbreviation] = $j->name;

		return View::make('leves')
			->with('active', 'leves')
			->with('job_list', $job_list);
	}

	public function postIndex()
	{
		// Parse the Job IDs
		$job_ids = array();
		foreach (Job::whereIn('abbreviation', Input::get('classes'))->get() as $j)
			$job_ids[] = $j->id;

		if (empty($job_ids))
			$job_ids[] = 1;

		// All Leves
		$query = Leve::with('job', 'item', 'major', 'minor', 'location')
			->orderBy('job_id')
			->orderBy('level')
			->orderBy('xp')
			->orderBy('gil');

		// Job IDs
		$query->whereIn('job_id', $job_ids);

		// Level Range
		$min = Input::get('min_level');
		$max = Input::get('max_level');

		// Invert if needed
		if ($min > $max) list($max, $min) = array($min, $max);

		$query->whereBetween('level', array($min, $max));
		
		// Triple Only
		if (Input::get('triple_only') == 'true')
			$query->where('triple', 1);

		// Types
		$query->whereIn('type', Input::get('types'));

		$leve_records = $query->get();

		return View::make('leve.rows')
			->with('leves', $leve_records);
	}

	public function getBreakdown($leve_id = 1)
	{
		foreach ($this->_breakdown($leve_id) as $key => $value)
			$$key = $value;

		// Get other Leve's at this level
		$other_leves = Leve::where('level', $leve->level)
			->where('job_id', $leve->job_id)
			->where('id', '!=', $leve->id)
			->get();

		return View::make('leve.breakdown')
			->with(array(
				'leve' => $leve,
				'chart' => $chart,
				'others' => $other_leves
			));
	}

	private function _breakdown($leve_id = 0)
	{
		$leve = Leve::with('item')->find($leve_id);
		$experience = Experience::whereBetween('level', array($leve->level, $leve->level + 9))->get();
		
		$xp_rewarded = $leve->xp * 3;

		$chart = array();
		foreach ($experience as $xp)
		{
			$previous_overkill = isset($chart[$xp->level - 1]) ? $chart[$xp->level - 1]['overkill'] : 0;

			$needed = $xp->experience - $previous_overkill;

			$amount = $turnins = 0;
			if ($xp_rewarded > 0)
				while ($amount < $needed)
				{
					$amount += $xp_rewarded;
					$turnins++;
				}
			$chart[$xp->level] = array(
				'level' => $xp->level,
				'requires' => $xp->experience,
				'previous_overkill' => $previous_overkill,
				'turnins' => $turnins,
				'overkill' => $amount - $needed
			);
		}

		return array('leve' => $leve, 'chart' => $chart);
	}

	public function getVs($leveA = 1, $leveB = 1)
	{
		return View::make('leve.vs')
			->with(array(
				'a' => $this->_breakdown($leveA),
				'b' => $this->_breakdown($leveB)
			));
	}

}
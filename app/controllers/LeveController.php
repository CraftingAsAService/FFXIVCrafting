<?php

class LeveController extends BaseController 
{

	public function getIndex()
	{
		// All Jobs
		$job_list = array();
		foreach (Job::all() as $j)
			$job_list[$j->abbreviation] = $j->name;

		// All Leves
		$leve_records = Leve::with('job', 'item', 'major', 'minor', 'location')
			->orderBy('job_id')
			->orderBy('level')
			->orderBy('xp')
			->orderBy('gil')
			->get();

		$leves = array();
		foreach ($leve_records as $leve)
		{
			if ( ! isset($leves[$leve->job->abbreviation]))
				$leves[$leve->job->abbreviation] = array();

			$leves[$leve->job->abbreviation][] = $leve;
		}

		return View::make('leves')
			->with('active', 'leves')
			->with('leves', $leves)
			->with('job_list', $job_list);
	}

}
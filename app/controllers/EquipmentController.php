<?php

class EquipmentController extends BaseController 
{

	public function getIndex()
	{
		// All Jobs
		$job_list = array();
		foreach (Job::all() as $j)
			$job_list[$j->abbreviation] = $j->name;

		return View::make('equipment')
			->with('error', FALSE)
			->with('active', 'equipment')
			->with('job_list', $job_list);
	}

	public function badUrl()
	{
		return Redirect::to('/equipment');
	}

	public function postIndex()
	{
		$vars = array('class' => 'CRP', 'level' => 5, 'forecast' => '3', 'hindsight' => 0, 'craftable_only' => 0);
		$values = array();
		foreach ($vars as $var => $default)
			$values[] = Input::has($var) ? Input::get($var) : $default;
		
		return Redirect::to('/equipment/list?' . implode(':', $values));
	}

	public function getList()
	{
		// Get Options
		$options = Input::all() ? explode(':', array_keys(Input::all())[0]) : array();

		// Parse Options              // Defaults
		$desired_job    = isset($options[0]) ? $options[0] : 'CRP';
		$level          = isset($options[1]) ? $options[1] : 5;
		$forecast       = isset($options[2]) ? $options[2] : 3;
		$hindsight      = isset($options[3]) ? $options[3] : 0;
		$craftable_only = isset($options[4]) ? $options[4] : 1;

		View::share('active', 'equipment');

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
			return View::make('equipment')
				->with('error', TRUE);

		// Make sure level is valid
		if ($level < 1 || ! is_numeric($level)) $level = 1;
		elseif ($level > 50) $level = 50;

		// control the Forecast
		if ($forecast < 0)     $forecast = 0;
		elseif ($forecast > 5) $forecast = 5;

		// Find equipment at this level, per equipment type
		$start = $level;
		if ($hindsight) { $start--; $forecast++; }
		if ($level == 50) $start -= $forecast;

		$slots = Slot::where('type', 'equipment')->orderBy('rank')->get();

		$equipment = array();
		foreach (range($start - 1, $start + $forecast) as $use_level)
			$equipment[$use_level] = Item::calculate($job->abbreviation, $use_level, $craftable_only);

		$changes = array();
		foreach (range($start, $start + $forecast) as $use_level)
		{
			$changes[$use_level] = array();

			$current_bucket =& $equipment[$use_level];
			$previous_bucket =& $equipment[$use_level - 1];

			if (empty($current_bucket) || empty($previous_bucket))
				continue;

			foreach ($slots as $slot)
			{
				// Slots empty?
				if ($current_bucket[$slot->name] == array() || $previous_bucket[$slot->name] == array())
				{
					// Maybe it's just previous that's empty
					if ( ! end($current_bucket[$slot->name]))
						// Nope
						continue;
					else
						$previous_bucket[$slot->name][0] = (object) array('level' => 0);
				}

				// Are the items the same?  No changes.
				if ($current_bucket[$slot->name][0]->level == $previous_bucket[$slot->name][0]->level)
					continue;

				$changes[$use_level][$slot->name] = TRUE;

				if ($previous_bucket[$slot->name][0]->level == 0)
					unset($previous_bucket[$slot->name][0]);
			}
		}

		// List of stats
		$disciple_focus = array();
		foreach (Stat::all() as $stat)
			$disciple_focus[$stat->name] = $stat->disciple_focus;

		$first_time = TRUE;

		if (Session::has('equipment_first_time'))
			$first_time = FALSE;
		else
			Session::put('equipment_first_time', TRUE);

		return View::make('equipment.list')
			->with(array(
				'equipment' => $equipment,
				'slots' => $slots,
				'changes' => $changes,
				'disciple_focus' => $disciple_focus,

				'kill_column' => $start - 1,

				'job' => $job,

				'level' => $level,
				'forecast' => $forecast,
				'hindsight' => $hindsight,
				'craftable_only' => $craftable_only,
				'first_time' => $first_time
			));
			
	}

}
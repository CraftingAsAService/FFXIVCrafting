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
		$vars = array('class' => 'CRP', 'level' => 5, 'craftable_only' => 0, 'slim_mode' => 0);
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
		$level = isset($options[1]) ? $options[1] : 1;
		$craftable_only = isset($options[2]) ? $options[2] : 1;
		$slim_mode = isset($options[3]) ? $options[3] : 1;

		// Make sure level is valid
		if ($level < 1 || ! is_numeric($level)) $level = 1;
		elseif ($level > 50) $level = 50;

		// All Jobs
		$job_list = array();
		foreach (Job::all() as $j)
			$job_list[$j->abbreviation] = $j->name;

		// Jobs are capital
		$desired_job = strtoupper($desired_job);

		// Make sure it's a real job
		$job = Job::where('abbreviation', $desired_job)->first();

		// If the job isn't real, error out
		if ( ! $job)
			return View::make('equipment')
				->with('error', TRUE);

		// Get all slots
		$slots = Slot::common();

		// What stats do the class like?
		$job_focus = Stat::focus($job->abbreviation);

		View::share('job_list', $job_list);
		View::share('job', $job);
		View::share('job_focus', $job_focus);

		$limit = 48;
		if ($slim_mode)
			$limit = 47;

		if ($level > $limit)
			$level = $limit;

		View::share('original_level', $level);

		$starting_equipment = array();
		if ($level > 1)
		{
			View::share('level', $level - 1);
			
			$equipment = Item::calculate($job->abbreviation, $level - 1, $craftable_only);
			$starting_equipment[$level - 1] = $this->getOutput($equipment, $slots);
		}

		foreach (range($level, $level + ($slim_mode ? 3 : 2)) as $e_level)
		{
			View::share('level', $e_level);
			
			$equipment = Item::calculate($job->abbreviation, $e_level, $craftable_only);
			$starting_equipment[$e_level] = $this->getOutput($equipment, $slots);
		}

		return View::make('equipment.list')
			->with(array(
				'craftable_only' => $craftable_only,
				'slots' => $slots,
				'level' => $level, // Reset view's level variable back to normal
				'starting_equipment' => $starting_equipment,
				'slim_mode' => $slim_mode
			));
	}

	public function postLoad()
	{
		$job = Input::get('job');
		$level = Input::get('level');
		$craftable_only = Input::get('craftable_only');

		// All Jobs
		$job_list = array();
		foreach (Job::all() as $j)
			$job_list[$j->abbreviation] = $j->name;

		// Make sure it's a real job
		$job = Job::where('abbreviation', strtoupper($job))->first();

		// What stats do the class like?
		$job_focus = Stat::focus($job->abbreviation);

		View::share('job_list', $job_list);
		View::share('job', $job);
		View::share('job_focus', $job_focus);
		View::share('level', $level);

		$slots = Slot::common();
		$equipment = Item::calculate($job->abbreviation, $level, $craftable_only);

		$output = $this->getOutput($equipment, $slots);

		exit(json_encode($output));
	}

	private function getOutput($equipment = array(), $slots = array())
	{
		$output = array('slots' => array());
		foreach ($slots as $slot)
			$output['slots'][$slot->name] = View::make('equipment.cell', array(
				'items' => $equipment[$slot->name],
				'slot' => $slot
			))->render();

		$output['head'] = View::make('equipment.cell-head')->render();
		$output['foot'] = View::make('equipment.cell-foot')->render();

		return $output;
	}

	public function getOldList()
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

		// What stats do the class like?
		$job_focus = Stat::focus($job->abbreviation);

		$equipment = array();
		foreach (range($start - 1, $start + $forecast) as $use_level)
			$equipment[$use_level] = Item::calculate($job->abbreviation, $use_level, $craftable_only);

		// Make sure the pieces avoid pieces with certain stats
		$stats_to_avoid = Stat::avoid($job->abbreviation);

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

		// List of stats that show up
		$visible_stats = array();
		foreach ($equipment as $use_levelB => $slotsB)
			foreach ($slotsB as $slotB => $itemsB)
				foreach ($itemsB as $itemB)
					foreach ($itemB->stats as $statB => $amountB)
						$visible_stats[] = $statB;


		$visible_stats = array_unique($visible_stats);
		sort($visible_stats);

		// Make sure visible stats aren't boring
		$boring_stats = Stat::boring();
		foreach ($visible_stats as $key => $stat)
			if (in_array($stat, $boring_stats))
				unset($visible_stats[$key]);

		// Was this their first run?
		$first_time = TRUE;

		if (Session::has('equipment_first_time'))
			$first_time = FALSE;
		else
			Session::put('equipment_first_time', TRUE);

		return View::make('equipment.old_list')
			->with(array(
				'equipment' => $equipment,
				'slots' => $slots,
				'changes' => $changes,
				'job_focus' => $job_focus,
				'visible_stats' => $visible_stats,
				'stats_to_avoid' => $stats_to_avoid,

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
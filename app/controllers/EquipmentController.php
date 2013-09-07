<?php

class EquipmentController extends BaseController 
{

	public function getIndex()
	{
		return Redirect::to('/');
	}

	public function postIndex()
	{
		//return $this->calculate();
		return Redirect::to('/equipment/' . Input::get('class') . '/' . Input::get('level') . '/' . Input::get('forecast') . '/' . Input::has('hindsight'));
	}

	public function calculate($desired_job = '', $level = 1, $forecast = 1, $hindsight = FALSE)
	{
		// Jobs are capital
		$desired_job = strtoupper($desired_job);

		// Make sure it's a real job
		$job = Job::where('abbreviation', $desired_job)->first();

		// TODO: proper error
		if ( ! $job)
			exit('Error, unrecognized job/class');

		// Make sure level is valid
		if ($level < 1 || ! is_numeric($level))
			$level = 1;
		elseif ($level > 50)
			$level = 50;

		// control the Forecast
		if ($forecast < 0)
			$forecast = 0;
		elseif ($forecast > 5)
			$forecast = 5;

		// Figure out the Discipline
		$disciple = 'DOH'; // Assume Disciple of Hand
		if (in_array($desired_job, array('MIN', 'BTN', 'FSH', 'DOL')))
			$disciple = 'DOL';

		$disciple = Job::where('abbreviation', $disciple)->first();

		// All Jobs
		$job_list = array();
		foreach (Job::all() as $j)
			$job_list[$j->abbreviation] = $j->name;

		// Find equipment at this level, per equipment type
		$equipment = array();
		$start = $level;
		if ($hindsight) $start--;

		foreach (range($start - 1, $start + $forecast) as $use_level)
			$equipment[$use_level] = Equipment::calculate($job->abbreviation, $disciple->abbreviation, $use_level);

		$changes = array($start => array());
		foreach (range($start, $start + $forecast) as $use_level)
		{
			$changes[$use_level] = array();

			foreach (array_keys($equipment[$use_level]) as $slot)
			{
				if (empty($equipment[$use_level]) || empty($equipment[$use_level - 1]))
					continue;

				if (end($equipment[$use_level][$slot]) == array() || end($equipment[$use_level - 1][$slot]) == array())
				{
					if (end($equipment[$use_level][$slot]) != array())
					{
						$current =& end($equipment[$use_level][$slot])[0];
						$stats = array('Materia' => $current->materia);
						foreach ($current->stats as $stat)
							$stats[$stat->name] = $stat->pivot->amount;

						$changes[$use_level][$slot] = $stats;
					}
					
					continue;
				}

				if (end($equipment[$use_level][$slot])[0]->level != end($equipment[$use_level - 1][$slot])[0]->level)
				{
					$a = $b = array();
					$previous =& end($equipment[$use_level - 1][$slot])[0];
					$current =& end($equipment[$use_level][$slot])[0];
					
					$a['Materia'] = $previous->materia;
					$b['Materia'] = $current->materia;

					foreach ($previous->stats as $stat)
						$a[$stat->name] = $stat->pivot->amount;

					foreach ($current->stats as $stat)
						$b[$stat->name] = $stat->pivot->amount;

					$diff = array();
					$diff_keys = array_unique(array_merge(array_keys($a), array_keys($b)));
					foreach ($diff_keys as $key)
					{
						if ( ! isset($a[$key]))
							$diff[$key] = $b[$key];
						elseif ( ! isset($b[$key]))
							$diff[$key] = '-' . $a[$key];
						else
							$diff[$key] = $b[$key] - $a[$key];

						if ($diff[$key] == 0)
							unset($diff[$key]);
					}

					$changes[$use_level][$slot] = $diff;
				}
			}
		}

		// We got the one before start for the comparison, but we're done with it now
		unset($equipment[$start - 1]);

		$stats = array();
		foreach($equipment as $td_level => $slots)
		{
			foreach(EquipmentType::orderBy('rank')->get() as $slot)
			{
				$item = $slots[$slot->name];

				if ( ! isset($item[0]))
					continue;

				$item = $item[0];

				if ( ! isset($stats[$td_level]['Materia']))
					$stats[$td_level]['Materia'] = 0;

				$stats[$td_level]['Materia'] += $item->materia;

				foreach ($item->stats as $stat)
				{
					if ( ! isset($stats[$td_level][$stat->name]))
						$stats[$td_level][$stat->name] = 0;

					$stats[$td_level][$stat->name] += $stat->pivot->amount;
				}
			}
		}

		$stats_diff = array();
		foreach ($stats as $stat_level => $list)
		{
			$stats_diff[$stat_level] = array();

			if ( ! isset($stats[$stat_level - 1]))
				continue;

			$a =& $stats[$stat_level - 1];
			$b =& $stats[$stat_level];

			$diff = array();
			$diff_keys = array_unique(array_merge(array_keys($a), array_keys($b)));
			foreach ($diff_keys as $key)
			{
				if ( ! isset($a[$key]))
					$diff[$key] = $b[$key];
				elseif ( ! isset($b[$key]))
					$diff[$key] = '-' . $a[$key];
				else
					$diff[$key] = $b[$key] - $a[$key];

				if ($diff[$key] == 0)
					unset($diff[$key]);
			}

			$stats_diff[$stat_level] = $diff;
		}

		return View::make('equipment')
			->with(array(
				'equipment' => $equipment,
				'stats' => $stats,
				'stats_diff' => $stats_diff,
				'changes' => $changes,
				'job' => $job,
				'disciple' => $disciple,
				'job_list' => $job_list,
				'level' => $level,
				'forecast' => $forecast,
				'hindsight' => $hindsight
			));
			
	}

}
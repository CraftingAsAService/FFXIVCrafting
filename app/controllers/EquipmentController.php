<?php

class EquipmentController extends BaseController 
{

	public function getIndex()
	{
		return View::make('equipment.index')
			->with('error', FALSE)
			->with('active', 'equipment')
			->with('job_list', ClassJob::get_name_abbr_list())
			->with('previous', Cookie::get('previous_equipment_load'));
	}

	public function badUrl()
	{
		return Redirect::to('/equipment');
	}

	public function postIndex()
	{
		$vars = array('class' => 'CRP', 'level' => 5, 'craftable_only' => 0, 'slim_mode' => 0, 'rewardable_too' => 0);
		$values = array();
		foreach ($vars as $var => $default)
			$values[] = Input::has($var) ? Input::get($var) : $default;

		$url = '/equipment/list?' . implode(':', $values);

		// Queueing the cookie, we won't need it right away, so it'll save for the next Response::
		Cookie::queue('previous_equipment_load', $url, 525600); // 1 year's worth of minutes
		
		return Redirect::to($url);
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
		$rewardable_too = isset($options[4]) ? $options[4] : 1;

		// Make sure level is valid
		if ($level < 1 || ! is_numeric($level)) $level = 1;
		elseif ($level > 50) $level = 50;

		// All Jobs
		$job_list = ClassJob::get_name_abbr_list();

		// Jobs are capital
		$desired_job = strtoupper($desired_job);

		// Make sure it's a real job
		$job = ClassJob::get_by_abbr($desired_job);

		// If the job isn't real, error out
		if ( ! $job)
			return View::make('equipment')
				->with('error', TRUE);

		// Get all roles
		$roles = Config::get('site.equipment_roles');

		// What stats do the class like?
		$stat_ids_to_focus = Stat::get_ids(Stat::focus($job->abbr->term));

		View::share('job_list', $job_list);
		View::share('job', $job);
		View::share('stat_ids_to_focus', $stat_ids_to_focus);

		$limit = 48;
		if ($slim_mode)
			$limit = 47;

		if ($level > $limit)
			$level = $limit;

		View::share('original_level', $level);

		View::share('slim_mode', $slim_mode);

		#$starting_equipment = array();

		// 3 + ($slim_mode ? 1 : 0)
		$equipment = Item::calculate($job->id, $level - 1, 4, $craftable_only, $rewardable_too);
		$equipment = $this->getOutput($equipment);

		//dd($equipment);
		//dd($equipment['46']);

		// if ($level > 1)
		// {
		// 	View::share('level', $level - 1);
			
		// 	$starting_equipment[$level - 1] = $this->getOutput($equipment, $roles);
		// }


		// foreach (range($level, $level + ($slim_mode ? 3 : 2)) as $e_level)
		// {
		// 	View::share('level', $e_level);
			
		// 	$equipment = Item::calculate($job->id, $e_level, $craftable_only, $rewardable_too);
		// 	$starting_equipment[$e_level] = $this->getOutput($equipment, $roles);
		// }

		return View::make('equipment.list')
			->with(array(
				'craftable_only' => $craftable_only,
				'rewardable_too' => $rewardable_too,
				'roles' => $roles,
				'level' => $level, // Reset view's level variable back to normal
				'equipment' => $equipment
			));
	}

	public function postLoad()
	{
		$job = Input::get('job');
		$level = Input::get('level');
		$craftable_only = Input::get('craftable_only');
		$rewardable_too = Input::get('rewardable_too');

		// All Jobs
		$job_list = ClassJob::get_name_abbr_list();

		// Jobs are capital
		$desired_job = strtoupper($job);

		$job = ClassJob::get_by_abbr($desired_job);

		// What stats do the class like?
		$stat_ids_to_focus = Stat::get_ids(Stat::focus($job->abbr->term));

		View::share('job_list', $job_list);
		View::share('job', $job);
		View::share('stat_ids_to_focus', $stat_ids_to_focus);
		View::share('level', $level);

		$equipment = Item::calculate($job->id, $level, 0, $craftable_only, $rewardable_too);
		
		exit(json_encode($this->getOutput($equipment)));
	}

	private function getOutput($equipment = array(), $solo = false)
	{
		$output = array(
			'head' => array(),
			'foot' => array(),
			'gear' => array()
		);

		foreach ($equipment as $level => $gear)
		{
			$output['head'][$level] = View::make('equipment.cell-head', array(
				'level' => $level
			))->render();

			$output['foot'][$level] = View::make('equipment.cell-foot', array(
				'level' => $level
			))->render();

			foreach ($gear as $role => $items)
				$output['gear'][$role][$level] = View::make('equipment.cell', array(
					'level' => $level,
					'items' => $items,
					'role' => $role
				))->render();
		}

		return $output;
	}

}
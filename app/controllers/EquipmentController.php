<?php

class EquipmentController extends BaseController 
{

	public function getIndex()
	{
		// All Jobs
		$job_list = Job::all()->lists('name', 'abbreviation');

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
		$vars = array('class' => 'CRP', 'level' => 5, 'craftable_only' => 0, 'slim_mode' => 0, 'rewardable_too' => 0);
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
		$rewardable_too = isset($options[4]) ? $options[4] : 1;

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

		// Get all roles
		$roles = Config::get('site.equipment_roles');

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
			
			$equipment = Item::calculate($job->abbreviation, $level - 1, $craftable_only, $rewardable_too);
			$starting_equipment[$level - 1] = $this->getOutput($equipment, $roles);
		}

		foreach (range($level, $level + ($slim_mode ? 3 : 2)) as $e_level)
		{
			View::share('level', $e_level);
			
			$equipment = Item::calculate($job->abbreviation, $e_level, $craftable_only, $rewardable_too);
			$starting_equipment[$e_level] = $this->getOutput($equipment, $roles);
		}

		return View::make('equipment.list')
			->with(array(
				'craftable_only' => $craftable_only,
				'roles' => $roles,
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

		$equipment = Item::calculate($job->abbreviation, $level, $craftable_only);

		// Get all roles
		$roles = Config::get('site.equipment_roles');

		$output = $this->getOutput($equipment, $roles);

		exit(json_encode($output));
	}

	private function getOutput($equipment = array(), $roles = array())
	{
		$output = array('roles' => array());
		foreach ($roles as $role)
			$output['roles'][$role] = View::make('equipment.cell', array(
				'items' => $equipment[$role],
				'role' => $role
			))->render();

		$output['head'] = View::make('equipment.cell-head')->render();
		$output['foot'] = View::make('equipment.cell-foot')->render();

		return $output;
	}

}
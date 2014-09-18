<?php

class GearController extends BaseController 
{

	public function __construct()
	{
		parent::__construct();
		View::share('active', 'equipment');
	}

	public function getIndex()
	{
		$job_ids = Config::get('site.job_ids');

		return View::make('gear.index')
			->with('error', FALSE)
			->with('active', 'equipment')
			->with('crafting_job_list', ClassJob::with('name', 'en_abbr', 'en_name')->whereIn('id', $job_ids['crafting'])->get())
			->with('gathering_job_list', ClassJob::with('name', 'en_abbr', 'en_name')->whereIn('id', $job_ids['gathering'])->get())
			->with('basic_melee_job_list', ClassJob::with('name', 'en_abbr', 'en_name')->whereIn('id', $job_ids['basic_melee'])->get())
			->with('basic_magic_job_list', ClassJob::with('name', 'en_abbr', 'en_name')->whereIn('id', $job_ids['basic_magic'])->get())
			->with('job_ids', $job_ids)
			->with('previous', Cookie::get('previous_gear_load'));
	}

	public function postIndex()
	{
		$vars = array('class' => 'CRP', 'level' => 5, 'craftable_only' => 0, 'rewardable_too' => 0);
		$values = array();
		foreach ($vars as $var => $default)
			$values[] = Input::has($var) ? Input::get($var) : $default;

		$url = '/gear/list?' . implode(':', $values);

		// Queueing the cookie, we won't need it right away, so it'll save for the next Response::
		Cookie::queue('previous_gear_load', $url, 525600); // 1 year's worth of minutes
		
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
		$focused_stats = Stat::focus($job->abbr->term);
		$stat_ids_to_focus = Stat::get_ids($focused_stats);

		View::share('job_list', $job_list);
		View::share('job', $job);
		View::share('stat_ids_to_focus', $stat_ids_to_focus);

		$gear_focus = Config::get('site.gear_focus');
		foreach ($gear_focus as $jobs => $stats)
		{
			if (in_array($job->abbr->term, explode(',', $jobs)))
			{
				$gear_focus = $stats;
				break;
			}
		}
		$gear_focus_ids = Stat::get_ids($gear_focus);

		View::share('gear_focus', $gear_focus);
		View::share('gear_focus_ids', $gear_focus_ids);

		$limit = 48;

		// The limit may need to take one off.
		// If this is a DOW or DOM class, there's too many items at level 50 to produce good results
		$fifty_warning = false;
		if ($limit == 48)
		{
			// Get the "DOW/M" classes
			$dowm_class_ids = array();
			$cj = ClassJobCategory::with('classjob')->find(34); // "Disciples of War or Magic"
			foreach ($cj->classjob as $c)
				$dowm_class_ids[] = $c->id;

			if (in_array($job->id, $dowm_class_ids))
			{
				$fifty_warning = true;
				$limit--;
			}
		}

		if ($level > $limit)
			$level = $limit;

		View::share('original_level', $level);
		View::share('fifty_warning', $fifty_warning);
		
		$gear = Item::get_gear($job->id, $level, 4, $craftable_only, $rewardable_too);

		// var_dump($gear['Main Hand'][2320]);
		// dd($gear['Main Hand']);

		return View::make('gear.list')
			->with(array(
				'craftable_only' => $craftable_only,
				'rewardable_too' => $rewardable_too,
				'roles' => $roles,
				'level' => $level, // Reset view's level variable back to normal
				'gear' => $gear
			));
	}

}
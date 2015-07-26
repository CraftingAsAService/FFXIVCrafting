<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Config;
use Cookie;

use App\Models\CAAS\Stat;
use App\Models\Garland\Item;
Use App\Models\Garland\Job;
use App\Models\Garland\JobCategory;

class EquipmentController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		view()->share('active', 'equipment');
	}

	public function getIndex()
	{
		$job_ids = Config::get('site.job_ids');
		$crafting_job_list = Job::whereIn('id', $job_ids['crafting'])->get();
		$gathering_job_list = Job::whereIn('id', $job_ids['gathering'])->get();
		$basic_melee_job_list = Job::whereIn('id', $job_ids['basic_melee'])->get();
		$basic_magic_job_list = Job::whereIn('id', $job_ids['basic_magic'])->get();
		$previous = Cookie::get('previous_equipment_load');
		$error = false;

		return view('equipment.index', compact('error', 'crafting_job_list', 'gathering_job_list', 'basic_melee_job_list', 'basic_magic_job_list', 'job_ids', 'previous'));
	}

	public function badUrl()
	{
		return redirect('/equipment');
	}

	public function postIndex(Request $request)
	{
		$inputs = $request->all();

		$vars = ['class' => 'CRP', 'level' => 5, 'craftable_only' => 0, 'slim_mode' => 0, 'rewardable_too' => 0];
		$values = [];
		foreach ($vars as $var => $default)
			$values[] = isset($inputs[$var]) ? $inputs[$var] : $default;

		$url = '/equipment/list?' . implode(':', $values);

		// Queueing the cookie, we won't need it right away, so it'll save for the next Response::
		Cookie::queue('previous_equipment_load', $url, 525600); // 1 year's worth of minutes
		
		return redirect($url);
	}

	public function getList()
	{
		// Get Options
		$options = \Request::all() ? explode(':', array_keys(\Request::all())[0]) : [];

		// Parse Options              // Defaults
		$desired_job    = isset($options[0]) ? $options[0] : 'CRP';
		$level = isset($options[1]) ? $options[1] : 1;
		$craftable_only = isset($options[2]) ? $options[2] : 1;
		$slim_mode = isset($options[3]) ? $options[3] : 1;
		$rewardable_too = isset($options[4]) ? $options[4] : 1;

		// Make sure level is valid
		if ($level < 1 || ! is_numeric($level)) $level = 1;
		elseif ($level > config('site.max_level')) $level = config('site.max_level');

		// All Jobs
		$job_list = Job::lists('name', 'abbr')->all();

		// Jobs are capital
		$desired_job = strtoupper($desired_job);

		// Make sure it's a real job
		$job = Job::get_by_abbr($desired_job);

		// If the job isn't real, error out
		if ( ! $job)
			return view('equipment')
				->with('error', TRUE);

		// Get all roles
		$roles = Config::get('site.equipment_roles');

		// What stats do the class like?
		$stat_ids_to_focus = Stat::get_ids(Stat::focus($job->abbr));

		view()->share('job_list', $job_list);
		view()->share('job', $job);
		view()->share('stat_ids_to_focus', $stat_ids_to_focus);

		$limit = config('site.max_level') - 2;
		if ($slim_mode)
			$limit = config('site.max_level') - 3;

		// The limit may need to take one off.
		// If this is a DOW or DOM class, there's too many items at level 50 to produce good results
		$fifty_warning = false;
		if ($limit == config('site.max_level') - 2 || ($slim_mode && $limit == config('site.max_level') - 3))
		{
			// Get the "DOW/M" classes
			$dowm_class_ids = [];
			$cj = JobCategory::with('jobs')->find(34); // "Disciples of War or Magic"
			foreach ($cj->jobs as $c)
				$dowm_class_ids[] = $c->id;

			if (in_array($job->id, $dowm_class_ids))
			{
				$fifty_warning = true;
				$limit--;
			}
		}

		if ($level > $limit)
			$level = $limit;

		$original_level = $level;

		view()->share(compact('original_level', 'slim_mode', 'fifty_warning'));
		
		#$starting_equipment = [];

		// 3 + ($slim_mode ? 1 : 0)
		$equipment = Item::calculate($job->id, $level - 1, 4, $craftable_only, $rewardable_too);
		// dd($equipment);
		// dd($equipment[7]['Hands'][6][0]->attributes);//->relations['attributes']);
		// dd($equipment[7]['Hands'][6][0]->relations['attributes']);
		$equipment = $this->getOutput($equipment);

		// dd($equipment);
		//dd($equipment['46']);

		// if ($level > 1)
		// {
		// 	view()->share('level', $level - 1);
			
		// 	$starting_equipment[$level - 1] = $this->getOutput($equipment, $roles);
		// }


		// foreach (range($level, $level + ($slim_mode ? 3 : 2)) as $e_level)
		// {
		// 	view()->share('level', $e_level);
			
		// 	$equipment = Item::calculate($job->id, $e_level, $craftable_only, $rewardable_too);
		// 	$starting_equipment[$e_level] = $this->getOutput($equipment, $roles);
		// }

		// Reset view's level variable back to normal
		return view('equipment.list', compact('craftable_only', 'rewardable_too', 'roles', 'level', 'equipment'));
	}

	public function postLoad(Request $request)
	{
		$inputs = $request->all();

		$job = $request['job'];
		$level = $request['level'];
		$craftable_only = $request['craftable_only'];
		$rewardable_too = $request['rewardable_too'];

		// All Jobs
		$job_list = Job::lists('name', 'abbr')->all();

		// Jobs are capital
		$desired_job = strtoupper($job);

		$job = Job::where('abbr', $desired_job)->first();

		// What stats do the class like?
		$stat_ids_to_focus = Stat::get_ids(Stat::focus($job->abbr));

		view()->share(compact('job_list', 'job', 'stat_ids_to_focus', 'level'));

		$equipment = Item::calculate($job->id, $level, 0, $craftable_only, $rewardable_too);
		
		return $this->getOutput($equipment);
	}

	private function getOutput($equipment = [])//, $solo = false)
	{
		$output = [
			'head' => [],
			'foot' => [],
			'gear' => []
		];

		foreach ($equipment as $level => $gear)
		{
			$output['head'][$level] = view('equipment.cell-head', compact('level'))->render();

			$output['foot'][$level] = view('equipment.cell-foot', compact('level'))->render();

			foreach ($gear as $role => $items)
				$output['gear'][$role][$level] = view('equipment.cell', compact('level', 'items', 'role'))->render();
		}

		return $output;
	}

}

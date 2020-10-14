<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Garland\Job;
use App\Models\CAAS\Gear;
use App\Models\CAAS\Stat;

class GearController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		view()->share('active', 'gear');
	}

	public function getIndex(Request $request)
	{
		$crafting_job_list = Job::get_by_type('crafting');
		$gathering_job_list = Job::get_by_type('gathering');
		$advanced_melee_job_list = Job::get_by_type('advanced_melee');
		$advanced_magic_job_list = Job::get_by_type('advanced_magic');

		$defaults = $request->all();

		if (isset($defaults['options']))
			$defaults['options'] = empty($defaults['options']) ? [] : explode(',', $defaults['options']);

		return view('gear.index', compact('defaults', 'crafting_job_list', 'gathering_job_list', 'advanced_melee_job_list', 'advanced_magic_job_list'));
	}

	public function getProfile(Request $request, $job = 'ALC', $start_level = 1)
	{
		$options = array_diff(explode(',', $request->input('options', '')), ['']);

		$gear = Gear::profile($job, $start_level, 5, $options);

		$job = Job::get_by_abbr($job);

		$stat_focus = Stat::gear_focus($job->abbr);
		$stat_focus_ids = Stat::get_ids($stat_focus, true);

		return view('gear.profile', compact('gear', 'job', 'stat_focus', 'stat_focus_ids', 'start_level', 'options'));
	}

}

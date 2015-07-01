<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\CAAS\ClassJob;
use App\Models\CAAS\Gear;
use App\Models\CAAS\Stat;

class GearController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		view()->share('active', 'gear');
	}

	public function getIndex()
	{
		$crafting_job_list = ClassJob::get_by_type('crafting');
		$gathering_job_list = ClassJob::get_by_type('gathering');
		$basic_melee_job_list = ClassJob::get_by_type('basic_melee');
		$basic_magic_job_list = ClassJob::get_by_type('basic_magic');

		$defaults = \Request::all();

		if (isset($defaults['options']) && ! empty($defaults['options']))
			$defaults['options'] = explode(',', $defaults['options']);

		return view('gear.index', compact('defaults', 'crafting_job_list', 'gathering_job_list', 'basic_melee_job_list', 'basic_magic_job_list'));
	}

	public function getProfile($job = 'ALC', $start_level = 1)
	{
		$options = array_diff(explode(',', \Request::get('options', '')), ['']);

		$gear = Gear::profile($job, $start_level, 5, $options);

		$classjob = ClassJob::get_by_abbr($job);
		
		$stat_focus = Stat::gear_focus($job);
		$stat_focus_ids = Stat::get_ids($stat_focus, true);

		return view('gear.profile', compact('gear', 'classjob', 'stat_focus', 'stat_focus_ids', 'start_level', 'options'));
	}

}

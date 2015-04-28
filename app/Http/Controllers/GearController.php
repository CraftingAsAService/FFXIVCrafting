<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

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

	}

	public function getProfile($job = 'ALC', $start_level = 1)
	{
		$options = array_diff(explode(',', \Request::get('options', '')), ['']);

		$gear = Gear::profile($job, $start_level, 5, $options);
		
		$stat_focus = Stat::gear_focus($job);
		$stat_focus_ids = Stat::get_ids($stat_focus, true);

		return view('gear.index', compact('gear', 'stat_focus', 'stat_focus_ids', 'start_level', 'options'));
	}

	// public function postIndex($class = 'ALC', $level = 18)
	// {

	// }

}

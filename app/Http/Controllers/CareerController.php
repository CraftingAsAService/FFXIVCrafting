<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Cookie;
use Config;

use App\Models\Garland\Job;
use App\Models\Garland\Recipe;
use App\Models\Garland\Career;

class CareerController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		view()->share('active', 'career');
	}

	public function getIndex()
	{
		$job_ids = Config::get('site.job_ids');
		$crafting_job_list = Job::whereIn('id', $job_ids['crafting'])->get();
		$gathering_job_list = Job::whereIn('id', $job_ids['gathering'])->get();
			
		$previous_ccp = Cookie::get('previous_ccp');
		$previous_ccr = Cookie::get('previous_ccr');
		$previous_gc = Cookie::get('previous_gc');
		$previous_bc = Cookie::get('previous_bc');

		return view('career.index', compact('crafting_job_list', 'gathering_job_list', 'job_ids', 'previous_ccp', 'previous_ccr', 'previous_gc', 'previous_bc'));
	}

	public function postProducer(Request $request)
	{
		$input = $request->all();

		$my_class = $input['supporter-producer-class'];
		$supported_classes = implode(',', $input['supporter-supported-classes']);
		$min_level = (int) $input['supporter-min-level'] ?: 1;
		$max_level = (int) $input['supporter-max-level'] ?: 70;

		$url = '/career/producer/' . implode('/', [$my_class, $supported_classes, $min_level, $max_level]);

		// Queueing the cookie, we won't need it right away, so it'll save for the next Response::
		Cookie::queue('previous_ccp', $url, 525600); // 1 year's worth of minutes

		return redirect($url);
	}

	public function getProducer($my_class = '', $supported_classes = '', $min_level = 0, $max_level = 0)
	{
		# I am a  Carpenter  , what can I make to support  these 8 Classes  between levels  x and  y ?
		$supported_classes = explode(',', $supported_classes);

		$show_quests = false;//in_array($my_class, $supported_classes);

		if (empty($supported_classes))
			exit('No supported class selected... Todo: real error'); // TODO

		$all_classes = Job::lists('id', 'abbr')->all();
		foreach ($supported_classes as $k => $v)
			if (in_array($v, array_keys($all_classes)))
				$supported_classes[$k] = $all_classes[$v];
			else
				unset($supported_classes[$k]);

		if (empty($supported_classes))
			exit('No supported class recognized...'); // TODO

		$jobs = Job::whereIn('id', $supported_classes)->get();
		foreach ($jobs as $k => $v)
			$jobs[$k] = $v->name;
	
		$job = Job::where('abbr', $my_class)->first();

		if (empty($job))
			exit('No primary class recognized...'); // TODO

		$produced = Career::with('job', 'recipe', 'recipe.item', 'recipe.item.shops')
			->where('type', 'recipe')
			->whereBetween('level', [$min_level, $max_level])
			->whereHas('job', function($query) use ($supported_classes) {
				$query->whereIn('job_id', $supported_classes);
			})
			->whereHas('recipe', function($query) use ($job) {
				$query->where('job_id', $job->id);
			})
			->get();

		$recipes = $amounts = [];
		foreach ($produced as $career)
		{
			$recipes[$career->recipe->id] = $career->recipe;
			@$amounts[$career->recipe->id] += $career->job->sum('pivot.amount');
		}

		return view('career.production', compact('recipes', 'amounts', 'show_quests', 'jobs', 'job', 'min_level', 'max_level'));
	}

	public function postReceiver(Request $request)
	{
		$input = $request->all();

		$my_class = $input['receiver-recipient-class'];
		$supported_classes = implode(',', $input['receiver-producer-classes']);
		$min_level = (int) $input['receiver-min-level'] ?: 1;
		$max_level = (int) $input['receiver-max-level'] ?: 70;

		$url ='/career/receiver/' . implode('/', [$my_class, $supported_classes, $min_level, $max_level]);

		// Queueing the cookie, we won't need it right away, so it'll save for the next Response::
		Cookie::queue('previous_ccr', $url, 525600); // 1 year's worth of minutes

		return redirect($url);
	}

	public function getReceiver($my_class = '', $supported_classes = '', $min_level = 0, $max_level = 0)
	{
		# I am a  Carpenter  , what should  these 8 Classes  make for me between levels  x and  y ?
		$supported_classes = explode(',', $supported_classes);

		$show_quests = false;//in_array($my_class, $supported_classes);

		if (empty($supported_classes))
			exit('No supported class selected... Todo: real error'); // TODO

		$all_classes = Job::lists('id', 'abbr')->all();
		foreach ($supported_classes as $k => $v)
			if (in_array($v, array_keys($all_classes)))
				$supported_classes[$k] = $all_classes[$v];
			else
				unset($supported_classes[$k]);

		if (empty($supported_classes))
			exit('No supported class recognized...'); // TODO

		$jobs = Job::whereIn('id', $supported_classes)->get();
		foreach ($jobs as $k => $v)
			$jobs[$k] = $v->name;
	
		$job = Job::where('abbr', $my_class)->first();

		if (empty($job))
			exit('No primary class recognized...'); // TODO

		$received = Career::with('job', 'recipe', 'recipe.item', 'recipe.item.shops')
			->where('type', 'recipe')
			->whereBetween('level', [$min_level, $max_level])
			->whereHas('job', function($query) use ($job) {
				$query->where('job_id', $job->id);
			})
			->whereHas('recipe', function($query) use ($supported_classes) {
				$query->whereIn('job_id', $supported_classes);
			})
			->get();

		$recipes = $amounts = [];
		foreach ($received as $career)
		{
			$recipes[$career->recipe->id] = $career->recipe;
			@$amounts[$career->recipe->id] += $career->job->sum('pivot.amount');
		}

		return view('career.receiver', compact('recipes', 'amounts', 'show_quests', 'jobs', 'job', 'min_level', 'max_level'));
	}

	public function postGathering(Request $request)
	{
		$input = $request->all();

		$my_class = $input['gatherer-class'];
		$supported_classes = implode(',', $input['gathering-supported-classes']);
		$min_level = (int) $input['gathering-min-level'] ?: 1;
		$max_level = (int) $input['gathering-max-level'] ?: 70;

		// previous_gc or previous_bc
		$cookie_name = 'previous_' . ($my_class == 'BTL' ? 'b' : 'g') . 'c';

		$url = '/career/gathering/' . implode('/', [$my_class, $supported_classes, $min_level, $max_level]);

		// Queueing the cookie, we won't need it right away, so it'll save for the next Response::
		Cookie::queue($cookie_name, $url, 525600); // 1 year's worth of minutes

		return redirect($url);
	}

	public function getGathering($my_class = '', $supported_classes = '', $min_level = 0, $max_level = 0)
	{
		$supported_classes = explode(',', $supported_classes);

		$show_quests = false; //in_array($my_class, $supported_classes);

		if (empty($supported_classes))
			exit('No supported class selected... Todo: real error'); // TODO

		$all_classes = Job::lists('id', 'abbr')->all();
		foreach ($supported_classes as $k => $v)
			if (in_array($v, array_keys($all_classes)))
				$supported_classes[$k] = $all_classes[$v];
			else
				unset($supported_classes[$k]);

		if (empty($supported_classes))
			exit('No supported class recognized...'); // TODO

		$jobs = Job::whereIn('id', $supported_classes)->get();
		foreach ($jobs as $k => $v)
			$jobs[$k] = $v->name;
		
		if ($my_class != 'BTL')
			$job = Job::where('abbr', $my_class)->first();
		else
			$job = $my_class;

		if (empty($job))
			exit('No primary class recognized...'); // TODO

		// I am a  Miner  , what should I obtain to support  these 11 Classes  between ilevels   and   ?
		$query = Career::with('job', 'item', 'item.category', 'item.nodes', 'item.shops', 'item.fishing', 'item.mobs')
			->where('type', 'item')
			->whereBetween('level', [$min_level, $max_level])
			->whereHas('job', function($query) use ($supported_classes) {
				$query->whereIn('job_id', $supported_classes);
			});

		if (in_array($my_class, array('MIN', 'BTN')))
		{
			// Has nodes
			//  of the $my_class variety
			// 0 == MIN == Mineral Deposit
			// 1 == MIN == Rocky Outcropping
			// 2 == BTN == Mature Tree
			// 3 == BTN == Lush Vegetation
			$types = $my_class == 'MIN' ? [0, 1] : [2, 3];
			$query->whereHas('item', function($query) use ($types) {
				$query->whereHas('nodes', function($query) use ($types) {
					$query->whereIn('type', $types);
				});
			});
		}
		elseif ($my_class == 'FSH')
		{
			// Has Fishing
			$query->whereHas('item', function($query) {
				$query->has('fishing');
			});
		}
		else // Presumably BTL
		{
			// Has Mobs
			$query->whereHas('item', function($query) {
				$query->has('mobs');
			});
		}

		$gathering = $query->get();

		$items = $amounts = [];
		foreach ($gathering as $career)
		{
			$items[$career->item->id] = $career->item;
			@$amounts[$career->item->id] += $career->job->sum('pivot.amount');
		}

		return view('career.items', compact('items', 'amounts', 'show_quests', 'jobs', 'job', 'min_level', 'max_level'));
	}

}

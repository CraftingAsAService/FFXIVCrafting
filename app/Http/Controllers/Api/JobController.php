<?php

namespace App\Http\Controllers\Api;

use App\Models\Garland\Job;
use App\Models\Garland\Recipe;
use Illuminate\Support\Facades\Cache;

class JobController extends Controller
{
	public function types($type)
	{
		if ($type == 'crafting')
			$jobs = Cache::rememberForever('JobController:index:' . $type, function() {
				return Job::whereIn('id', Recipe::select(\DB::raw('DISTINCT job_id'))->pluck('job_id'))
					->get()
					->keyBy('id')
					->toArray();
			});

		return response()->json($jobs ?? []);
	}
}

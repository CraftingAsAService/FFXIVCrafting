<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Leve as LeveResource;
use App\Models\Garland\Job;
use App\Models\Garland\JobCategory;
use App\Models\Garland\Leve;

class LeveController extends Controller
{
	public function search()
	{
		$validatedData = request()->validate([
			'jobId' => 'required|numeric',
			'level' => 'required|numeric|min:1|max:' . config('site.max_level'),
		]);

		// All Job Categories that we're interested are solo 'ARM', 'CUL', etc
		//  There aren't any Disciple of the Hand global Leves
		$job = Job::findOrFail(request('jobId'));
		$jcIds = JobCategory::where('name', $job->abbr)->pluck('id')->all();

		$leves = Leve::with('requirements.recipes.item.category', 'location')
			->whereIn('job_category_id', $jcIds)
			->where('level', request('level'))
			->get();

		return LeveResource::collection($leves);
	}
}

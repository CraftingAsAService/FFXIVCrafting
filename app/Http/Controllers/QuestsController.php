<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Config;

use App\Models\Garland\Quest;
use App\Models\Garland\Job;

class QuestsController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		view()->share('active', 'quests');
	}

	public function getIndex()
	{
		// All Quests
		$results = Quest::with('job_category', 'job_category.jobs', 'requirements', 'requirements.recipes')
			->has('requirements')
			// ->whereHas('requirements', function($query) {
			// 	$query->has('recipes');
			// })
			->whereIn('job_category_id', range(9,19)) // 9-19 are solo categories for DOL/H
			->orderBy('job_category_id')
			->orderBy('level')
			->orderBy('sort')
			->get();

		$quests = [];
		foreach($results as $quest)
		{
			$quest->job = $quest->job_category->jobs[0];
			$items = [];

			if ( ! isset($quests[$quest->job->abbr]))
				$quests[$quest->job->abbr] = [];

			foreach ($quest->requirements as $requirement)
				// foreach ($requirement->recipes as $recipe)
				// 	if ($recipe->job_id == $quest->job->id)
						$items[] = [
							'id' => $requirement->id,
							'name' => $requirement->name,
							'icon' => $requirement->icon,
						];

			$quest->items = $items;

			$quests[$quest->job->abbr][] = $quest;
		}

		$job_ids = array_merge(Config::get('site.job_ids.crafting'), Config::get('site.job_ids.gathering'));
		$job_list = Job::whereIn('id', $job_ids)->get();

		return view('pages.quests', compact('quests', 'job_ids', 'job_list'));
	}

}

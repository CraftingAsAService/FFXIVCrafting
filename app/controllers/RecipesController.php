<?php

class RecipesController extends BaseController 
{

	public function getIndex()
	{
		// All Jobs
		$job_list = array();
		foreach (Job::all() as $j)
			$job_list[$j->abbreviation] = $j->name;

		$random_list = Recipe::with('item', 'job')
			->orderBy(DB::raw('RAND()'))
			->paginate(10);

		return View::make('recipes')
			->with('active', 'recipes')
			->with('list', $random_list)
			->with('job_list', $job_list);
	}

	public function postSearch()
	{
		$name = Input::get('name');
		$min = Input::get('min') ?: 1;
		$max = Input::get('max') ?: 70;
		$class = Input::get('class') ?: 'all';
		$per_page = Input::get('per_page') ?: 10;

		if ( ! in_array($per_page, array(10, 25, 50)))
			$per_page = 10;

		if ( ! is_numeric($min))
			$min = 1;
		if ( ! is_numeric($max))
			$max = 70;

		if ($min > $max)
			list($max, $min) = array($min, $max);

		if ($class && $class != 'all')
			$job = Job::where('abbreviation', $class)->first();

		$query = Recipe::with('item', 'job')
			->orderBy('name');
		
		if ($name)
			$query->where('name', 'like', '%' . $name . '%');

		if ($min && $max)
			$query->whereBetween('level', array($min, $max));

		if (isset($job))
			$query->where('job_id', $job->id);

		$recipes = $query->paginate($per_page);

		View::share('list', $recipes);
		View::share('per_page', $per_page);

		$output = array(
			'tbody' => View::make('recipes.results')->render(),
			'tfoot' => View::make('recipes.results_footer')->render()
		);

		return $output;
	}

}
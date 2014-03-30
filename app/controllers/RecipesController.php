<?php

class RecipesController extends BaseController 
{

	public function getIndex()
	{
		$random_list = Recipes::with('item', 'item.name')
			->orderBy(DB::raw('RAND()'))
			->paginate(10);

		return View::make('recipes')
			->with('active', 'recipes')
			->with('list', $random_list)
			->with('job_list', ClassJob::get_name_abbr_list());
	}

	public function postSearch()
	{
		$name = Input::get('name');
		$min = Input::get('min') ?: 1;
		$max = Input::get('max') ?: 70;
		$class = Input::get('class') ?: 'all';
		$per_page = Input::get('per_page') ?: 10;
		$sorting = Input::get('sorting') ?: 'name_asc';

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
		
		$sorting = explode('_', $sorting);
		$order_by = 'name'; $sort = 'asc';
		if (count($sorting) == 2)
		{
			// Only overwrite if need-be (i.e. don't test for "name" or "asc")

			if ($sorting[0] == 'level')
				$order_by = $sorting[0];

			if ($sorting[1] == 'desc')
				$sort = $sorting[1];
		}
		
		$query = Recipe::with('item', 'job')
			->orderBy($order_by, $sort);
		
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
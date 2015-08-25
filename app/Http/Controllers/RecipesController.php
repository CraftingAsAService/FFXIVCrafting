<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Config;

use App\Models\Garland\Job;
use App\Models\Garland\Recipe;
use App\Models\Garland\Item;

class RecipesController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		view()->share('active', 'recipes');
	}

	public function getIndex()
	{
		$list = Recipe::with('item')
			->orderBy(\DB::raw('RAND()'))
			->paginate(10);

		$crafting_job_list = Job::whereIn('id', Config::get('site.job_ids.crafting'))->get();
		$job_list = Job::lists('name', 'abbr')->all();

		$crafting_list_ids = array_keys(session('list', []));

		return view('recipes.index', compact('list', 'crafting_job_list', 'job_list', 'crafting_list_ids'));
	}

	public function getSearch(Request $request)
	{
		$input = $request->all();

		$name = $input['name'];
		$min = $input['min'] ?: 1;
		$max = $input['max'] ?: 90;
		$class = $input['class'] ?: 'all';
		$per_page = isset($input['per_page']) ? $input['per_page'] : 10;
		$sorting = $input['sorting'] ?: 'name_asc';

		if ( ! in_array($per_page, array(10, 25, 50)))
			$per_page = 10;

		if ( ! is_numeric($min))
			$min = 1;
		if ( ! is_numeric($max))
			$max = 90;

		if ($min > $max)
			list($max, $min) = [$min, $max];

		$job_list = Job::lists('name', 'abbr')->all();

		if ($class && $class != 'all')
			$job = Job::where('abbr', $class)->first();
			
		$sorting = explode('.', $sorting);
		$order_by = 'name'; $sort = 'asc';
		if (count($sorting) == 2)
		{
			// Only overwrite if need-be (i.e. don't test for "name" or "asc")

			if ($sorting[0] == 'recipe_level')
				$order_by = $sorting[0];

			if ($sorting[1] == 'desc')
				$sort = $sorting[1];
		}
		
		$query = Recipe::with('item');

		// We need this next bit for both an order by and a name search
		if ($order_by == 'name' || $name)
			$query
				->select('recipe.*') // Avoid selecting any data from the item table
				->join('item as i', 'i.id', '=', 'recipe.item_id');

		if ($order_by == 'name')
			$query->orderBy('i.name', $sort);

		if ($order_by != 'name')
			$query->orderBy($order_by, $sort);
		
		if ($name)
			$query->where('i.' . Item::getNameVarAttribute(), 'like', '%' . $name . '%');

		if ($min && $max)
			$query->whereBetween('recipe_level', [$min, $max]);

		if (isset($job))
			$query->where('job_id', $job->id);

		$list = $query->paginate($per_page);

		$crafting_list_ids = array_keys(session('list', []));

		view()->share(compact('list', 'per_page', 'crafting_list_ids'));

		$output = [
			'tbody' => view('recipes.results')->render(),
			'tfoot' => view('recipes.results_footer')->render()
		];

		return $output;
	}

}

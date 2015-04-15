<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Config;
use Session;

use App\Models\CAAS\ClassJob;
use App\Models\CAAS\Recipes;

class RecipesController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		view()->share('active', 'recipes');
	}

	public function getIndex()
	{
		$list = Recipes::with('item', 'item.name')
			->select('*', 'recipes.id AS recipe_id')
			->join('items AS i', 'i.id', '=', 'recipes.item_id')
			->join('translations AS t', 't.id', '=', 'i.name_' . Config::get('language'))
			->orderBy(\DB::raw('RAND()'))
			->paginate(10);

		$crafting_job_list = ClassJob::with('name', 'en_abbr')->whereIn('id', Config::get('site.job_ids.crafting'))->get();
		$job_list = ClassJob::get_name_abbr_list();

		$crafting_list_ids = array_keys(Session::get('list', []));

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

		$job_list = ClassJob::get_name_abbr_list();

		if ($class && $class != 'all')
			$classjob = ClassJob::get_by_abbr($class);
			
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
		
		$query = Recipes::with('item', 'item.name')
			->select('*', 'recipes.id AS recipe_id', 'recipes.level AS level')
			->join('items AS i', 'i.id', '=', 'recipes.item_id')
			->join('translations AS t', 't.id', '=', 'i.name_' . Config::get('language'));

		$query->orderBy($order_by == 'name' ? 't.term' : 'recipes.' . $order_by, $sort);
		
		if ($name)
			$query->whereHas('item', function($query) use ($name) {
				$query->whereHas('name', function($query) use ($name) {
					$query->where('term', 'like', '%' . $name . '%');
				});
			});

		if ($min && $max)
			$query->whereBetween('recipes.level', [$min, $max]);

		if (isset($classjob))
			$query->where('recipes.classjob_id', $classjob->id);

		$list = $query->paginate($per_page);

		$crafting_list_ids = array_keys(Session::get('list', []));

		view()->share(compact('list', 'per_page', 'crafting_list_ids'));

		$output = [
			'tbody' => view('recipes.results')->render(),
			'tfoot' => view('recipes.results_footer')->render()
		];

		return $output;
	}

}

<?php

namespace App\Http\Controllers\Api;

use App\Models\Notebookdivision;
use App\Models\NotebookdivisionCategories;
use Illuminate\Support\Facades\Cache;

class NotebookdivisionController extends Controller
{
	public function index()
	{
		return response()->json(Cache::rememberForever('NotebookdivisionController:index', function() {
			$categories = NotebookdivisionCategories::with('divisions')->get();

			$divisionMapping = function($n) {
				return [
					'id'   => $n->id,
					'name' => $n->name,
				];
			};

			return [
				'leveling' => [
					'id'   => 0,
					'name' => 'Leveling',
					'divisions' => Notebookdivision::where('category_id', 0)->where('id', '<=', config('site.max_level') / 5)->get()->map($divisionMapping),
				],
				'special' => $categories->map(function($c) use ($divisionMapping) {
					return [
						'id'   => $c->id,
						'name' => $c->name,
						'divisions' => $c->divisions->map($divisionMapping),
					];
				})->toArray(),
			];
		}));
	}
}

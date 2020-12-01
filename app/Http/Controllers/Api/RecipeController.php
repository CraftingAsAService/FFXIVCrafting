<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Recipe as RecipeResource;
use App\Models\Garland\Category;
use App\Models\Garland\Item;
use App\Models\Garland\Location;
use App\Models\Garland\Recipe;
use App\Models\Notebook;
use App\Models\Notebookdivision;
use DB;

class RecipeController extends Controller
{
	public function search()
	{
		$validatedData = request()->validate([
			'name'      => 'nullable|min:3|max:80',
			// 'sort'      => '',
			'levelMin' => 'numeric|min:1|max:' . config('site.max_level'),
			'levelMax' => 'numeric|min:1|max:' . config('site.max_level'),
			// 'stars'     => '',
			'notebooks' => 'array',
			'divisions' => 'array',
			'jobs'      => 'array',
			// 'page'      => '',
			'perpage'   => 'numeric|min:15|max:60',
		]);

		$query = Recipe::with('item.category')
			->select('recipe.*') // Avoid selecting any data from the item table
			->join('item as i', 'i.id', '=', 'recipe.item_id');

		if ($name = request('name'))
		{
			$name = preg_replace('/\*/', '%', $name);
			$query->where('i.' . Item::localized_name_variable(), 'like', '%' . $name . '%');
		}

		[$orderBy, $sort] = explode('.', request('sort'));
		if ($orderBy == 'name')
			$orderBy = 'i.' . Item::localized_name_variable();
		$query->orderBy($orderBy, $sort);

		$query->whereBetween('recipe_level', [request('levelMin'), request('levelMax')]);

		$stars = request('stars');
		if ($stars != 'any')
			$query->where('stars', $stars);

		if (request('jobs.Any') <= 0)
			$query->whereIn('job_id', array_values(request('jobs')));

		return RecipeResource::collection($query->simplePaginate(request('perpage')));
	}

	public function byNotebook($jobs, $notebookDivisions)
	{
		$notebookIds = Notebook::with('notebookdivisions')->whereHas('notebookdivisions', function($query) use ($notebookDivisions) {
			$query->whereIn('notebookdivision_id', explode(',', $notebookDivisions));
		})->pluck('id')->unique();

		$recipeIds = Recipe::whereIn('job_id', explode(',', $jobs))
			->whereHas('notebooks', function($query) use ($notebookIds) {
				$query->whereIn('notebook_id', $notebookIds);
			})->pluck('id')->unique()->toArray();

		$results = $this->tree($recipeIds);

		return response()->json($results);
	}

	public function show($id)
	{
		$recipe = Recipe::with('reagents')->findOrFail($id);

		return response()->json($this->packageRecipe($recipe));
	}

	public function packageRecipe($recipe)
	{
		return [
			'id'           => $recipe->id,
			'job_id'       => $recipe->job_id,
			'item_id'      => $recipe->item_id,
			'recipe_level' => $recipe->level,
			'stars'        => $recipe->stars,
			'yield'        => $recipe->yield,
			'reagents'     => $recipe->reagents->mapWithKeys(function($row) {
				return [$row->id => $row->pivot->amount];
			}),
		];
	}

	public $items = [],
	       $recipes = [],
	       $categories = [],
	       $nextRoundRecipes = [];

	public function tree($recipeIds)
	{
		static $coveredRecipeIds = [];
		static $loops = 0;
		$recipeIds = array_diff($recipeIds, $coveredRecipeIds);
		$coveredRecipeIds = array_merge($recipeIds, $coveredRecipeIds);
		$loops++;

		$recipes = Recipe::with('item', 'reagents', 'reagents.recipes')
			->whereIn('id', $recipeIds)
			->get();

		foreach ($recipes as $recipe)
			$this->registerRecipe($recipe);

		static $primaryRecipeIds = [];
		if (empty($primaryRecipeIds))
			$primaryRecipeIds = collect($this->recipes)->pluck('id');

		if ($this->nextRoundRecipes)
		{
			$nrr = $this->nextRoundRecipes;
			$this->nextRoundRecipes = [];
			return $this->tree($nrr);
		}
		else
		{
			$this->primaryRecipeIds = $primaryRecipeIds;
			return $this->packageEverything();
		}
	}

	public function registerRecipe($recipe)
	{
		if (isset($this->recipes[$recipe->id]))
			return;

		$this->recipes[$recipe->id] = $this->packageRecipe($recipe);

		$this->registerItem($recipe->item);

		foreach ($recipe->reagents as $reagent)
			$this->registerItem($reagent);
	}

	public function registerItem($item)
	{
		if (isset($this->items[$item->id]))
			return;

		$this->items[$item->id] = (new ItemController)->packageItem($item);

		foreach ($item->recipes as $recipe)
			$this->nextRoundRecipes[] = $recipe->id;
	}

	public function packageEverything()
	{
		$items = Item::with('shops.npcs', 'ventures.job_category.jobs', 'mobs', 'fishing.zone', 'fishing.area', 'nodes')
			->select('item.id', 'item.item_category_id', 'item_category.rank')
			->join('item_category', 'item_category.id', '=', 'item.item_category_id')
			->whereIn('item.id', array_keys($this->items))
			->get();

		// At the end, recursively get locations
		$locationsToLookup = collect([]);

		foreach ($items as $item)
		{
			$shops = $ventures = $mobs = $fishingSpots = $nodes = [];

			foreach ($item->shops as $shop)
				foreach ($shop->npcs as $npc)
				{
					$shops[] = [
						'name'   => $npc->name,
						'zone'   => $npc->zone_id,
						'coords' => $npc->x ? ceil($npc->x) . ' x ' . ceil($npc->y) : null,
					];

					$locationsToLookup[] = $npc->zone_id;
				}

			foreach ($item->ventures as $venture)
				$ventures[] = [
					'name'    => $venture->name,
					'amounts' => $venture->amounts,
					'jobIds'  => $venture->job_category->jobs->pluck('id')->toArray(),
					'level'   => $venture->level,
				];

			foreach ($item->mobs as $mob)
			{
				$mobs[] = [
					'name'  => $mob->name,
					'level' => $mob->level,
					'zone'  => $mob->zone_id,
				];

				$locationsToLookup[] = $mob->zone_id;
			}

			foreach ($item->fishing as $fishingSpot)
			{
				$fishingSpots[] = [
					'name'   => $fishingSpot->name,
					'level'  => $fishingSpot->level,
					// Name and the area->name are the same
					'zone'   => $fishingSpot->zone_id,
					'coords' => $fishingSpot->x ? ceil($fishingSpot->x) . ' x ' . ceil($fishingSpot->y) : null,
				];

				$locationsToLookup[] = $fishingSpot->zone_id;
			}

			foreach ($item->nodes as $node)
			{
				$nodes[] = [
					'name'   => $node->name,
					'type'   => $node->type,
					'level'  => $node->level,
					// Name and the area->name are the same
					'zone'   => $node->zone_id,
					'coords' => $node->coordinates,
					'timer'  => $node->timer,
				];

				$locationsToLookup[] = $node->zone_id;
			}

			$rank = $item->rank . '.' . str_pad($item->id, 8, 0, STR_PAD_LEFT);

			$this->items[$item->id] = array_merge($this->items[$item->id], compact('shops', 'ventures', 'mobs', 'fishingSpots', 'nodes', 'rank'));
		}

		$locations = [];
		$remainingLocations = $locationsToLookup->unique()->sort()->values()->toArray();
		while ( ! empty($remainingLocations))
		{
			$queriedLocations = Location::whereIn('id', $remainingLocations)->get();
			foreach ($queriedLocations as $location)
				$locations[$location->id] = [
					'name'      => $location->name,
					// For the root areas, it self-references; avoid that
					'parent'    => $location->location_id == $location->id ? null : $location->location_id,
				];

			$remainingLocations = $queriedLocations->pluck('location_id')->unique()->diff(array_keys($locations))->toArray();
		}

		function extendLocationName($location, $allLocations)
		{
			$parentName = $location['parent'] ? extendLocationName($allLocations[$location['parent']], $allLocations) : null;
			return ($parentName ? $parentName . ' — ' : '') . $location['name'];
		}

		foreach ($locations as &$location)
			$location['extendedName'] = extendLocationName($location, $locations);
		unset($location);

		$locations = collect($locations)->sortBy(function($location, $key) {
			$override = 'zzz';
			$firstChars = substr($location['extendedName'], 0, 8);
			if ($firstChars == 'La Nosce')     // La Noscea
				$override = 'aaa';
			elseif ($firstChars == 'Thanalan') // Thanalan
				$override = 'aab';
			elseif ($firstChars == 'The Blac') // Gridania/The Black Shroud
				$override = 'aac';
			return $override . ' ' . $location['extendedName'];
		})->toArray();

		$categoryIds = $items->pluck('item_category_id')->unique();
		$categories = Category::whereIn('id', $categoryIds)
			->orderBy('rank')
			->get()
			->mapWithKeys(function($category) {
				return [
					$category->id => [
						'id'   => $category->id,
						'name' => $category->name,
						'rank' => $category->rank,
					]
				];
			})->toArray();

		return [
			'sortMap'          => collect($this->items)->pluck('rank', 'id')->sort()->flip()->toArray(),
			'items'            => $this->items,
			'recipes'          => $this->recipes,
			'primaryRecipeIds' => $this->primaryRecipeIds,
			'categories'       => $categories,
			'locations'        => $locations,
			'locationSortMap'  => array_keys($locations),
		];
	}
}

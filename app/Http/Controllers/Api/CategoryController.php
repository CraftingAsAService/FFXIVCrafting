<?php

namespace App\Http\Controllers\Api;

use App\Models\Garland\Category;

class CategoryController extends Controller
{
	public function show($id)
	{
		$category = Category::findOrFail($id);

		return response()->json($this->packageCategory($category));
	}

	public function packageCategory($category) {
		return [
			'id'   => $category->id,
			'name' => $category->name,
			'rank' => $category->rank,
		];
	}
}

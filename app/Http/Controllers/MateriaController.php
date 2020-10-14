<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Garland\Item;
use App\Models\CAAS\Stat;

class MateriaController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		view()->share('active', 'materia');
	}

	public function getIndex()
	{
		// Items that are Materia
		$results = Item::with('attributes')
			->where('item_category_id', 58)
			->orderBy('id')
			->get();

		// Flatten materia list
		$materia_list = [];
		foreach ($results as $item)
		{
			preg_match('/^(.*)\sMateria\s(.*)$/', $item->name, $matches);

			list($ignore, $name, $power) = $matches;

			if ($item->attributes->count() == 0)
				continue;

			$attribute = $item->attributes[0];

			if ( ! isset($materia_list[$name]))
				$materia_list[$name] = [
					'icon' => Stat::name($attribute->attribute),
					'stat' => Stat::name($attribute->attribute),
					'power' => []
				];

			$materia_list[$name]['power'][$power] = [
				'item' => $item,
				'amount' => $attribute->amount
			];
		}

		return view('pages.materia', compact('materia_list'));
	}

}

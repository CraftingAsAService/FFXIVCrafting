<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\CAAS\Item;

class VendorsController extends Controller
{

	public function getView($id = 0)
	{
		abort(404);

		$item = Item::with('name', 'vendors', 'vendors.npc', 'vendors.npc.name', 'vendors.npc.location', 'vendors.npc.location.name')
			->where('id', $id)
			// ->remember(Config::get('site.cache_length'))
			->first();

		$vendors = [];

		foreach ($item->vendors as $vendor)
		{
			$npc = array(
				'id' => $vendor->npc->id,
				'name' => $vendor->npc->name->term,
				'color' => $vendor->pivot->color
			);

			foreach ($vendor->npc->location as $loc)
			{
				if ( ! isset($vendors[$loc->id]))
					$vendors[$loc->id] = array(
						'name' => $loc->name->term,
						'npcs' => []
					);

				$vendors[$loc->id]['npcs'][] = array_merge($npc,
					$loc->pivot->x
					? array(
						'coords' => array(
							'x' => $loc->pivot->x,
							'y' => $loc->pivot->y
						)
					)
					: []
				);
			}
		}

		ksort($vendors);

		return [
			'html' => view('partials.vendors-modal', compact('item', 'vendors'))->render()
		];
	}

}

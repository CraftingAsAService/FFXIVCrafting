<?php

namespace App\Http\Controllers\Api;

use App\Models\Garland\Item;

class ItemController extends Controller
{
	public function show($id)
	{
		$item = Item::with('recipes:id,item_id')->findOrFail($id);

		return response()->json($this->packageItem($item));
	}

	public function packageItem($item)
	{
		return [
			'id'          => $item->id,
			'name'        => $item->name,
			'price'       => $item->price,
			'gc_price'    => $item->gc_price,
			'special_buy' => !! $item->special_buy,
			'tradeable'   => $item->tradeable,
			'ilvl'        => $item->ilvl,
			'category_id' => $item->item_category_id,
			'rarity'      => $item->rarity,
			'icon'        => icon($item->icon),
			'recipes'     => $item->recipes->pluck('id')->toArray(),
		];
	}
}

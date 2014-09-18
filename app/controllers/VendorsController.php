<?php

class VendorsController extends BaseController 
{

	public function getView($id = 0)
	{
		$item = Item::with('name', 'vendors', 'vendors.npc', 'vendors.npc.name', 'vendors.npc.location', 'vendors.npc.location.name')
			->where('id', $id)
			// ->remember(Config::get('site.cache_length'))
			->first();

		$vendors = array();

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
						'npcs' => array()
					);

				$vendors[$loc->id]['npcs'][] = array_merge($npc, 
					$loc->pivot->x 
					? array(
						'coords' => array(
							'x' => $loc->pivot->x,
							'y' => $loc->pivot->y
						)
					)
					: array()
				);
			}
		}

		ksort($vendors);

		exit(json_encode(array(
			'html' => View::make('vendors.modal', array(
				'item' => $item,
				'vendors' => $vendors
			))->render()
		)));
	}

}
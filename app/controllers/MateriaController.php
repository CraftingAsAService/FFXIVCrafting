<?php

class MateriaController extends BaseController {

	public function getIndex()
	{
		// Items that are Materia
		$results = Slot::with(array('items', 'items.stats' => function($query) {
				$query->orderBy('item_stat.amount');
			}))
			->where('type', 'materia')
			->first();

		// Flatten materia list
		$materia = array();
		foreach ($results->items as $item)
			if (isset($item->stats[0]))
				$materia[$item->stats[0]->name][] = array(
					'name' => $item->name,
					'href' => $item->href,
					'amount' => rtrim(rtrim(rtrim($item->stats[0]->pivot->amount, '0'), '0'), '.')
				);

		return View::make('materia')
			->with('active', 'materia')
			->with('materia_list', $materia);
	}
}
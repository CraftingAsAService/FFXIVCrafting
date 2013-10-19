<?php

class MateriaController extends BaseController {

	public function getIndex()
	{
		// Items that are Materia
		$results = Item::with('stats')
			->where('role', 'materia')
			->orderBy('id')
			->get();

		// Flatten materia list
		$materia = array();
		foreach ($results as $row)
		{
			$stat = $row->stats->first();

			if ( ! $stat) 
				continue;

			preg_match('/^(.*)\sMateria\s(.*)$/', $row->name, $matches);
			
			list($ignore, $name, $power) = $matches;

			if ( ! isset($materia[$name]))
				$materia[$name] = array(
					'stat' => $stat->name,
					'power' => array()
				);

			$materia[$name]['power'][$power] = array(
				'id' => $row->id,
				'icon' => $row->icon,
				'amount' => $stat->pivot->amount
			);
		}

		// Let's move a few up front
		// First, Crafters, then Gatherers, then the rest (Battling)

		return View::make('materia')
			->with('active', 'materia')
			->with('materia_list', $materia);
	}
}
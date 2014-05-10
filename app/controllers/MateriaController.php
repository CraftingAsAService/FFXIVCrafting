<?php

class MateriaController extends BaseController
{

	public function getIndex()
	{
		// Items that are Materia
		$results = Item::with('name', 'en_name', 'baseparam', 'baseparam.name')
			->where('itemcategory_id', 13)
			->orderBy('id')
			->get();

		// Flatten materia list
		$materia = array();
		foreach ($results as $row)
		{
			preg_match('/^(.*)\sMateria\s(.*)$/', $row->en_name->term, $matches);
			
			list($ignore, $name, $power) = $matches;

			if ( ! isset($materia[$name]))
				$materia[$name] = array(
					'stat' => $row->baseparam[0]->name->term,
					'power' => array()
				);

			$materia[$name]['power'][$power] = array(
				'id' => $row->id,
				'amount' => $row->baseparam[0]->pivot->nq_amount
			);
		}

		// Let's move a few up front
		// First, Crafters, then Gatherers, then the rest (Battling)

		return View::make('pages.materia')
			->with('active', 'materia')
			->with('materia_list', $materia);
	}

}
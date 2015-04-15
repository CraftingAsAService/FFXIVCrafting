<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\CAAS\Item;

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
		$results = Item::with('name', 'en_name', 'baseparam', 'baseparam.name', 'baseparam.en_name')
			->where('itemcategory_id', 13)
			->orderBy('id')
			->get();

		// Flatten materia list
		$materia_list = array();
		foreach ($results as $row)
		{
			preg_match('/^(.*)\sMateria\s(.*)$/', $row->en_name->term, $matches);
			
			list($ignore, $name, $power) = $matches;

			if ( ! isset($materia_list[$name]))
				$materia_list[$name] = array(
					'icon' => $row->baseparam[0]->en_name->term,
					'stat' => $row->baseparam[0]->name->term,
					'power' => array()
				);

			$materia_list[$name]['power'][$power] = array(
				'id' => $row->id,
				'amount' => $row->baseparam[0]->pivot->nq_amount
			);
		}

		return view('pages.materia', compact('materia_list'));
	}

}

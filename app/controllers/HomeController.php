<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome()
	{

		// $x = Item::with('vendors', 'vendors.npc', 'vendors.npc.name', 'vendors.npc.location', 'vendors.npc.location.name')->find(5540);
		// d($x->vendors[0]->npc->name->term);
		// d($x->vendors[0]->npc->location[0]->name->term);
		// d($x->vendors[0]->npc->location);

		// $job = ClassJob::get_by_abbr('CRP');
		// #$a = Item::calculate($job->id, 6, 4, true, true);
		// $b = Item::calculate($job->id, 9, 0, 0, 0);

		// d(
		// 	// $a[6],
		// 	// $a[7],
		// 	// $a[8],
		// 	// $a[9],
		// 	$b[9]['Right Ring']['11'][0]->vendors
		// );


		############################
		#return View::make('_blank');
		############################

		return View::make('hello')
			->with('host_warning', preg_match('/thokk/', Request::getHost()));
	}

}
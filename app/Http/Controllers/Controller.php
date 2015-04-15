<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Session;

abstract class Controller extends BaseController {

	use DispatchesCommands, ValidatesRequests;

	public function __construct()
	{
		view()->share('active', '');
		
		view()->share('account', session('account'));
		view()->share('character_name', session('character_name', ''));
		view()->share('server', session('server', ''));
	}

}

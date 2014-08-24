<?php

use Illuminate\Routing\Controller;

class BaseController extends Controller
{

	public function __construct()
	{
		View::share('active', '');
		
		View::share('account', Session::get('account'));
		View::share('character_name', Session::get('character_name', ''));
		View::share('server', Session::get('server', ''));
	}

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

}
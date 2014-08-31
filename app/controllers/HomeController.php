<?php

class HomeController extends BaseController
{
	
	public function __construct()
	{
		parent::__construct();
		View::share('active', 'home');
	}

	public function showWelcome()
	{
		return View::make('index')
			->with('host_warning', preg_match('/thokk/', Request::getHost()));
	}

}
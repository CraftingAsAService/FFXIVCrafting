<?php

namespace App\Http\Controllers\Api;

abstract class Controller
{
	public function __construct()
	{
		// TODO this header() is dumb
		header('Access-Control-Allow-Origin: *');
	}
}

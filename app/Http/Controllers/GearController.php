<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class GearController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		view()->share('active', 'gear');
	}

	public function getIndex()
	{
		return view('gear.index');
	}

}

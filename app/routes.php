<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'HomeController@showWelcome');

Route::get('stats', function()
{
	return View::make('stats')
		->with('active', 'stats');
});

Route::controller('materia', 'MateriaController');
Route::controller('food', 'FoodController');

Route::get('calculate', function()
{
	// All Jobs
	$job_list = array();
	foreach (Job::all() as $j)
		$job_list[$j->abbreviation] = $j->name;

	return View::make('calculate')
		->with('error', FALSE)
		->with('active', 'calculate')
		->with('job_list', $job_list);
});

Route::get('equipment/{job}/{level}/{forecast}/{hindsight}', 'EquipmentController@calculate');
Route::get('equipment/{job}/{level}/{forecast}', 'EquipmentController@calculate');
Route::get('equipment/{job}/{level}', 'EquipmentController@calculate');
Route::get('equipment/{job}', 'EquipmentController@calculate');

Route::controller('equipment', 'EquipmentController');

Route::get('thanks', function()
{
	View::share('active', 'thanks');
	return View::make('thanks');
});
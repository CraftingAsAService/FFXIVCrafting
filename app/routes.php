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

Route::get('/', function()
{
	return View::make('hello');
});

Route::get('stats', function()
{
	View::share('active', 'stats');
	return View::make('stats');
});

Route::get('materia', function()
{
	View::share('active', 'materia');
	return View::make('materia');
});

Route::get('food', function()
{
	View::share('active', 'food');
	return View::make('food');
});

Route::get('equipment/{job}/{level}/{range}', 'EquipmentController@calculate');

Route::controller('equipment', 'EquipmentController');

Route::controller('datamine', 'DatamineController');
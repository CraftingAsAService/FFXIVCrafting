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

Route::controller('equipment', 'EquipmentController');

Route::controller('crafting', 'CraftingController');
Route::controller('gathering', 'GatheringController');
Route::controller('quests', 'QuestsController');
Route::controller('leve', 'LeveController');

Route::controller('list', 'ListController');
Route::controller('recipes', 'RecipesController');

Route::get('thanks', function()
{
	View::share('active', 'thanks');
	return View::make('thanks');
});

Route::get('credits', function()
{
	View::share('active', 'credits');
	return View::make('credits');
});

Route::get('about', function()
{
	View::share('active', 'about');
	return View::make('about');
});
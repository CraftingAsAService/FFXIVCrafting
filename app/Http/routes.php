<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'HomeController@index');

// Controllers

Route::controller('materia', 'MateriaController');
Route::controller('food', 'FoodController');
Route::controller('levequests', 'LevequestsController');
Route::controller('quests', 'QuestsController');
Route::controller('recipes', 'RecipesController');
Route::controller('career', 'CareerController');
Route::controller('gathering', 'GatheringController');
Route::controller('vendors', 'VendorsController');
Route::controller('list', 'ListController');
Route::controller('account', 'AccountController');
Route::controller('crafting', 'CraftingController');
Route::controller('map', 'MapController');
Route::controller('equipment', 'EquipmentController');

// Simpler Pages
foreach (['stats', 'report', 'thanks', 'credits'] as $page)
	Route::get($page, function() use ($page)
	{
		$active = $page;
		return view('pages.' . $page, compact('active'));
	});

// Blog used to exist, but now it's just a subreddit
Route::get('blog', function()
{
	return redirect('http://www.reddit.com/r/ffxivcrafting');
});

if (app()->environment('local'))
	Route::group(['prefix' => 'osmose'], function()
	{

		Route::get('/', 'Osmose\HomeController@index');
		Route::controller('libra', 'Osmose\LibraController');
		Route::controller('maps', 'Osmose\MapsController');
		Route::controller('icons', 'Osmose\IconsController');
		Route::controller('leves', 'Osmose\LevesController');

	});
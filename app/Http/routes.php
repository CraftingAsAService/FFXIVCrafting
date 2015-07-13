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

Route::controllers([
	'materia'		=> 'MateriaController',
	'food'			=> 'FoodController',
	'levequests'	=> 'LevequestsController',
	'quests'		=> 'QuestsController',
	'recipes'		=> 'RecipesController',
	'career'		=> 'CareerController',
	'gathering'		=> 'GatheringController',
	'vendors'		=> 'VendorsController',
	'list'			=> 'ListController',
	'account'		=> 'AccountController',
	'crafting'		=> 'CraftingController',
	'map'			=> 'MapController',
	'equipment'		=> 'EquipmentController',
	'gear'			=> 'GearController',
]);

// Old/Redirect Controllers

Route::any('leve', function() {
	return redirect('/levequests');
});

// Simpler Pages
foreach (['stats', 'report', 'thanks', 'credits'] as $page)
	Route::get($page, function() use ($page)
	{
		$active = $page;
		return view('pages.' . $page, compact('active'));
	});

// Blog used to exist, but now it's just a subreddit
Route::any('blog/{sluga?}/{slugb?}/{slugc?}/{slugd?}', function()
{
	return redirect('http://www.reddit.com/r/ffxivcrafting');
});

if (app()->environment('local'))
	Route::group(['prefix' => 'osmose'], function()
	{

		Route::get('/', 'Osmose\HomeController@index');
		Route::controllers([
			'libra' => 'Osmose\LibraController',
			'maps' => 'Osmose\MapsController',
			'icons' => 'Osmose\IconsController',
			'leves' => 'Osmose\LevesController',
			'garland' => 'Osmose\GarlandController',
		]);

	});
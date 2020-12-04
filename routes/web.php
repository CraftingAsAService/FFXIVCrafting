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

Route::get('entity/{item}/{type}', 'EntityController@show');

// Route::get('materia', 'MateriaController@getIndex');
Route::get('food', 'FoodController@getIndex');
Route::get('hunting', 'HuntingController@index');

Route::get('levequests', 'LevequestsController@index');
Route::get('levequests/advanced', 'LevequestsController@index'); # TODO REMOVEME, manages old redirect/page
Route::get('levequests/breakdown/{leve_id}', 'LevequestsController@breakdown');
Route::get('levequests/vs/{leveA}/{leveB}', 'LevequestsController@vs');

Route::get('gear', 'GearController@getIndex');
Route::get('gear/profile/{job?}/{start_level?}', 'GearController@getProfile');

Route::get('gathering', 'GatheringController@getIndex');
Route::get('gathering/list', 'GatheringController@getList');
Route::get('gathering/clusters/{id}', 'GatheringController@getClusters');
Route::get('gathering/beasts/{id}', 'GatheringController@getBeasts');

Route::get('vendors/view/{id}', 'VendorsController@getView');

Route::get('list', 'ListController@getIndex');
Route::post('list/add', 'ListController@postAdd');
Route::post('list/edit', 'ListController@postEdit');
Route::post('list/delete', 'ListController@postDelete');
Route::get('list/flush', 'ListController@getFlush');
Route::get('list/saved/{string}', 'ListController@getSaved');

Route::get('equipment', 'EquipmentController@getIndex');
Route::post('equipment', 'EquipmentController@postIndex');
Route::get('equipment/list', 'EquipmentController@getList');
Route::post('equipment/load', 'EquipmentController@postLoad');

Route::get('crafting/advanced', 'CraftingController@getAdvanced');
Route::get('crafting/list', 'CraftingController@getList');
Route::post('crafting/list', 'CraftingController@postList');
Route::get('crafting/by-class/{classes?}/{start?}/{end?}', 'CraftingController@getByClass');
Route::get('crafting/item/{item_id}', 'CraftingController@getItem');
Route::get('crafting/from-list', 'CraftingController@getFromList');
Route::get('crafting/{advanced?}', 'CraftingController@getIndex');
Route::post('crafting', 'CraftingController@postIndex');

Route::get('recipes', 'PagesController@recipes');

Route::get('stats', 'PagesController@stats');
Route::get('report', 'PagesController@report');
Route::get('thanks', 'PagesController@thanks');
Route::get('credits', 'PagesController@credits');

if (app()->environment('local'))
	Route::group(['prefix' => 'osmose'], function() {
		Route::get('/', 'Osmose\HomeController@index');

		Route::get('leves/crawl', 'Osmose\LevesController@getCrawl');
		Route::get('leves/compile', 'Osmose\LevesController@getCompile');

		Route::get('garland', 'Osmose\GarlandController@getIndex');
		Route::get('garland/view', 'Osmose\GarlandController@getView');

		Route::get('eorzea/crawl-names', 'Osmose\EorzeaController@getCrawlNames');
		Route::get('eorzea/parse-names', 'Osmose\EorzeaController@getParseNames');
		Route::get('eorzea/view-names', 'Osmose\EorzeaController@getViewNames');

	});
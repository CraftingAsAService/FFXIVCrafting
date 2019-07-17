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
// Route::get('quests', 'QuestsController@getIndex');

// Route::get('map', 'MapController@getIndex');
// Route::post('map', 'MapController@postIndex');

// Route::get('account', 'AccountController@getIndex');
// Route::get('account/login', 'AccountController@getLogin');
// Route::post('account/login', 'AccountController@postLogin');
// Route::get('account/refresh', 'AccountController@getRefresh');
// Route::get('account/logout', 'AccountController@getLogout');

Route::get('levequests', 'LevequestsController@getIndex');
Route::get('levequests/breakdown/{leve_id}', 'LevequestsController@getBreakdown');
Route::get('levequests/vs/{leveA}/{leveB}', 'LevequestsController@getVs');
Route::get('levequests/advanced', 'LevequestsController@getAdvanced');
Route::get('levequests/populate-advanced', 'LevequestsController@getPopulateAdvanced');

Route::get('gear', 'GearController@getIndex');
Route::get('gear/profile/{job?}/{start_level?}', 'GearController@getProfile');

// Route::get('career', 'CareerController@getIndex');
// Route::get('career/producer/{my_class?}/{supported_classes?}/{min_level?}/{max_level?}', 'CareerController@getProducer');
// Route::post('career/producer', 'CareerController@postProducer');
// Route::get('career/receiver/{my_class?}/{supported_classes?}/{min_level?}/{max_level?}', 'CareerController@getReceiver');
// Route::post('career/receiver', 'CareerController@postReceiver');
// Route::get('career/gathering/{my_class?}/{supported_classes?}/{min_level?}/{max_level?}', 'CareerController@getGathering');
// Route::post('career/gathering', 'CareerController@postGathering');

Route::get('gathering', 'GatheringController@getIndex');
Route::get('gathering/list', 'GatheringController@getList');
Route::get('gathering/clusters/{id}', 'GatheringController@getClusters');
Route::get('gathering/beasts/{id}', 'GatheringController@getBeasts');

Route::get('vendors/view/{id}', 'VendorsController@getView');

Route::get('recipes', 'RecipesController@getIndex');
Route::get('recipes/search', 'RecipesController@getSearch');

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

Route::get('stats', 'PagesController@stats');
Route::get('report', 'PagesController@report');
Route::get('thanks', 'PagesController@thanks');
Route::get('credits', 'PagesController@credits');

if (app()->environment('local'))
	Route::group(['prefix' => 'osmose'], function()
	{

		Route::get('/', 'Osmose\HomeController@index');

		Route::get('leves/crawl', 'Osmose\LevesController@getCrawl');
		Route::get('leves/compile', 'Osmose\LevesController@getCompile');

		Route::get('garland', 'Osmose\GarlandController@getIndex');
		Route::get('garland/view', 'Osmose\GarlandController@getView');

		Route::get('eorzea/crawl-names', 'Osmose\EorzeaController@getCrawlNames');
		Route::get('eorzea/parse-names', 'Osmose\EorzeaController@getParseNames');
		Route::get('eorzea/view-names', 'Osmose\EorzeaController@getViewNames');

	});

// Route::get('test', function () {
//     \Inspector\Laravel\Facades\Inspector::reportException(new Exception('Test'));
//     return "Inspector works";
// });
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

if (Config::get('database.log', false))
{           
    Event::listen('illuminate.query', function($query, $bindings, $time, $name)
    {
        $data = compact('bindings', 'time', 'name');

        // Format binding data for sql insertion
        foreach ($bindings as $i => $binding)
        {   
            if ($binding instanceof \DateTime)
            {   
                $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
            }
            else if (is_string($binding))
            {   
                $bindings[$i] = "'$binding'";
            }   
        }       

        // Insert bindings into query
        $query = str_replace(array('%', '?'), array('%%', '%s'), $query);
        $query = vsprintf($query, $bindings); 

        Log::info($query, $data);
    });
}

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
Route::controller('quests', 'QuestsController');
Route::controller('leve', 'LeveController');

Route::controller('list', 'ListController');
Route::controller('recipes', 'RecipesController');

Route::controller('career', 'CareerController');

Route::controller('map', 'MapController');


Route::controller('gathering', 'GatheringController');
Route::controller('vendors', 'VendorsController');

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
	return Redirect::to('/blog/about');
});
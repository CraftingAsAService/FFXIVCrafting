<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\NotebookdivisionController;
use App\Http\Controllers\Api\RecipeController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// TODO ADD CACHING MIDDLEWARE :D

Route::get('notebooks', [NotebookdivisionController::class, 'index']);

Route::get('recipes/{classes}/notebooks/{notebookIds}', [RecipeController::class, 'byNotebook']);
Route::get('recipes/tree/{recipeIds}', [RecipeController::class, 'tree']);

Route::get('category/{id}', [CategoryController::class, 'show']);
Route::get('recipe/{id}', [RecipeController::class, 'show']);
Route::get('item/{id}', [ItemController::class, 'show']);
Route::get('job/types/{type}', [JobController::class, 'types']);

Route::post('recipe/search', [RecipeController::class, 'search']);

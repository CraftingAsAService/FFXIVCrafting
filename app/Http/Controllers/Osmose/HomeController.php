<?php namespace App\Http\Controllers\Osmose;

use App\Models\Osmose\AppData;
use App\Models\Osmose\FileHandler;

class HomeController extends \App\Http\Controllers\Controller
{

	public function __construct()
	{
		view()->share('active', 'osmose');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		// the app_data table only has one row anyway
		// $app_data = AppData::first();
		
		// $tables = array_flip([
		// 	'ClassJob',
		// 	'ClassJobCategory',
		// 	'PlaceName',
		// 	'ItemUIKind',
		// 	'ItemUICategory',
		// 	'ItemCategory',
		// 	'ItemSeries',
		// 	'ItemSpecialBonus',
		// 	'BaseParam',
		// 	'Item',
		// 	'Race',
		// 	'BNpcName',
		// 	'ENpcResident',
		// 	'Shop',
		// 	'RecipeElement',
		// 	'NotebookDivision',
		// 	'Recipe', // Will handle CraftType as well
		// 	'GuardianDeity',

		// 	// Maybe's: Achievement, AchievementKind, AchievementCategory
		// 	// Quest, Quest_ClassJob
		// ]);

		// foreach ($tables as $table => $val)
		// 	$tables[$table] = FileHandler::get_version($table);

		return view('osmose.index');//, compact('app_data', 'tables'));
	}

}

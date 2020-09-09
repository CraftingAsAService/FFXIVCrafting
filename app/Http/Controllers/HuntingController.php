<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Cache;
use Config;

use App\Models\Garland\Item;
use App\Models\CAAS\Stat;

class HuntingController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		view()->share('active', 'hunting');
	}

	public function index()
	{
		$sections = [];

		$huntingData = \Cache::rememberForever('huntingData', function() {
			$huntingData = \Storage::get('hunting.tsv');
			$csv = new \ParseCsv\Csv();
			$csv->fields = ['class', 'image', 'rank', 'task', 'number', 'area', 'location'];
			$csv->offset = 1; // Ignore header
			$csv->auto($huntingData); // Auto detect delimiter and parse()
			return collect($csv->data)->sortBy('rank')->sortBy('task')->sortBy('location')->groupBy(function($row) {
				if (preg_match('/Thanalan/', $row['area']))
					return 'Thanalan';
				if (preg_match('/Shroud/', $row['area']))
					return 'Black Shroud';
				if (preg_match('/Noscea/', $row['area']))
					return 'La Noscea';
				return 'Other';
			})->map(function($row) {
				return $row->groupBy('area');
			});
		});

		$companies = [
			'IMF' => 'Immortal Flames',
			'MLS' => 'Maelstrom',
			'ORD' => 'Order of the Twin Adder',
		];

		$companiesKeys = array_keys($companies);

		return view('pages.hunting', compact('huntingData', 'companies', 'companiesKeys'));
	}

}

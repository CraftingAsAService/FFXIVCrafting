<?php

namespace App\Http\Controllers\Osmose;

use App\Models\Osmose\Eorzea;

class EorzeaController extends \App\Http\Controllers\Controller
{

	public function getCrawlNames()
	{
		Eorzea::scrape_names();

		flash()->success('I18N Names Scraping, this will take a while');

		return redirect()->back();
	}

	public function getParseNames()
	{
		Eorzea::parse_names();

		flash()->success('I18N Names Parsed, Ready for use during seeding');

		return redirect()->back();
	}

	public function getViewNames()
	{
		dd(json_decode(file_get_contents(storage_path() . '/app/osmose/i18n_names.json')));
	}

}
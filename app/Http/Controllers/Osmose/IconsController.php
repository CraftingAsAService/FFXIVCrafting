<?php

namespace App\Http\Controllers\Osmose;

use App\Models\Osmose\FileHandler;

class IconsController extends \App\Http\Controllers\Controller
{

	public function __construct()
	{
		view()->share('active', 'osmose');
	}

	public function getCrawl()
	{
		$downloaded = 0;

		$items = FileHandler::get_data('Item');

		$libra_base = 'http://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/';
		$img_path = base_path() . '/resources/assets/images/items/';

		$nq_scandir = array_diff(scandir($img_path . 'nq/'), array('.', '..'));
		$hq_scandir = array_diff(scandir($img_path . 'hq/'), array('.', '..'));

		foreach ($items as $item)
		{
			foreach (['nq', 'hq'] as $q)
			{
				if (is_null($item->icon->$q))
					continue;

				// $item->icon->$q == a6/a6586ae9dc19681d22f43aa1f3fdd6d9d104d4fc.png
				$filename = $item->id . '.png';
				$scandir = &${$q . '_scandir'};

				if (in_array($filename, $scandir))
					continue;

				// echo 'Downloading ' . $png . '<br>';
				$downloaded++;

				$ch=curl_init();
				curl_setopt($ch, CURLOPT_URL, $libra_base . $item->icon->$q);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_USERAGENT, 'Libra Eorzea');
				$output = curl_exec($ch);
				curl_close($ch);

				// echo 'Saving as ' . $img_path . $q . '/' . $filename . '<br>';

				file_put_contents($img_path . $q . '/' . $filename, $output);
			}
		}

		flash('Icons downloader finished.  Saved ' . $downloaded . ' files')->message();
		return redirect('/osmose');
	}

}
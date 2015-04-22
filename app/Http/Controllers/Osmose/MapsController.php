<?php namespace App\Http\Controllers\Osmose;

use App\Models\Osmose\FileHandler;

class MapsController extends \App\Http\Controllers\Controller
{

	public function __construct()
	{
		view()->share('active', 'osmose');
	}

	public function getBuild()
	{
		// Get list of maps
		$zones = file_get_contents('http://xivdb.com/modules/maps/js/zones.js');

		$zones = explode("\n", $zones);
		// First two and last two are junk
		$junk = [];
		$junk[] = array_shift($zones);
		$junk[] = array_shift($zones);
		$junk[] = array_pop($zones);
		$junk[] = array_pop($zones);

		$regions = [];
		foreach ($zones as $z)
		{
			$z = trim($z);
			preg_match('/^gmap\.MapDefines\[(\d+)\] = (.*)$/', $z, $matches);
			$id = $matches[1];
			$json = json_decode($matches[2]);

			if ( ! is_array($json))
				dd($json);

			$regions[$id] = $json;
		}

		foreach ($regions as &$region)
			foreach ($region as &$map)
				$map = $this->get_map($map);

		file_put_contents(FileHandler::path() . 'maps.json', json_encode($regions));

		flash()->message('Map JSON Builder finished');
		return redirect('/osmose');
	}

	private function get_map($json) 
	{
		$map_id = $json->MAPID;

		// Markers:
		// http://xivdb.com/modules/maps/php/get-marker.php

		// Fake POSTs

		// type:FIX
		// mapId:f1f3/00

		// type:MPOINT
		// mapId:f1f3/00

		// type:BPOINT
		// mapId:f1f3/00

		foreach (array('FIX', 'MPOINT', 'BPOINT') as $get)
		{
			$cached_folder = storage_path() . '/app/osmose/cache/maps/' . $json->MAPID;
			$cached_file = $cached_folder . '/' . $get . '.json';

			if ( ! is_file($cached_file))
			{
				\Log::info($json->MAPID . '|' . $get);

				$request = new \Jyggen\Curl\Request('http://xivdb.com/modules/maps/php/get-marker.php');
				
				$request->setOption(CURLOPT_HTTPHEADER, [
					'User-Agent: php:ffxivcrafting:v0.0.1 (by /u/tickthokk)', 
					'Content-type: application/x-www-form-urlencoded; charset=UTF-8',
					'X-Requested-With: XMLHttpRequest'
				]);
				$request->setOption(CURLOPT_FOLLOWLOCATION, true);
				$request->setOption(CURLOPT_POST, true);
				$request->setOption(CURLOPT_POSTFIELDS, [
					'type' => $get,
					'mapId' => $json->MAPID
				]);
				$request->execute();

				if ($request->isSuccessful()) {
				    $response = $request->getResponse();
				} else {
				    throw new Exception($resquest->getErrorMessage());
				}

				$json->$get = json_decode($response->getContent())->message;
				
				// Create the folder if it doesn't exist
				if ( ! is_dir($cached_folder))
					exec('mkdir -p ' . $cached_folder);
				file_put_contents($cached_file, $json->$get);
			}
			else
				$json->$get = file_get_contents($cached_file);
		}

		// Let's get Images
		// 1st level zoom, don't need that much detail
		// Try to get all images.  
		//  At 4th level zoom 15 was highest (starting at 0), so we'll go with that and break out as appropriate
		// http://xivdbimg.zamimg.com/modules/maps/Tiles/f1f3/00/4_0_0.png
		// http://xivdbimg.zamimg.com/modules/maps/Tiles/f1f3/00/4_15_15.png
		$path = 'http://xivdbimg.zamimg.com/modules/maps/Tiles/';
		$map_path = base_path() . '/resources/assets/maps/original/';
		$zoom = '1';

		if ( ! is_dir($map_path . $json->MAPID))
		{	
			@mkdir($map_path . explode('/', $json->MAPID)[0]);
			mkdir($map_path . $json->MAPID);
		}

		foreach (range(0,15) as $i)
		{
			$had_file = false;

			foreach (range(0,15) as $j)
			{
				// i then j gets us up to down
				// j then i gets us left to right
				$filename = $zoom . '_' . $i . '_' . $j . '.png';
				$savename = $zoom . '_' . $j . '_' . $i . '.png'; // Imagemagick will work better if we swap these numbers around
				$img_path = $path . $json->MAPID . '/' . $filename;
				$save_path = $map_path . $json->MAPID . '/' . $savename;

				if (is_file($save_path))
				{
					$had_file = true;
					continue;
				}

				// File exists? Check for 404
				// if (substr(get_headers($img_path)[0], 9, 3) == '404')
				// 	break;

				$img = @file_get_contents($img_path);
				
				if ($img === false) // Alternative to 404 checking
					break;

				file_put_contents($save_path, $img);

				$had_file = true;
			}

			if ( ! $had_file)
				break;
		}

		return $json;
	}

	public function getCompile()
	{
		$maps = json_decode(file_get_contents(FileHandler::path() . 'maps.json'));
		$zoom = '1';

		// https://www.google.com/search?q=image+magick+grid+combine&oq=image+magick+grid+combine
		// http://gotofritz.net/blog/geekery/combining-images-imagemagick/

		// convert *.png -gravity south -splice 0x111 -shave 0x111 -resize 400x400  converted.png
		// montage converted*.png -mode concatenate  -tile 2x2  output.png

		$map_base_path = base_path() . '/resources/assets/maps/';
		$compiled_folder = $map_base_path . 'compiled/';

		foreach ($maps as $id => $sub_maps)
		{
			#if ($id != 54) continue; // TESTING

			foreach ($sub_maps as $map)
			{
				$original_folder = $map_base_path . 'original/' . $map->MAPID . '/';
				$compiled_name = strtolower(str_replace(' ', '-', $map->ZONE . '-' . $map->REGION)) . '-' . str_replace('/', '-', $map->MAPID) . '.png';

				if (count($map->BPOINT) + count($map->MPOINT) != 0)
					$compiled_name = '_' . $compiled_name;
				
				$x = $y = 0;

				foreach (range(0,15) as $i)
				{
					$had_file = false;

					foreach (range(0,15) as $j)
					{
						// i then j gets us up to down
						// j then i gets us left to right
						$filename = $zoom . '_' . $i . '_' . $j . '.png';

						if ( ! is_file($original_folder . $filename))
							break;

						$had_file = true;
						$y = $j;
					}

					if ( ! $had_file)
						break;

					$x = $i;
				}

				$x++;
				$y++;

				exec('montage ' . $original_folder . '*.png -mode concatenate -tile ' . $x . 'x' . $y . ' ' . $compiled_folder . $compiled_name);
			}
		}

		flash()->message('Map Image Compiler finished');
		return redirect('/osmose');
	}

}
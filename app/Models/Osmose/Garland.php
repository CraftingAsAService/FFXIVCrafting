<?php

namespace App\Models\Osmose;

use Cache;

class Garland
{
    
	static public function scrape()
	{
		$core = Garland::get_core();

		file_put_contents(storage_path() . '/app/osmose/garland-data-core.json', json_encode($core));
	}

	static private function get_core()
	{
		return Cache::remember('garland.core', 30, function()
		{
			$core_url = 'http://www.garlandtools.org/db/js/gt.data.core.js';
			$core_contents = Garland::curl($core_url);
			$core_array = array_diff(explode("\n", Garland::binary_fix($core_contents)), ['']);

			$core = [];
			foreach ($core_array as $row)
			{
				// Key winds up as `gt.something.something`
				list($keys, $value) = explode(' = ', $row);

				// Key Cleanup
				// Drop the `gt.`
				$keys = preg_replace('/^gt\./', '', $keys);
				// And any JS var declaration
				$keys = preg_replace('/^var\s/', '', $keys);
				// Make the [id] part of the key into a dot notation
				$keys = preg_replace('/\[(.*)\]/', '.$1', $keys);

				// Value Cleanup
				// Drop the semicolon
				$value = substr($value, 0, -1);
				if (in_array(substr($value, 0, 1), ['{', '[']))
					$value = json_decode($value);

				array_set($core, $keys, $value);
			}

			return $core;
		});
	}

	static private function json_cleaner($content)
	{
		// http://stackoverflow.com/questions/17219916/json-decode-returns-json-error-syntax-but-online-formatter-says-the-json-is-ok
		for ($i = 0; $i <= 31; ++$i) { 
		    $content = str_replace(chr($i), "", $content); 
		}
		$content = str_replace(chr(127), "", $content);

		// This is the most common part
		Garland::binary_fix($content);

		return $content;
	}

	static private function binary_fix($string)
	{
		// Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
		// here we detect it and we remove it, basically it's the first 3 characters 
		if (0 === strpos(bin2hex($string), 'efbbbf')) {
		   $string = substr($string, 3);
		}
		return $string;
	}

	static private function curl($url)
	{
		// Reset timeout on each iteration
		set_time_limit(0);

		// Get the website information
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 0);

		// Thank you, we'll take that!
		$results = curl_exec($ch);

		// Cleanup
		curl_close($ch);
		unset($ch);

		return $results;
	}

}

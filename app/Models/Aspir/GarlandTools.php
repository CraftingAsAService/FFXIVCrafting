<?php

/**
 * GarlandTools
 * 	Parse data manually provided from GarlandTools
 * 	Legitimate data ends with v5.0, but historically it should be accurate for a good while
 */

namespace App\Models\Aspir;

use Cache;

class GarlandTools
{

	public $aspir;

	private $path;

	public function __construct(&$aspir)
	{
		$this->path = base_path() . '/../garlandtools/db/data/';

		$this->aspir =& $aspir;
	}

	public function mob()
	{
		$this->loopEndpoint('mob', function($data) {
			$id = (int) $data->mob->id % 10000000000;

			$this->aspir->setData('mob', [
				'quest'   => $data->mob->quest ?? null,
				'level'   => $data->mob->lvl,
				'zone_id' => $data->mob->zoneid,
			], $id);

			// And now for dropped items
			foreach ($data->mob->drops as $item_id)
				$this->aspir->setData('item_mob', [
					'item_id' => $item_id,
					'mob_id' => $data->mob->id,
				]);
		});
	}

	public function npc()
	{



				// 'zone_id' => null, // Filled in later
				// 'approx'  => null, // Filled in later
				// 'x'       => null, // Filled in later
				// 'y'       => null, // Filled in later
	}










	private function loopEndpoint($endpoint, $callback)
	{
		foreach ($this->getFileList($endpoint) as $file)
			$callback($this->getJSONData($file, $endpoint));
	}

	private function getFileList($endpoint, $language = 'en')
	{
		return array_diff(scandir($this->path . $language . '/' . $endpoint), ['.', '..']);
	}

	private function getJSONData($filename, $endpoint, $language = 'en')
	{
		$file = $this->path . $language . '/' . $endpoint . '/' . $filename;
		return $this->getCleanedJson($file);
	}

	private function getCleanedJson($path, $debug = false)
	{
		$content = file_get_contents($path);

		// http://stackoverflow.com/questions/17219916/json-decode-returns-json-error-syntax-but-online-formatter-says-the-json-is-ok
		for ($i = 0; $i <= 31; ++$i)
			$content = str_replace(chr($i), "", $content);

		$content = str_replace(chr(127), "", $content);

		// This is the most common part
		$content = $this->binaryFix($content);

		return json_decode($content);
	}

	private function binaryFix($string)
	{
		// Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
		// here we detect it and we remove it, basically it's the first 3 characters
		if (0 === strpos(bin2hex($string), 'efbbbf'))
		   $string = substr($string, 3);

		return $string;
	}

}
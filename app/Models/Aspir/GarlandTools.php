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

	public function mobs()
	{
		$this->loopEndpoint('mob', function($data) {
			$mobId = $this->translateMobID($data->mob->id);

			$this->aspir->setData('mob', [
				'quest'   => $data->mob->quest ?? null,
				'level'   => $data->mob->lvl,
				'zone_id' => $data->mob->zoneid,
			], $mobId);

			// And now for dropped items
			foreach ($data->mob->drops as $itemId)
				$this->aspir->setData('item_mob', [
					'item_id' => $itemId,
					'mob_id'  => $mobId,
				]);
		});
	}

	public function npcs()
	{
		$this->loopEndpoint('npc', function($data) {
			$this->aspir->setData('npc', [
				'zone_id' => $data->npc->zoneid ?? null,
				'approx'  => $data->npc->approx ?? null,
			], $data->npc->id);

			if (isset($data->npc->coords))
				$this->aspir->setData('npc', [
					'x' => $data->npc->coords[0],
					'y' => $data->npc->coords[1],
				], $data->npc->id);
		});
	}

	public function instances()
	{
		$this->loopEndpoint('instance', function($data) {
			if (isset($data->instance->fights))
				foreach ($data->instance->fights as $f)
				{
					if (isset($f->coffer))
						foreach ($f->coffer->items as $itemId)
							$this->aspir->setData('instance_item', [
								'item_id'     => $itemId,
								'instance_id' => $data->instance->id,
							]);

					foreach ($f->mobs as $mobID)
						$this->aspir->setData('instance_mob', [
							'mob_id'      => $this->translateMobID($mobID),
							'instance_id' => $data->instance->id,
						]);
				}

			if (isset($data->instance->rewards))
				foreach ($data->instance->rewards as $itemId)
					$this->aspir->setData('instance_item', [
						'item_id'     => $itemId,
						'instance_id' => $data->instance->id,
					]);

			if (isset($data->instance->coffers))
				foreach ($data->instance->coffers as $coffer)
					foreach ($coffer->items as $itemId)
						$this->aspir->setData('instance_item', [
							'item_id'     => $itemId,
							'instance_id' => $data->instance->id,
						]);
		});
	}

	private function translateMobID($mobId, $base = false)
	{
		// The mob id can be split between base and name
		if ($base)
			return (int) ($mobId / 10000000000);
		return (int) ($mobId % 10000000000);
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
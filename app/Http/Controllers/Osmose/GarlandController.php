<?php 

namespace App\Http\Controllers\Osmose;


class GarlandController extends \App\Http\Controllers\Controller
{
	protected $ffxivcrafting_repo = 'ffxivcrafting',
				$aspir_repo = 'aspir';

	public function getIndex()
	{
		// We want the storage path in another repository
		// It exists at the same level as this repository
		// Just replace the names
		$storage_path = preg_replace('/' . $this->ffxivcrafting_repo . '/', $this->aspir_repo, storage_path());
		$garland_path = $storage_path . '/garland';
		$data_path = $garland_path . '/db/data';

		$core = json_decode(file_get_contents($garland_path . '/core.json'));

		


		dd($core);
	}

}
<?php

namespace App\Models\CAAS;

use Illuminate\Database\Eloquent\Model;

class PlaceName extends _LibraBasic
{

	protected $table = 'place_name';

	public function region()
	{
		return $this->belongsTo('App\Models\CAAS\PlaceName', 'region');
	}

}
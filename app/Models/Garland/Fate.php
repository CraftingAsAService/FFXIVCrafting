<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class Fate extends Model {

	protected $table = 'fate';

	public function location()
	{
		return $this->belongsTo('App\Models\Garland\Location', 'zone_id');
	}

}

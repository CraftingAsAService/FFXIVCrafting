<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class Mob extends Model {

	protected $table = 'mob';

	public function items()
	{
		return $this->belongsToMany('App\Models\Garland\Item');
	}

	public function instances()
	{
		return $this->belongsToMany('App\Models\Garland\Instance');
	}

	public function location()
	{
		return $this->belongsTo('App\Models\Garland\Location', 'zone_id');
	}

}

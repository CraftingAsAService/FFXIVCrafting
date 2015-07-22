<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class Instance extends Model {

	protected $table = 'instance';

	public function items()
	{
		return $this->belongsToMany('App\Models\Garland\Item');
	}

	public function mobs()
	{
		return $this->belongsToMany('App\Models\Garland\Mob');
	}

	public function location()
	{
		return $this->belongsTo('App\Models\Garland\Location', 'zone_id');
	}

}

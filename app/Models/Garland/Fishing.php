<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class Fishing extends Model {

	protected $table = 'fishing';

	public function items()
	{
		return $this->belongsToMany('App\Models\Garland\Item')->withPivot('level');
	}

	public function zone()
	{
		return $this->belongsTo('App\Models\Garland\Location', 'zone_id');
	}

	public function area()
	{
		return $this->belongsTo('App\Models\Garland\Location', 'area_id');
	}
}

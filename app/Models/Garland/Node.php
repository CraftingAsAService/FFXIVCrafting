<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class Node extends Model {

	protected $table = 'node';

	public function items()
	{
		return $this->belongsToMany('App\Models\Garland\Item');
	}

	public function zone()
	{
		return $this->belongsTo('App\Models\Garland\Location', 'zone_id');
	}

	public function area()
	{
		return $this->belongsTo('App\Models\Garland\Location', 'area_id');
	}

	public function bonuses()
	{
		return $this->belongsTo('App\Models\Garland\NodeBonuses', 'bonus_id');
	}

}

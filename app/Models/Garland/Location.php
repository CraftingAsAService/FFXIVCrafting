<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class Location extends Model {

	protected $table = 'location';

	public function node_zones()
	{
		return $this->hasMany('App\Models\Garland\Node', 'zone_id');
	}

	public function node_areas()
	{
		return $this->hasMany('App\Models\Garland\Node', 'area_id');
	}

	public function fishing_zones()
	{
		return $this->hasMany('App\Models\Garland\Fishing', 'zone_id');
	}

	public function fishing_areas()
	{
		return $this->hasMany('App\Models\Garland\Fishing', 'area_id');
	}

	public function mobs()
	{
		return $this->hasMany('App\Models\Garland\Mob', 'zone_id');
	}

	public function location()
	{
		return $this->belongsTo('App\Models\Garland\Location');
	}

	public function npcs()
	{
		return $this->hasMany('App\Models\Garland\Npc', 'zone_id');
	}

}

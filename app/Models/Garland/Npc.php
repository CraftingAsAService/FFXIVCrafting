<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class Npc extends Model {

	protected $table = 'npc';

	public function bases()
	{
		return $this->belongsToMany('App\Models\Garland\NpcBase');
	}

	public function quests()
	{
		return $this->belongsToMany('App\Models\Garland\Quest');
	}

	public function shops()
	{
		return $this->belongsToMany('App\Models\Garland\Shop');
	}

	public function location()
	{
		return $this->belongsTo('App\Models\Garland\Location', 'zone_id');
	}

}

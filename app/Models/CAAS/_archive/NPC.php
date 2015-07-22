<?php

namespace App\Models\CAAS;

use Illuminate\Database\Eloquent\Model;

class NPC extends _LibraBasic
{

	protected $table = 'npcs';

	protected $guarded = ['id'];

	public function location()
	{
		return $this->belongsToMany('App\Models\CAAS\PlaceName', 'npcs_place_name', 'npcs_id', 'placename_id')->withPivot('x', 'y', 'levels', 'triggered');
	}

	public function items()
	{
		return $this->belongsToMany('App\Models\CAAS\Item', 'npcs_items', 'npcs_id', 'item_id');
	}

}
<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model {

	protected $table = 'shop';
	
	public function npcs()
	{
		return $this->belongsToMany('App\Models\Garland\Npc');
	}

	public function items()
	{
		return $this->belongsToMany('App\Models\Garland\Item');
	}

	public function name()
	{
		return $this->belongsTo('App\Models\Garland\ShopName');
	}

}

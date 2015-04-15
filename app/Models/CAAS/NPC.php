<?php namespace App\Models\CAAS;

use Illuminate\Database\Eloquent\Model;

class NPC extends _LibraBasic
{

	protected $table = 'npcs';

	public function location()
	{
		return $this->belongsToMany('App\Models\CAAS\PlaceName', 'npcs_place_name', 'npcs_id', 'placename_id')->withPivot('x', 'y', 'levels', 'triggered');
	}

}
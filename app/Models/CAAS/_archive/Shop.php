<?php

namespace App\Models\CAAS;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{

	protected $table = 'npcs_shops';

	public function npc()
	{
		return $this->belongsTo('App\Models\CAAS\NPC', 'npcs_id', 'id');
	}

}
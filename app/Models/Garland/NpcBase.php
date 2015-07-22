<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class NpcBase extends Model {

	protected $table = 'npc_base';

	public function npcs()
	{
		return $this->belongsToMany('App\Models\Garland\Npc');
	}

}

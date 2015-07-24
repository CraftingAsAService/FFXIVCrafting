<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class NodeBonuses extends Model {

	protected $table = 'node_bonuses';

	public function nodes()
	{
		return $this->hasMany('App\Models\Garland\Node', 'bonus_id');
	}

}

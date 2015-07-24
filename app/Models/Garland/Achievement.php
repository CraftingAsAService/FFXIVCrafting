<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model {

	protected $table = 'achievement';

	public function item()
	{
		return $this->belongsTo('App\Models\Garland\Item');
	}

}

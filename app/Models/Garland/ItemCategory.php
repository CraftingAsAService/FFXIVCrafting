<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model {

	protected $table = 'item_category';

	public function items()
	{
		return $this->hasMany('App\Models\Garland\Item');
	}

}

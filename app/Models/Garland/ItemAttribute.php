<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class ItemAttribute extends Model {
	
	protected $table = 'item_attribute';

	public function item()
	{
		return $this->belongsTo('App\Models\Garland\Item');
	}

}

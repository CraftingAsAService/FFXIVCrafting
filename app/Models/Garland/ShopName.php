<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class ShopName extends Model {

	protected $table = 'shop_name';

	public function shops()
	{
		return $this->hasMany('App\Models\Garland\Shop');
	}
}

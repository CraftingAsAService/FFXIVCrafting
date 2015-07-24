<?php

namespace App\Models\CAAS;

use Illuminate\Database\Eloquent\Model;

class ItemUIKind extends _LibraBasic
{

	protected $table = 'item_ui_kind';

	public function category()
	{
		return $this->hasMany('App\Models\CAAS\ItemUICategory', 'itemuikind_id');
	}

}
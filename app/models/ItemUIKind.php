<?php

class ItemUIKind extends _LibraBasic
{

	protected $table = 'item_ui_kind';

	public function category()
	{
		return $this->hasMany('ItemUICategory', 'itemuikind_id');
	}

}
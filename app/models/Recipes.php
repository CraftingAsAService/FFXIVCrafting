<?php

class Recipes extends _LibraBasic
{

	protected $table = 'recipes';

	public function item()
	{
		return $this->belongsTo('Item');
	}

	public function classjob()
	{
		return $this->belongsTo('ClassJob');
	}

	public function reagents()
	{
		return $this->belongsToMany('Item', 'recipe_reagents', 'recipe_id', 'item_id')->withPivot('amount');
	}

}
<?php namespace App\Models\CAAS;

use Illuminate\Database\Eloquent\Model;

class Recipes extends _LibraBasic
{

	protected $table = 'recipes';

	public function item()
	{
		return $this->belongsTo('App\Models\CAAS\Item');
	}

	public function classjob()
	{
		return $this->belongsTo('App\Models\CAAS\ClassJob');
	}

	public function reagents()
	{
		return $this->belongsToMany('App\Models\CAAS\Item', 'recipe_reagents', 'recipe_id', 'item_id')->withPivot('amount');
	}

}
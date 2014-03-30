<?php

class xRecipe extends Eloquent
{

	protected $table = 'recipes';
	public $timestamps = false;

	public function reagents()
	{
		return $this->belongsToMany('Item')->withPivot('amount');
	}

	public function job()
	{
		return $this->belongsTo('Job');
	}

	public function item()
	{
		return $this->belongsTo('Item');
	}

}
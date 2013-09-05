<?php

class Food extends Eloquent
{

	protected $table = 'food';
	public $timestamps = false;

	public function stats()
	{
		return $this->belongsToMany('Stat')->withPivot('percent', 'maximum');
	}

}
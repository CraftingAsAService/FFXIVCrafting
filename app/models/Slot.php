<?php

class Slot extends Eloquent
{

	protected $table = 'slots';
	public $timestamps = false;

	public function items()
	{
		return $this->hasMany('Item');
	}

}
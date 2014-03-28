<?php

class xVendor extends Eloquent
{

	protected $table = 'vendors';
	public $timestamps = false;

	public function items()
	{
		return $this->belongsToMany('Item');
	}

	public function location()
	{
		return $this->belongsTo('Location');
	}

}
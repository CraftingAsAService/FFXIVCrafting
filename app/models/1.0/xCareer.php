<?php

class xCareer extends Eloquent
{

	protected $table = 'careers';
	public $timestamps = false;

	public function item()
	{
		return $this->belongsTo('Item', 'identifier');
	}

	public function recipe()
	{
		return $this->belongsTo('Recipe', 'identifier');
	}

	public function job()
	{
		return $this->belongsToMany('Job')->withPivot('amount');
	}

}

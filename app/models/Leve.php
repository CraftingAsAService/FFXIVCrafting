<?php

class Leve extends Eloquent
{

	protected $table = 'leves';
	public $timestamps = false;

		//id, name, job_id, item_id, level, amount, xp, gil, triple, type, major_location_id, minor_location_id, location_id, 	

	public function major()
	{
		return $this->belongsTo('Location', 'major_location_id');
	}

	public function minor()
	{
		return $this->belongsTo('Location', 'minor_location_id');
	}

	public function location()
	{
		return $this->belongsTo('Location');
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
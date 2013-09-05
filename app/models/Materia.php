<?php

class Materia extends Eloquent
{

	protected $table = 'materia';
	public $timestamps = false;

	public function job()
	{
		return $this->belongsTo('Job');
	}

	public function stat()
	{
		return $this->belongsTo('Stat');
	}

}
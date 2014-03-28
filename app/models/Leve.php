<?php

class Leve extends Eloquent
{

	protected $table = 'leves';
	public $timestamps = false;

	public function classjob()
	{
		return $this->belongsTo('ClassJob');
	}

	public function item()
	{
		return $this->belongsTo('Item');
	}

}
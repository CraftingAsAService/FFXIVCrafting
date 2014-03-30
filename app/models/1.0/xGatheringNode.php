<?php

class xGatheringNode extends Eloquent
{

	protected $table = 'gathering_nodes';
	public $timestamps = false;

	public function location()
	{
		return $this->belongsTo('Location');
	}

	public function job()
	{
		return $this->belongsTo('Job');
	}

}
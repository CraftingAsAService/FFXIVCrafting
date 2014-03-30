<?php

class ClusterNodes extends Eloquent
{

	protected $table = 'cluster_nodes';
	public $timestamps = false;

	public function cluster()
	{
		return $this->belongsTo('ClassJob');
	}

}
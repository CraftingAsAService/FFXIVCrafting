<?php

class Cluster extends Eloquent
{

	protected $table = 'clusters';
	public $timestamps = false;

	public function items()
	{
		return $this->belongsToMany('Item', 'cluster_items', 'cluster_id', 'item_id');
	}

	public function classjob()
	{
		return $this->belongsTo('ClassJob');
	}

	public function nodes()
	{
		return $this->hasMany('ClusterNodes');
	}

	public function location()
	{
		return $this->belongsTo('PlaceName', 'placename_id');
	}

}
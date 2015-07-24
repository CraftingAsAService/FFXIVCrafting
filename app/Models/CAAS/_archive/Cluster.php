<?php

namespace App\Models\CAAS;

use Illuminate\Database\Eloquent\Model;

class Cluster extends Model
{

	protected $table = 'clusters';
	public $timestamps = false;

	public function items()
	{
		return $this->belongsToMany('App\Models\CAAS\Item', 'cluster_items', 'cluster_id', 'item_id');
	}

	public function classjob()
	{
		return $this->belongsTo('App\Models\CAAS\ClassJob');
	}

	public function nodes()
	{
		return $this->hasMany('App\Models\CAAS\ClusterNodes');
	}

	public function location()
	{
		return $this->belongsTo('App\Models\CAAS\PlaceName', 'placename_id');
	}

}
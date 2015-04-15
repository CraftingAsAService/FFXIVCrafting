<?php namespace App\Models\CAAS;

use Illuminate\Database\Eloquent\Model;

class ClusterNodes extends Model
{

	protected $table = 'cluster_nodes';
	public $timestamps = false;

	public function cluster()
	{
		return $this->belongsTo('App\Models\CAAS\ClassJob');
	}

}
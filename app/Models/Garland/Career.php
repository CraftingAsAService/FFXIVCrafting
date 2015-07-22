<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class Career extends Model {

	protected $table = 'career';

	public function scopeOfType($query, $type)
	{
		return $query->whereType($type);
	}
	
	public function item()
	{
		return $this->belongsTo('App\Models\Garland\Item', 'identifier');
	}
	
	public function recipe()
	{
		return $this->belongsTo('App\Models\Garland\Recipe', 'identifier');
	}
	
	public function job()
	{
		return $this->belongsToMany('App\Models\Garland\Job')->withPivot('amount');
	}

}

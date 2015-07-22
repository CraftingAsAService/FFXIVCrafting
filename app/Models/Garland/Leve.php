<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class Leve extends Model {
	
	protected $table = 'leve';

	public function items()
	{
		return $this->belongsToMany('App\Models\Garland\Item');
	}

	public function location()
	{
		return $this->belongsTo('App\Models\Garland\Location', 'area_id');
	}

	public function job_category()
	{
		return $this->belongsTo('App\Models\Garland\JobCategory');
	}

}

<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class Venture extends Model {
	
	protected $table = 'venture';

	public function items()
	{
		return $this->belongsToMany('App\Models\Garland\Item');
	}

	public function job_category()
	{
		return $this->belongsTo('App\Models\Garland\JobCategory');
	}

}

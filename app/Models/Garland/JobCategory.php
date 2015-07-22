<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model {

	protected $table = 'job_category';

	public function jobs()
	{
		return $this->belongsToMany('App\Models\Garland\Job');
	}

	public function ventures()
	{
		return $this->hasMany('App\Models\Garland\Venture');
	}

	public function leves()
	{
		return $this->hasMany('App\Models\Garland\Leve');
	}

	public function items()
	{
		return $this->hasMany('App\Models\Garland\Item');
	}

}

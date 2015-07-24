<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class Leve extends Model {
	
	protected $table = 'leve';

	public function requirements()
	{
		return $this->belongsToMany('App\Models\Garland\Item', 'leve_required')->withPivot('amount');
	}

	public function rewards()
	{
		return $this->belongsToMany('App\Models\Garland\Item', 'leve_reward')->withPivot('amount', 'rate');
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

<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class Job extends Model {

	protected $table = 'job';

	public function categories()
	{
		return $this->belongsToMany('App\Models\Garland\JobCategory');
	}

	public function recipe()
	{
		return $this->hasMany('App\Models\Garland\Recipe');
	}

	static public function get_by_type($type)
	{
		return Job::whereIn('id', config('site.job_ids')[$type])->get();
	}

	static public function get_by_abbr($abbr)
	{
		return Job::where('abbr', $abbr)->first();
	}

}

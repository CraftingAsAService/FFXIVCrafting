<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

class Quest extends Model {

	protected $table = 'quest';

	public function rewards()
	{
		return $this->belongsToMany('App\Models\Garland\Item', 'quest_reward');
	}

	public function requirements()
	{
		return $this->belongsToMany('App\Models\Garland\Item', 'quest_required');
	}

	public function npcs()
	{
		return $this->belongsToMany('App\Models\Garland\Npc');
	}

	public function location()
	{
		return $this->belongsTo('App\Models\Garland\Location', 'zone_id');
	}

	public function job_category()
	{
		return $this->belongsTo('App\Models\Garland\JobCategory');
	}

}

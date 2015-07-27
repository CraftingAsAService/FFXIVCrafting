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

	public function getTypeAttribute()
	{
		// Types are a little complicated.
		// Types are separated by either being In or Out of a main location
		$main_locations = [27, 39, 51]; // Limsa, Ul'dah, Gridania
		// Combined with a Plate
		$plates = [
			'Courier' => [80034], // && Not in Main
			'Field' => [80034, 80041], // && Not in Main
			'Reverse Courier' => [80034], // && in Main
			'Town' => [80033, 80041, 80045, 80057], // && in Main, 45 & 57 are for FSH
		];

		if (in_array($this->area_id, $main_locations))
		{
			foreach (['Town', 'Reverse Courier'] as $type)
				if (in_array($this->plate, $plates[$type]))
					return $type;
		}
		else
		{
			foreach (['Courier', 'Field'] as $type)
				if (in_array($this->plate, $plates[$type]))
					return $type;
		}
	}

}

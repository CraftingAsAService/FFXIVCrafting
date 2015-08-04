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

	public function getSimpleTypeAttribute()
	{
		// Types are a little complicated.

		// Combined with a Plate
		// $plates = [
		// 	'Courier' => [80034], // && Not in Main && Ingenuity
		// 	'Field' => [80034, 80041], // && Not in Main && Constancy
		// 	'Reverse Courier' => [80034], // && in Main && Ingenuity
		// 	'Town' => [80033, 80041, 80045, 80057], // && in Main && Constancy, 45 & 57 are for FSH
		// ];
		
		// Types are separated by either being In or Out of a main location
 		// 							  Limsa, Ul'dah, Gridania
		if (in_array($this->area_id, [27, 39, 51])) 
		{
			if ($this->type == 'Ingenuity')
				return 'Reverse Courier';
			elseif ($this->type == 'Constancy' || $this->type == 'Charity')
				return 'Town';
		}
		else
		{
			if ($this->type == 'Ingenuity')
				return 'Courier';
			elseif ($this->type == 'Constancy' || $this->type == 'Charity')
				return 'Field';
		}

		return 'Unknown';
	}

}

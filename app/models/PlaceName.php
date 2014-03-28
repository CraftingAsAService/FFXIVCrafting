<?php

class PlaceName extends _LibraBasic
{

	protected $table = 'place_name';

	public function region()
	{
		return $this->belongsTo('PlaceName', 'region');
	}

}
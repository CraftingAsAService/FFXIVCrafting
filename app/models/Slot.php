<?php

class Slot extends Eloquent
{

	protected $table = 'slots';
	public $timestamps = false;

	public function items()
	{
		return $this->hasMany('Item');
	}

	public static function common()
	{
		return Slot::where('type', 'equipment')
			->orderBy('rank')
			->remember(Config::get('site.cache_length'))
			->get();
	}

}
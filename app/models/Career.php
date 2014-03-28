<?php

class Career extends Eloquent
{

	protected $table = 'careers';

	public function scopeOfType($query, $type)
	{
		return $query->whereType($type);
	}

	public function classjob()
	{
		return $this->belongsToMany('ClassJob', 'career_classjob', 'career_id', 'classjob_id')->withPivot('amount');
	}

	public function recipe()
	{
		return $this->hasOne('Recipe', 'id', 'identifier');
	}

}
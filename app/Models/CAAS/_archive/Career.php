<?php

namespace App\Models\CAAS;

use Illuminate\Database\Eloquent\Model;

class Career extends Model
{

	protected $table = 'careers';

	public function scopeOfType($query, $type)
	{
		return $query->whereType($type);
	}

	public function classjob()
	{
		return $this->belongsToMany('App\Models\CAAS\ClassJob', 'career_classjob', 'career_id', 'classjob_id')->withPivot('amount');
	}

	public function recipe()
	{
		return $this->hasOne('Recipe', 'id', 'identifier');
	}

}
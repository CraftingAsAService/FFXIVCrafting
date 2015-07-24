<?php

namespace App\Models\CAAS;

use Illuminate\Database\Eloquent\Model;

class Leve extends Model
{

	protected $table = 'leves';
	public $timestamps = false;

	public function classjob()
	{
		return $this->belongsTo('App\Models\CAAS\ClassJob');
	}

	public function item()
	{
		return $this->belongsTo('App\Models\CAAS\Item');
	}

}
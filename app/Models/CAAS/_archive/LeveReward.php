<?php

namespace App\Models\CAAS;

use Illuminate\Database\Eloquent\Model;

class LeveReward extends Model
{

	protected $table = 'leve_rewards';
	public $timestamps = false;

	public function item()
	{
		return $this->belongsTo('App\Models\CAAS\Item');
	}

	public function classjob()
	{
		return $this->belongsTo('App\Models\CAAS\ClassJob');
	}

}
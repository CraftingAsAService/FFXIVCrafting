<?php

namespace App\Models\CAAS;

use Illuminate\Database\Eloquent\Model;

class QuestItem extends Model
{

	protected $table = 'quest_items';
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
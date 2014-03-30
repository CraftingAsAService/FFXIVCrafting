<?php

class LeveReward extends Eloquent
{

	protected $table = 'leve_rewards';
	public $timestamps = false;

	public function item()
	{
		return $this->belongsTo('Item');
	}

	public function classjob()
	{
		return $this->belongsTo('ClassJob');
	}

}
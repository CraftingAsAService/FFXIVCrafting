<?php

class QuestItem extends Eloquent
{

	protected $table = 'quest_items';
	public $timestamps = false;

	public function item()
	{
		return $this->belongsTo('Item');
	}

	public function job()
	{
		return $this->belongsTo('Job');
	}

}
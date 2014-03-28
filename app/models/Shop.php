<?php

class Shop extends Eloquent
{

	protected $table = 'npcs_shops';

	public function npc()
	{
		return $this->belongsTo('NPC', 'npcs_id', 'id');
	}

}
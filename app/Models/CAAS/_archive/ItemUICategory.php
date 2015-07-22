<?php

namespace App\Models\CAAS;

use Illuminate\Database\Eloquent\Model;

class ItemUICategory extends _LibraBasic
{

	protected $table = 'item_ui_category';

	public function kind()
	{
		return $this->hasOne('App\Models\CAAS\ItemUIKind', 'id', 'itemuikind_id');
	}

	public static function two_handed_weapon_ids()
	{
		return array(
			1,	// Pugilist's Arm
			3,	// Marauder's Arm
			4,	// Archer's Arm
			5,	// Lancer's Arm
			7,	// Two-handed Thaumaturge's Arm
			9,	// Two-handed Conjurer's Arm
			10, // Arcanist's Grimoire
			32, // Fisher's Primary
			84, // Rogue's Arms
		);
	}

}
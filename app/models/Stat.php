<?php

class Stat extends Eloquent
{

	protected $table = 'stats';
	public $timestamps = false;

	public static function avoid($job = 'CRP')
	{
		$hand_land = array('Control', 'CP', 'Craftsmanship', 'Gathering', 'GP', 'Perception');
		$melee_set = array_merge(array('Intelligence', 'Mind', 'Spell Speed'), $hand_land);
		$magic_set = array_merge(array('Strength', 'Dexterity', 'Skill Speed', 'Parry'), $hand_land);

		$avoid = array(
			'ARC' => $melee_set,
			'GLA' => $melee_set,
			'PGL' => $melee_set,
			'MRD' => $melee_set,
			'LNC' => $melee_set,

			'BRD' => $melee_set,
			'PLD' => $melee_set,
			'WAR' => $melee_set,
			'DRG' => $melee_set,
			'MNK' => $melee_set,

			'CNJ' => array_merge(array('Intelligence'), $magic_set),
			'SCH' => array_merge(array('Mind'), $magic_set),
			'THM' => array_merge(array('Mind'), $magic_set),

			'WHM' => $magic_set,
			'BLM' => $magic_set,
			'SMN' => $magic_set,
			'SCH' => $magic_set,
		);

		return isset($avoid[$job]) ? $avoid[$job] : array();
	}

	public static function focus($job = 'CRP')
	{

		$look_for = array('CRP');

		$shortcuts = array(
			'DOH' => 'CRP,BSM,ARM,GSM,LTW,WVR,ALC,CUL',
			'DOL' => 'MIN,BTN,FSH',
			'DOW' => 'GLA,MRD,LNC,PGL,ARC,PLD,WAR,DRG,MNK,BRD',
			'DOM' => 'CNJ,THM,ACN,SCH,SMN,BLM,WHM',
			'DPS' => 'LNC,PGL,DRG,MNK', // Melee DPS
			'RDPS' => 'BRD,ARC', // Ranged DPS
			'MDPS' => 'THM,BLM,ACN,SMN', // Magical DPS
			'Heals' => 'CNJ,SCH,WHM',
			'Tanks' => 'GLA,MRD,PLD,WAR',
		);

		foreach ($shortcuts as $role => $classes)
			if (in_array($job, explode(',', $classes)))
				$look_for[] = $role;

		$benefactors = array(
			// Everyone Benefits
			'Materia' => 'DOH,DOL,DOW,DOM',

			// Disciples of the Hand
			'Control' => 'DOH',
			'CP' => 'DOH',
			'Craftsmanship' => 'DOH',

			// Disciples of the Land
			'Gathering' => 'DOL',
			'GP' => 'DOL',
			'Perception' => 'DOL',

			// DPS and Tanks
				
			'Accuracy' => 'DPS,RDPS,MDPS',
			'Critical Hit Rate' => 'DPS,RDPS,MDPS',
			'Determination' => 'DPS,Tanks',

			'Delay' => 'DPS,RDPS',

			'Defense' => 'Tanks',
			'Magic Defense' => 'Tanks',
			'Vitality' => 'Tanks,CRP,ARM,LTW,MIN',

			'Skill Speed' => 'DPS,RDPS,Tanks',
			'Physical Damage' => 'DPS,RDPS',
			'Auto-Attack' => 'DPS,RDPS',
			'DPS' => 'DPS,RDPS',

			'Block Rate' => 'Tanks',
			'Block Strength' => 'Tanks',
			'Parry' => 'Tanks',

			'Strength' => 'Tanks,DPS,BSM,ARM,BTN',

			'Dexterity' => 'RDPS,Tanks,GSM,WVR,CRP,FSH',

			'Spell Speed' => 'MDPS,Heals',
			'Intelligence' => 'MDPS,Heals,ALC,GSM,LTW',
			'Magic Damage' => 'MDPS',
			'Mind' => 'Heals,CUL,BSM,WVR,MIN',
			'Piety' => 'Heals,RDPS,ALC,CUL,FSH',

			'Intelligence' => 'Heals,RDPS,ALC,GSM,LTW,BTN',

		);
		
		//  'Dexterity' => 'RDPS,Tanks,GSM,WVR,CRP,FSH',
		
		$focus = array();

		foreach ($benefactors as $stat => $roles)
			foreach($look_for as $job)
				if (in_array($job, explode(',', $roles)))
					$focus[] = $stat;

		$focus = array_unique($focus);
		sort($focus);

		return $focus;
	}

	public static function boring()
	{
		// A list of the "boring" stats
		return array(
			'Increased Spiritbond Gain',
			'Reduced Durability Loss',

			'Blind Resistance',
			'Blunt Resistance',
			'Heavy Resistance',
			'Ice Resistance',
			'Earth Resistance',
			'Fire Resistance',
			'Lightning Resistance',
			'Paralysis Resistance',
			'Piercing Resistance',
			'Poison Resistance',
			'Silence Resistance',
			'Slashing Resistance',
			'Sleep Resistance',
			'Water Resistance',
			'Wind Resistance',
		);
	}

}
<?php

namespace App\Models\CAAS;

class Stat
{

	public static function avoid($job = 'CRP')
	{
		$hand_land = ['Control', 'CP', 'Craftsmanship', 'Gathering', 'GP', 'Perception'];
		$melee_set = array_merge(array('Intelligence', 'Mind', 'Spell Speed'), $hand_land);
		$magic_set = array_merge(array('Strength', 'Dexterity', 'Skill Speed', 'Parry'), $hand_land);

		$avoid = [
			'ARC' => $melee_set,
			'GLA' => $melee_set,
			'PGL' => $melee_set,
			'MRD' => $melee_set,
			'LNC' => $melee_set,
			'ROG' => $melee_set,
			'MCH' => $melee_set,
			'DRK' => $melee_set,

			'BRD' => $melee_set,
			'PLD' => $melee_set,
			'WAR' => $melee_set,
			'DRG' => $melee_set,
			'MNK' => $melee_set,
			'NIN' => $melee_set,

			'CNJ' => $magic_set, // array_merge(array('Intelligence w/o Mind'), $magic_set),
			'SCH' => $magic_set, // array_merge(array('Mind'), $magic_set),
			'THM' => $magic_set, // array_merge(array('Mind w/o Intelligence'), $magic_set),

			'WHM' => $magic_set,
			'BLM' => $magic_set,
			'SMN' => $magic_set,
			'SCH' => $magic_set,
			'AST' => $magic_set,
		];

		return isset($avoid[$job]) ? $avoid[$job] : [];
	}

	public static function advanced_avoidance($job = 'CRP')
	{
		$avoid = [
			// Don't suggest pieces that have Intelligence unless they have Mind
			'CNJ' => ['Intelligence w/o Mind'],
			// THM: "Avoid Mind pieces without Intelligence"
			'THM' => ['Mind w/o Intelligence']
		];

		return isset($avoid[$job]) ? $avoid[$job] : [];
	}

	public static function primary($job = 'CRP')
	{
		$primaries = [
			// Giving DOH/DOL classes "Materia" as a skapegoat
			// because we don't want to give Control over Craftsmanship
			'Materia' => ['CRP', 'BSM', 'ARM', 'GSM', 'LTW', 'WVR', 'ALC', 'CUL', 'MIN', 'BTN', 'FSH'],
			'Vitality' => ['GLA', 'MRD', 'PLD', 'WAR', 'DRK'],
			'Strength' => ['LNC', 'PGL', 'DRG', 'MNK'],
			'Dexterity' => ['BRD', 'ARC', 'ROG', 'NIN', 'MCH'],
			'Intelligence' => ['THM', 'BLM', 'ACN', 'SMN'],
			'Mind' => ['CNJ', 'SCH', 'WHM', 'AST'],
		];

		foreach ($primaries as $stat => $jobs)
			if (in_array($job, $jobs))
				return $stat;

		return 'Materia';
	}

	public static function focus($job = 'CRP')
	{

		$look_for = [$job];

		$shortcuts = [
			'DOH' => 'CRP,BSM,ARM,GSM,LTW,WVR,ALC,CUL',
			'DOL' => 'MIN,BTN,FSH',
			'DOW' => 'GLA,MRD,LNC,PGL,ARC,PLD,WAR,DRG,MNK,BRD,ROG,NIN,MCH,DRK',
			'DOM' => 'CNJ,THM,ACN,SCH,SMN,BLM,WHM,AST',
			'DPS' => 'LNC,PGL,DRG,MNK,ROG,NIN', // Melee DPS
			'RDPS' => 'BRD,ARC,MCH', // Ranged DPS
			'MDPS' => 'THM,BLM,ACN,SMN', // Magical DPS
			'Heals' => 'CNJ,SCH,WHM,AST',
			'Tanks' => 'GLA,MRD,PLD,WAR,DRK',
		];

		foreach ($shortcuts as $role => $classes)
			if (in_array($job, explode(',', $classes)))
				$look_for[] = $role;

		$benefactors = [
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

			// Battle Classes

			'Determination' => 'DOW,DOM',

			'Accuracy' => 'DPS,RDPS,MDPS',
			'Critical Hit Rate' => 'DPS,RDPS,MDPS',

			'Delay' => 'DPS,RDPS',

			'Defense' => 'Tanks',
			'Magic Defense' => 'Tanks',
			'Vitality' => 'Tanks',//,CRP,ARM,LTW,MIN',

			'Skill Speed' => 'DPS,RDPS,Tanks',
			'Physical Damage' => 'DPS,RDPS',
			'Auto-Attack' => 'DPS,RDPS',
			'DPS' => 'DPS,RDPS',

			'Block Rate' => 'Tanks',
			'Block Strength' => 'Tanks',
			'Parry' => 'Tanks',

			'Strength' => 'Tanks,DPS',//,BSM,ARM,BTN',

			'Dexterity' => 'RDPS,Tanks',//,GSM,WVR,CRP,FSH',

			'Spell Speed' => 'MDPS,Heals',
			'Intelligence' => 'MDPS',//,ALC,GSM,LTW',
			'Magic Damage' => 'MDPS',
			'Mind' => 'Heals',//,CUL,BSM,WVR,MIN',
			'Piety' => 'Heals,RDPS',//,ALC,CUL,FSH',

		];

		$focus = [];

		foreach ($benefactors as $stat => $roles)
			foreach($look_for as $job)
				if (in_array($job, explode(',', $roles)))
					$focus[] = $stat;

		$focus = array_unique($focus);
		sort($focus);

		return $focus;
	}

	/**
	 * Gear Focus, just stick to 6 stats, tops
	 * @param  string $job  [description]
	 * @return array $focus [description]
	 */
	public static function gear_focus($job = 'CRP')
	{
		// TODO move into a config file
		$shortcuts = [
			'DOH' => 'CRP,BSM,ARM,GSM,LTW,WVR,ALC,CUL',
			'DOL' => 'MIN,BTN,FSH',
			'DOW' => 'GLA,MRD,LNC,PGL,ARC,PLD,WAR,DRG,MNK,BRD,ROG,NIN,MCH,DRK',
			'DOM' => 'CNJ,THM,ACN,SCH,SMN,BLM,WHM,AST',
			'DPS' => 'LNC,PGL,DRG,MNK,ROG,NIN', // Melee DPS
			'RDPS' => 'BRD,ARC,MCH', // Ranged DPS
			'STR-DPS' => 'LNC,PGL,DRG,MNK',
			'DEX-DPS' => 'BRD,ARC,ROG,NIN,MCH',
			'MDPS' => 'THM,BLM,ACN,SMN', // Magical DPS
			'Heals' => 'CNJ,SCH,WHM,AST',
			'Tanks' => 'GLA,MRD,PLD,WAR,DRK',
		];

		$look_for = [$job];
		foreach ($shortcuts as $role => $classes)
			if (in_array($job, explode(',', $classes)))
				$look_for[] = $role;

		// TODO, move into a config file
		// The order these are defined in are important
		$benefactors = [
			// Disciples of the Hand
			'Craftsmanship' => ['DOH'],
			'Control' => ['DOH'],
			'CP' => ['DOH'],

			// Disciples of the Land
			'Gathering' => ['DOL'],
			'Perception' => ['DOL'],
			'GP' => ['DOL'],

			// Battle Classes

			'Strength' => ['Tanks','STR-DPS'],
			'Dexterity' => ['DEX-DPS'],
			'Intelligence' => ['MDPS'],
			'Mind' => ['Heals'],

			'Accuracy' => ['DOW','DOM'],
			'Critical Hit Rate' => ['DOW','DOM'],
			'Determination' => ['DOW','DOM'],
			'Skill Speed' => ['DOW'],
			'Spell Speed' => ['DOM'],

			'Vitality' => ['DOW','DOM'],

			'Parry' => ['Tanks'],
			'Piety' => ['DOM'],
		];

		$focus = [];

		foreach ($benefactors as $stat => $roles)
			foreach ($look_for as $job)
				if (in_array($job, $roles))
					$focus[] = $stat;

		return $focus;
	}

	public static function boring()
	{
		// A list of the "boring" stats
		return [
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
		];
	}

	static public $stat_conversion = [
			'Accuracy' => 'accuracy',
			'Bind Resistance' => 'bind_res',
			'Blind Resistance' => 'blind_res',
			'Block Rate' => 'block_rate',
			'Block Strength' => 'block_strength',
			'Blunt Resistance' => 'blunt_res',
			'Control' => 'control',
			'CP' => 'cp',
			'Craftsmanship' => 'craftsmanship',
			'Critical Hit Rate' => 'critical_rate',
			'Defense' => 'def',
			'Delay' => 'delay',
			'Determination' => 'determination',
			'Dexterity' => 'dexterity',
			'Physical Damage' => 'dmg',
			'Doom Resistance' => 'doom_res',
			'Reduced Durability Loss' => 'durability_loss',
			'Earth Resistance' => 'earth_res',
			'Fire Resistance' => 'fire_res',
			'Gathering' => 'gathering',
			'GP' => 'gp',
			'Heavy Resistance' => 'heavy_res',
			'Ice Resistance' => 'ice_res',
			'Intelligence' => 'intelligence',
			'Lightning Resistance' => 'lightning_res',
			'Magic Defense' => 'mdef',
			'Magic Damage' => 'mdmg',
			'Mind' => 'mind',
			'Morale' => 'morale',
			'Paralysis Resistance' => 'paralysis_res',
			'Parry' => 'parry',
			'Perception' => 'perception',
			'Petrification Resistance' => 'petrify_res',
			'Piercing Resistance' => 'pierce_res',
			'Piety' => 'piety',
			'Poison Resistance' => 'poison_res',
			'Silence Resistance' => 'silence_res',
			'Skill Speed' => 'skill_speed',
			'Slashing Resistance' => 'slash_res',
			'Sleep Resistance' => 'sleep_res',
			'Slow Resistance' => 'slow_res',
			'Spell Speed' => 'spell_speed',
			'Increased Spiritbond Gain' => 'spiritbond_gain',
			'Strength' => 'strength',
			'Stun Resistance' => 'stun_res',
			'Vitality' => 'vitality',
			'Water Resistance' => 'water_res',
			'Wind Resistance' => 'wind_res',

			'Careful Desynthesis' => 'careful_desynthesis',
		];

	public static function get_ids($stats, $preserve_order = false)
	{
		if (empty($stats))
			return [];

		$results = [];

		// if ( ! $preserve_order)
		// 	return BaseParam::with('en_name')
		// 	->whereHas('en_name', function($q) use ($stats) {
		// 		$q->whereIn('term', $stats);
		// 	})
		// 	->lists('id')->all();

		// foreach ($stats as $stat)
		// {
		// 	$r = BaseParam::with('en_name')
		// 		->whereHas('en_name', function($q) use ($stat) {
		// 			$q->where('term', $stat);
		// 		})
		// 		->first();
		// 	$results[] = isset($r->id) ? $r->id : 0;
		// }

		// Translate the full names into the slugs
		// Slugs will act as an "id" to avoid rewriting a bunch of stuff
		//  This was part of the Libra->Garland transition


		// Enfeebling Magic Potency
		// Enhancement Magic Potency
		// Enmity
		// Enmity Reduction
		// Evasion
		// Healing Magic Potency
		// HP
		// Magic Resistance
		// Movement Speed
		// MP
		// Projectile Resistance
		// Refresh
		// Regen
		// Spikes
		// TP
		foreach ($stats as $stat)
			if (isset(self::$stat_conversion[$stat]))
				$results[] = self::$stat_conversion[$stat];

		return $results;
	}

	static public function name($attribute)
	{
		$flip = array_flip(self::$stat_conversion);
		if ( ! isset($flip[$attribute]))
			dd($flip, $attribute);
		// dd(array_flip(self::$stat_conversion));//, $attribute);
		return $flip[$attribute];
	}

}
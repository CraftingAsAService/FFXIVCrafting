<?php

namespace App\Models\CAAS;

use App\Models\Garland\Item;
Use App\Models\Garland\Job;

class StatWeight
{

	/**
	 * Get the score of an item based on the stat weights of a job
	 */
	static public function get_score(Job $job, Item $item)
	{
		$weights = self::translate_weights($job->abbr);

		$score = 0;
		// Multi-slotted items' score should be adjusted to only account for that slot equally
		$division_factor = (isset($item->cannot_equip) ? count(explode(',', $item->cannot_equip)) : 0) + 1;

		foreach ($item->attributes as $attribute)
		{
			if ($attribute->quality != 'nq')
				continue;

			if (in_array($attribute->attribute, array_keys($weights)))
				$score += $weights[$attribute->attribute] * $attribute->amount;
		}

		return $score / $division_factor;
	}

	static public $translated_score = [];

	static public function translate_weights($job_abbr)
	{
		if (isset(self::$translated_score[$job_abbr]))
			return self::$translated_score[$job_abbr];

		// $weights = ;
		foreach (config('site.stat_weights') as $jobs => $weights)
		{
			if ( ! preg_match('/\b' . $job_abbr . '\b/', $jobs))
				continue;

			foreach ($weights as $key => $value)
			{
				if ( ! isset(self::$stat_conversion[$key]))
					continue;

				$weights[self::$stat_conversion[$key]] = $value;
				unset($weights[$key]);
			}

			break;
		}

		return $weights;
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

}
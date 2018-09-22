<?php

namespace App\Models\Garland;

use Illuminate\Database\Eloquent\Model;

use Config;
use App\Models\CAAS\Stat;
use App\Models\CAAS\StatWeight;

class Item extends Model {

	protected $table = 'item';

	public function fishing()
	{
		return $this->belongsToMany('App\Models\Garland\Fishing')->withPivot('level');
	}

	public function instances()
	{
		return $this->belongsToMany('App\Models\Garland\Instance');
	}

	public function mobs()
	{
		return $this->belongsToMany('App\Models\Garland\Mob');
	}

	public function nodes()
	{
		return $this->belongsToMany('App\Models\Garland\Node');
	}

	public function quest_rewards()
	{
		return $this->belongsToMany('App\Models\Garland\Quest', 'quest_reward')->withPivot('amount');
	}

	public function quest_required()
	{
		return $this->belongsToMany('App\Models\Garland\Quest', 'quest_required');
	}

	public function leve_rewards()
	{
		return $this->belongsToMany('App\Models\Garland\Leve', 'leve_reward')->withPivot('amount', 'rate');
	}

	public function leve_required()
	{
		return $this->belongsToMany('App\Models\Garland\Leve', 'leve_required')->withPivot('amount');
	}

	public function shops()
	{
		return $this->belongsToMany('App\Models\Garland\Shop');
	}

	public function ventures()
	{
		return $this->belongsToMany('App\Models\Garland\Venture');
	}

	public function reagent_of()
	{
		return $this->belongsToMany('App\Models\Garland\Recipe', 'recipe_reagents')->withPivot('amount');
	}

	public function recipes()
	{
		return $this->hasMany('App\Models\Garland\Recipe');
	}

	public function attributes()
	{
		return $this->hasMany('App\Models\Garland\ItemAttribute');
	}

	public function category()
	{
		return $this->belongsTo('App\Models\Garland\ItemCategory', 'item_category_id');
	}

	public function job_category()
	{
		return $this->belongsTo('App\Models\Garland\JobCategory');
	}

	public function achievements()
	{
		return $this->hasMany('App\Models\Garland\Achievement');
	}

	public function career()
	{
		return $this->hasMany('App\Models\Garland\Career', 'identifier');
	}

	public function getDisplayNameAttribute()
	{
		return $this->{self::localized_name_variable()};
	}

	public static function localized_name_variable()
	{
		return [
			'en' => '',
			'ja' => 'jp_',
			'de' => 'de_',
			'fr' => 'fr_',
		][Config::get('language')] . 'name';
	}

	public static function calculate($job_id = 0, $level = 1, $range = 0, $craftable_only = TRUE, $rewardable_too = TRUE)
	{
		// $cache_key = __METHOD__ . '|' . Config::get('language') . '|' . $job_id . ',' . $level . ',' . $range . ($craftable_only ? ('T' . ($rewardable_too ? 'T' : 'F')) : 'F');

		// // Does cache exist?  Return that instead
		// if (Cache::has($cache_key))
		// 	return Cache::get($cache_key);

		// Get the job IDs
		$job = Job::with('categories')->find($job_id);

		$equipment_list = array_flip(Config::get('site.equipment_roles'));
		array_walk($equipment_list, function(&$i) { $i = []; });

		// Slot data
		$slots = Config::get('site.defined_slots');
		$slot_alias = Config::get('site.slot_alias');
		$slot_cannot_equip = Config::get('site.slot_cannot_equip');
		foreach ($slot_cannot_equip as &$sce)
			foreach ($sce as &$ce)
				$ce = $slots[$ce];
		unset($sce, $ce);

		// Make sure the slot avoids pieces with certain stats
		$stat_ids_to_avoid = Stat::get_ids(Stat::avoid($job->abbr));
		$stat_ids_to_focus = Stat::focus($job->abbr);
		// $primary_stat = Stat::get_ids([Stat::primary($job->abbr)]);
		$boring_stat_ids = Stat::get_ids(Stat::boring());
		$advanced_stat_avoidance = Stat::advanced_avoidance($job->abbr);
		foreach ($advanced_stat_avoidance as &$ava)
		{
			// These are in a very specific order.
			// Keep that order in tact.
			list($a, $b) = explode(' w/o ', $ava);
			$ava[0] = Stat::get_ids(array($a))[0];
			$ava[1] = Stat::get_ids(array($b))[0];
		}
		unset($ava);

		// Get all items where:
		// Slot isn't zero
		// It's between the level & level - 10
		// The class can use it
		// craftable only?
		// rewardable?

		$job_category_ids = $job->categories->pluck('id')->all();

		$two_handed_weapon_ids = self::two_handed_weapon_ids();

		foreach ($slots as $slot_identifier => $slot_name)
		{
			$query = Item::with('attributes', 'shops', 'recipes', 'recipes.job', 'quest_rewards', 'leve_rewards', 'ventures', 'achievements')
				->where('slot', $slot_identifier)
				->whereBetween('elvl', array($level - 10, $level + $range))
				->whereIn('job_category_id', $job_category_ids)
				->whereHas('attributes', function($query) use ($stat_ids_to_focus) {
					$query->whereIn('attribute', $stat_ids_to_focus);
				})
				->orderBy('elvl', 'DESC')
				->orderBy('ilvl', 'DESC')
				->limit(20);

			if ($craftable_only && $rewardable_too)
				$query->where(function($query) {
					$query
						->whereHas('recipes', function($query) {
							$query->where('item_id', \DB::raw('item.id'));
						})
						->orHas('quest_rewards')
						->orHas('leve_rewards')
						->orHas('ventures')
						->orHas('achievements');
				});
			elseif ($craftable_only)
				$query->whereHas('recipes', function($query) {
					$query->where('item_id', \DB::raw('item.id'));
				});

			$items = $query->get();

			$slot = isset($slot_alias[$slot_identifier]) ? $slot_alias[$slot_identifier] : $slot_identifier;
			$role = $slots[$slot];

			foreach ($items as $item)
			{
				// Cannot equip attribute?
				if (isset($slot_cannot_equip[$item->slot]))
					$item->cannot_equip = implode(',', $slot_cannot_equip[$item->slot]);
				if (empty($item->cannot_equip) && in_array($item->item_category_id, $two_handed_weapon_ids))
					$item->cannot_equip = 'Off Hand';

				$item->score = StatWeight::get_score($job, $item);

				// // Kick it to the curb because of attributes?
				// // Compare the focused vs the avoids
				// $focus = $avoid = 0;
				// $param_count = []; // DISABLING FOR GARLAND // array_fill(1, 100, 0); // 73 total stats, 100's pretty safe, not to mention we only really focus on the first dozen
				// // $item->attributes is essentially a "reserved" keyword for laravel, so we need to access it through the relations
				// foreach ($item->relations['attributes'] as $attribute)
				// {
				// 	if ($attribute->quality != 'nq')
				// 		continue;

				// 	if ( ! isset($param_count[$attribute->attribute]))
				// 		$param_count[$attribute->attribute] = 1;
				// 	else
				// 		$param_count[$attribute->attribute]++;

				// 	if (in_array($attribute->attribute, $stat_ids_to_avoid))
				// 		$avoid++;
				// 	elseif (in_array($attribute->attribute, $stat_ids_to_focus))
				// 		$focus++;
				// }

				// if ($advanced_stat_avoidance)
				// 	foreach ($advanced_stat_avoidance as $ava)
				// 		// If the [0] stat exists, but the [1] stat doesn't, drop the piece completely
				// 		if (isset($param_count[$ava[0]]) && isset($param_count[$ava[1]]) && $param_count[$ava[0]] > 0 && $param_count[$ava[1]] == 0)
				// 			$avoid += 10; // Really sell that this should be avoided

				// if ($avoid >= $focus || $focus == 0)
				// 	continue;

				$equipment_list[$role][] = $item;
			}

			unset($items);
		}

		// Copy the Right Ring into the Left Ring
		$equipment_list['Left Ring'] = $equipment_list['Right Ring'];

		$leveled_equipment = [];

		// We now have a proper list, but now we need to widdle down further by ilvl
		foreach (range($level, $level + $range) as $l)
		{
			$leveled_equipment[$l] = [];
			foreach ($equipment_list as $role => $items)
			{
				$leveled_equipment[$l][$role] = [];

				// $max_elvl =
				$max_score = 0;

				// Find max
				foreach ($items as $item)
				{
					// $faux_ilvl = $item->ilvl;

					// foreach ($item->baseparam as $param)
					// 	if ($param->id == $primary_stat)
					// 	{
					// 		$faux_ilvl += 2; // Treat it as 2 ilvls higher
					// 		break;
					// 	}

					// if ($item->elvl <= $l && $item->elvl > $max_elvl)
					// 	$max_elvl = $item->elvl;

					if ($item->elvl <= $l && $item->score > $max_score)
						$max_score = $item->score;
				}

				$allowable_score = $max_score * .8;

				// Drop lesser items
				// OR figure out cannot equip stuff for weapons
				foreach ($items as $key => $item)
					if ($item->elvl <= $l && $item->score > $allowable_score)
					{
							//$item->cannot_equip = array_flip(Config::get('site.defined_slots'))['Off Hand'];

						if ( ! isset($leveled_equipment[$l][$role][$item->score]))
							$leveled_equipment[$l][$role][$item->score] = [];

						$leveled_equipment[$l][$role][$item->score][] = $item;
					}

				// Place highest ilvl first
				krsort($leveled_equipment[$l][$role]);

				// Re-key the list for good measure
				//$items = array_values($items);

				//$leveled_equipment[$l][$role] = $items;
			}
		}

		// We should mostly have a list of just single items now

		// Cache the results
		// Cache::put($cache_key, $leveled_equipment, Config::get('site.cache_length'));

		return $leveled_equipment;
	}

	public static function two_handed_weapon_ids()
	{
		return [
			1,	// Pugilist's Arm
			3,	// Marauder's Arm
			4,	// Archer's Arm
			5,	// Lancer's Arm
			7,	// Two-handed Thaumaturge's Arm
			9,	// Two-handed Conjurer's Arm
			10, // Arcanist's Grimoire
			32, // Fisher's Primary
			84, // Rogue's Arms
			87, // 'Dark Knight's Arm
			88, // 'Machinist's Arm
			89, // 'Astrologian's Arm
		];
	}

}

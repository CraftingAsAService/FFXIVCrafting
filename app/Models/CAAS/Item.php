<?php namespace App\Models\CAAS;

use Illuminate\Database\Eloquent\Model;

use Config;

class Item extends _LibraBasic
{

	protected $table = 'items';

	public function classjob()
	{
		return $this->belongsToMany('App\Models\CAAS\ClassJob', 'classjob_items', 'item_id', 'classjob_id');
	}

	public function recipe()
	{
		return $this->hasMany('App\Models\CAAS\Recipes');
	}

	public function baseparam()
	{
		return $this->belongsToMany('App\Models\CAAS\BaseParam', 'baseparam_items', 'item_id', 'baseparam_id')->withPivot('nq_amount', 'hq_amount', 'nq_limit', 'hq_limit', 'bonus');
	}

	public function vendors()
	{
		return $this->belongsToMany('App\Models\CAAS\Shop', 'items_npcs_shops', 'item_id', 'npcs_shop_id')->withPivot('color');
	}

	public function clusters()
	{
		return $this->belongsToMany('App\Models\CAAS\Cluster', 'cluster_items', 'item_id', 'cluster_id');
	}

	public function beasts()
	{
		return $this->belongsToMany('App\Models\CAAS\NPC', 'npcs_items', 'item_id', 'npcs_id');
	}
	
	#select `classjob`.*, `classjob_items`.`classjob_id` as `pivot_classjob_id`, `classjob_items`.`item_id` as `pivot_item_id` from `classjob` inner join `classjob_items` on `classjob`.`id` = `classjob_items`.`item_id` where `classjob_items`.`classjob_id` in ('2222')

	// public function stats()
	// {
	// 	return $this->belongsToMany('Stat')->withPivot('amount', 'maximum');
	// }

	// public function jobs()
	// {
	// 	return $this->belongsToMany('Job');
	// }

	// public function recipes()
	// {
	// 	return $this->hasMany('Recipe');
	// }

	public function quest()
	{
		return $this->hasMany('App\Models\CAAS\QuestItem');
	}

	public function leve()
	{
		return $this->hasMany('App\Models\CAAS\Leve');
	}

	// public function nodes()
	// {
	// 	return $this->belongsToMany('GatheringNode')->orderBy('level');
	// }

	// public function vendors()
	// {
	// 	return $this->belongsToMany('Vendor');
	// }

	public static function calculate($job_id = 0, $level = 1, $range = 0, $craftable_only = TRUE, $rewardable_too = TRUE)
	{
		// $cache_key = __METHOD__ . '|' . Config::get('language') . '|' . $job_id . ',' . $level . ',' . $range . ($craftable_only ? ('T' . ($rewardable_too ? 'T' : 'F')) : 'F');
		
		// // Does cache exist?  Return that instead
		// if (Cache::has($cache_key))
		// 	return Cache::get($cache_key);

		// Get the job IDs
		$job = ClassJob::with('en_abbr')->find($job_id);

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
		$stat_ids_to_avoid = Stat::get_ids(Stat::avoid($job->en_abbr->term));
		$stat_ids_to_focus = Stat::get_ids(Stat::focus($job->en_abbr->term));
		// $primary_stat = Stat::get_ids([Stat::primary($job->en_abbr->term)]);
		$boring_stat_ids = Stat::get_ids(Stat::boring());
		$advanced_stat_avoidance = Stat::advanced_avoidance($job->en_abbr->term);
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
		
		foreach ($slots as $slot_identifier => $slot_name)
		{
			$query = Item::with('name', 'baseparam', 'baseparam.name', 'vendors', 'recipe', 'recipe.classjob', 'recipe.classjob.name')
			//, 'vendors.npc', 'vendors.npc.name', 'vendors.npc.location', 'vendors.npc.location.name')
				->where('slot', $slot_identifier)
				->whereBetween('equip_level', array($level - 10, $level + $range))
				->whereHas('classjob', function($query) use ($job_id) {
					$query->where('classjob.id', $job_id);
				})
				->whereHas('baseparam', function($query) use ($stat_ids_to_focus) {
					$query->whereIn('baseparam.id', $stat_ids_to_focus);
				})
				->orderBy('items.equip_level', 'DESC')
				->orderBy('items.level', 'DESC')
				->limit(20);

			if ($craftable_only && $rewardable_too)
				$query->where(function($query) {
					$query
						->whereHas('recipe', function($query) {
							$query->where('recipes.item_id', \DB::raw('items.id'));
						})
						->orWhere('items.achievable', '1')
						->orWhere('items.rewarded', '1');
				});
			elseif ($craftable_only)
				$query->whereHas('recipe', function($query) {
					$query->where('recipes.item_id', \DB::raw('items.id'));
				});

			$items = $query
				// ->remember(Config::get('site.cache_length'))
				->get();

			$slot = isset($slot_alias[$slot_identifier]) ? $slot_alias[$slot_identifier] : $slot_identifier;
			$role = $slots[$slot];

			foreach ($items as $item)
			{
				// Kick it to the curb because of attributes?
				// Compare the focused vs the avoids
				$focus = $avoid = 0;
				$param_count = array_fill(1, 100, 0); // 73 total stats, 100's pretty safe, not to mention we only really focus on the first dozen
				foreach ($item->baseparam as $param)
				{
					$param_count[$param->id]++;
					if (in_array($param->id, $stat_ids_to_avoid))
						$avoid++;
					elseif (in_array($param->id, $stat_ids_to_focus))
						$focus++;
				}

				if ($advanced_stat_avoidance)
					foreach ($advanced_stat_avoidance as $ava)
						// If the [0] stat exists, but the [1] stat doesn't, drop the piece completely
						if ($param_count[$ava[0]] > 0 && $param_count[$ava[1]] == 0)
							$avoid += 10; // Really sell that this should be avoided
				
				# echo '<strong>' . $item->name->term . ' [' . $item->id . ']</strong> for ' . $role . ' (' . $focus . ',' . $avoid . ')<br>';
				
				if ($avoid >= $focus || $focus == 0)
					continue;

				// if ($item->name->term == 'Linen Cowl')
				// 	dd($item->name->term, $item->slot, $slot, $slot_cannot_equip, $slot_cannot_equip[$item->slot]);
				
				// Cannot equip attribute?
				if (isset($slot_cannot_equip[$item->slot]))
					$item->cannot_equip = implode(',', $slot_cannot_equip[$item->slot]);

				$equipment_list[$role][] = $item;

				# echo '<strong>+ ' . $item->name->term . ' [' . $item->id . ']</strong> for ' . $role . '<br>';
			}

			unset($items);
		}

		$two_handed_weapon_ids = ItemUICategory::two_handed_weapon_ids();

		$leveled_equipment = [];

		// We now have a proper list, but now we need to widdle down further by ilvl
		foreach (range($level, $level + $range) as $l)
		{
			$leveled_equipment[$l] = [];
			foreach ($equipment_list as $role => $items)
			{
				$leveled_equipment[$l][$role] = [];

				$max_equip_level = 0;

				// Find max
				foreach ($items as $item)
				{
					// $faux_ilvl = $item->level;

					// foreach ($item->baseparam as $param)
					// 	if ($param->id == $primary_stat)
					// 	{
					// 		$faux_ilvl += 2; // Treat it as 2 ilvls higher
					// 		break;
					// 	}
						
					if ($item->equip_level <= $l && $item->equip_level > $max_equip_level)
						$max_equip_level = $item->equip_level;
				}

				// Drop lesser items
				// OR figure out cannot equip stuff for weapons
				foreach ($items as $key => $item)
					if ($item->equip_level == $max_equip_level)
					{
						if (empty($item->cannot_equip) && in_array($item->itemuicategory_id, $two_handed_weapon_ids))
							$item->cannot_equip = 'Off Hand';
							//$item->cannot_equip = array_flip(Config::get('site.defined_slots'))['Off Hand'];
						

						if ( ! isset($leveled_equipment[$l][$role][$item->level]))
							$leveled_equipment[$l][$role][$item->level] = [];

						$leveled_equipment[$l][$role][$item->level][] = $item;
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

	public static function get_gear($job_id = 0, $level = 1, $range = 0, $craftable_only = TRUE, $rewardable_too = TRUE)
	{
		// $cache_key = __METHOD__ . '|' . Config::get('language') . '|' . $job_id . ',' . $level . ',' . $range . ($craftable_only ? ('T' . ($rewardable_too ? 'T' : 'F')) : 'F');
		
		// // Does cache exist?  Return that instead
		// if (Cache::has($cache_key))
		// 	return Cache::get($cache_key);

		// Get the job IDs
		$job = ClassJob::with('en_abbr')->find($job_id);

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
		$stat_ids_to_avoid = Stat::get_ids(Stat::avoid($job->en_abbr->term));
		$stat_ids_to_focus = Stat::get_ids(Stat::focus($job->en_abbr->term));
		$boring_stat_ids = Stat::get_ids(Stat::boring());
		$advanced_stat_avoidance = Stat::advanced_avoidance($job->en_abbr->term);
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
		
		foreach ($slots as $slot_identifier => $slot_name)
		{
			$query = Item::with('name', 'baseparam', 'baseparam.name', 'vendors', 'recipe', 'recipe.classjob', 'recipe.classjob.name')
			//, 'vendors.npc', 'vendors.npc.name', 'vendors.npc.location', 'vendors.npc.location.name')
				->where('slot', $slot_identifier)
				->whereBetween('equip_level', array($level - 10, $level + $range))
				->whereHas('classjob', function($query) use ($job_id) {
					$query->where('classjob.id', $job_id);
				})
				->whereHas('baseparam', function($query) use ($stat_ids_to_focus) {
					$query->whereIn('baseparam.id', $stat_ids_to_focus);
				})
				->orderBy('items.equip_level')
				->orderBy('items.level', 'DESC')
				->limit(20);

			if ($craftable_only && $rewardable_too)
				$query->where(function($query) {
					$query
						->whereHas('recipe', function($query) {
							$query->where('recipes.item_id', \DB::raw('items.id'));
						})
						->orWhere('items.achievable', '1')
						->orWhere('items.rewarded', '1');
				});
			elseif ($craftable_only)
				$query->whereHas('recipe', function($query) {
					$query->where('recipes.item_id', \DB::raw('items.id'));
				});

			$items = $query
						->get();

			$slot = isset($slot_alias[$slot_identifier]) ? $slot_alias[$slot_identifier] : $slot_identifier;
			$role = $slots[$slot];

			foreach ($items as $item)
			{
				// Kick it to the curb because of attributes?
				// Compare the focused vs the avoids
				$focus = $avoid = 0;
				$param_count = array_fill(1, 100, 0); // 73 total stats, 100's pretty safe, not to mention we only really focus on the first dozen
				foreach ($item->baseparam as $param)
				{
					$param_count[$param->id]++;
					if (in_array($param->id, $stat_ids_to_avoid))
						$avoid++;
					elseif (in_array($param->id, $stat_ids_to_focus))
						$focus++;
				}

				if ($advanced_stat_avoidance)
					foreach ($advanced_stat_avoidance as $ava)
						// If the [0] stat exists, but the [1] stat doesn't, drop the piece completely
						if ($param_count[$ava[0]] > 0 && $param_count[$ava[1]] == 0)
							$avoid += 10; // Really sell that this should be avoided
				
				# echo '<strong>' . $item->name->term . ' [' . $item->id . ']</strong> for ' . $role . ' (' . $focus . ',' . $avoid . ')<br>';

				if ($avoid >= $focus || $focus == 0)
					continue;

				// if ($item->name->term == 'Linen Cowl')
				// 	dd($item->name->term, $item->slot, $slot, $slot_cannot_equip, $slot_cannot_equip[$item->slot]);
				
				// Cannot equip attribute?
				if (isset($slot_cannot_equip[$item->slot]))
					$item->cannot_equip = implode(',', $slot_cannot_equip[$item->slot]);

				$equipment_list[$role][] = $item;

				# echo '<strong>+ ' . $item->name->term . ' [' . $item->id . ']</strong> for ' . $role . '<br>';
			}

			unset($items);
		}

		$two_handed_weapon_ids = ItemUICategory::two_handed_weapon_ids();

		$leveled_equipment = [];

		// We now have a proper list, but now we need to widdle down further by ilvl
		// foreach (range($level, $level + $range) as $l)
		// {

			foreach ($equipment_list as $role => $items)
			{
				// Let's find the lowest acceptable level
				// if I'm level 15, and there are level 12 and 17 items, include the level 12 items, but nothing under that
				$lowest_acceptable_level = $level;
				$closest_found = 0;

				// Find max
				foreach ($items as $item)
					if ($item->equip_level > $closest_found && $item->equip_level <= $level)
						$closest_found = $item->equip_level;

				// Drop lesser items
				// OR figure out cannot equip stuff for weapons
				foreach ($items as $key => $item)
					if ($item->equip_level >= $closest_found)
					{
						if (empty($item->cannot_equip) && in_array($item->itemuicategory_id, $two_handed_weapon_ids))
							$item->cannot_equip = 'Off Hand';
							//$item->cannot_equip = array_flip(Config::get('site.defined_slots'))['Off Hand'];

						$leveled_equipment[$role][$item->id] = $item;
					}
			}
		// }

		// Cache the results
		// Cache::put($cache_key, $leveled_equipment, Config::get('site.cache_length'));

		return $leveled_equipment;
	}

}
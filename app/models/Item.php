<?php

class Item extends Eloquent
{

	protected $table = 'items';
	public $timestamps = false;

	public function stats()
	{
		return $this->belongsToMany('Stat')->withPivot('amount', 'maximum');
	}

	public function jobs()
	{
		return $this->belongsToMany('Job');
	}

	public function recipes()
	{
		return $this->hasMany('Recipe');
	}

	public function quest()
	{
		return $this->hasMany('QuestItem');
	}

	public function leve()
	{
		return $this->hasMany('Leve');
	}

	public function nodes()
	{
		return $this->belongsToMany('GatheringNode')->orderBy('level');
	}

	public function vendors()
	{
		return $this->belongsToMany('Vendor');
	}

	public static function calculate($job = '', $level = 1, $craftable_only = TRUE)
	{
		$cache_key = __METHOD__ . '|' . $job . $level . ($craftable_only ? 'T' : 'F');

		// Does cache exist?  Return that instead
		if (Cache::has($cache_key))
			return Cache::get($cache_key);

		// Get the job IDs
		$job = Job::where('abbreviation', $job)->first();

		$equipment_list = array();

		// Make sure the pieces avoid pieces with certain stats
		$stats_to_avoid = Stat::avoid($job->abbreviation);

		DB::statement('SET SESSION group_concat_max_len=16384');

		foreach (Config::get('site.equipment_roles') as $role)
		{
			$query = DB::table('items AS i')
				->select(
					'i.id', 'i.name', 'i.buy', 'i.level', 'i.ilvl', 'i.icon', 'i.cannot_equip', 
					DB::raw('GROUP_CONCAT(DISTINCT rj.abbreviation) AS crafted_by'), 
					#####DB::raw('COUNT(iv.id) AS vendor_count'), 
					DB::raw("(
						SELECT
							GROUP_CONCAT(DISTINCT CONCAT(v.name,'|',v.title,'|',IFNULL(vl.name, ''),'|',v.x,'|',v.y) ORDER BY vl.name SEPARATOR '***') AS vendors
						FROM `item_vendor` AS `iv` 
						JOIN `vendors` AS `v` ON `v`.`id` = `iv`.`vendor_id`
						LEFT JOIN `locations` AS `vl` ON `vl`.`id` = `v`.`location_id`
						WHERE `iv`.`item_id` = `i`.`id`
					) AS vendors")
				)
				->join('item_job AS ij', 'ij.item_id', '=', 'i.id')
				->join('jobs AS j', 'j.id', '=', 'ij.job_id')
				->leftJoin('recipes AS r', 'i.id', '=', 'r.item_id')
				->leftJoin('jobs AS rj', 'rj.id', '=', 'r.job_id')
				#######->leftJoin('item_vendor AS iv', 'iv.item_id', '=', 'i.id')
				//->where('j.id', $job->id)
				->where(function($query) use ($job)
				{
					$query->where('j.id', $job->id);
					
					// If we're not talking crafting/gathering gear, include ALL
					if ( ! in_array($job->disciple, array('DOH', 'DOL')))
						$query->orWhere('j.abbreviation', 'ALL');
				})
				->where('i.role', $role)
				->where('i.level', '<=' , $level)
				->orderBy('i.ilvl', 'DESC')
				->orderBy('i.level', 'DESC')
				->groupBy('i.name', 'i.level'); // Fight off duplicates :(

			if ($craftable_only)
				$query->havingRaw('crafted_by IS NOT NULL');

			$equipment_list[$role] = $query
				->remember(Config::get('site.cache_length'))
				->get();

			$list =& $equipment_list[$role];

			// Load the stats on each one
			foreach ($list as $key => $item)
			{
				$item->stats = DB::table('item_stat AS istat')
					->select('s.name', 'istat.amount', 'istat.maximum')
					->join('stats AS s', 's.id', '=', 'istat.stat_id')
					->where('istat.item_id', $item->id)
					->remember(Config::get('site.cache_length'))
					->get();

				$stats = array();
				foreach ($item->stats as $stat)
					$stats[$stat->name] = rtrim(rtrim(rtrim($stat->amount, '0'), '0'), '.');
				$item->stats = $stats;

				if (count(array_intersect(array_keys($item->stats), $stats_to_avoid)) > 0)
					unset($list[$key]);

				// Vendors
				$item->vendor_count = 0;
				
				if ($item->vendors)
				{
					$new_vendors = array();
					foreach(explode('***', $item->vendors) as $vendor)
					{
						list($name, $title, $location, $x, $y) = explode('|', $vendor);

						$new_vendors[$location ?: 'Unknown'][] = (object) array(
							'name' => $name,
							'title' => $title,
							'x' => $x,
							'y' => $y
						);

						$item->vendor_count++;
					}

					ksort($new_vendors);

					$item->vendors = $new_vendors;
				}
			}

			// Go through the list.
			// If it's not the highest level, remove it
			// Unless it's the very first item
			// Then get the stats
			$highest_level = 0;
			foreach ($equipment_list[$role] as $item)
				if ($item->ilvl > $highest_level)
					$highest_level = $item->ilvl;

			$i = 0;
			foreach ($equipment_list[$role] as $key => $item)
				if ($i++ != 0 && $item->ilvl != $highest_level)
					unset($equipment_list[$role][$key]);

			// Re-key the list
			$list = array_values($list);

			unset($list);
		}
		
		// Cache the results
		Cache::put($cache_key, $equipment_list, Config::get('site.cache_length'));

		return $equipment_list;
	}

}
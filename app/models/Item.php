<?php

class Item extends Eloquent
{

	protected $table = 'items';
	public $timestamps = false;

	public function slot()
	{
		return $this->belongsTo('Slot');
	}

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

	public static function calculate($job = '', $level = 1, $craftable_only = TRUE)
	{
		$cache_key = __METHOD__ . '|' . $job . $level . ($craftable_only ? 'T' : 'F');

		// Does cache exist?  Return that instead
		if (Cache::has($cache_key))
			return Cache::get($cache_key);

		// Get the job IDs
		$job = Job::where('abbreviation', $job)->first();

		$equipment_list = array();

		foreach (Slot::where('type', 'equipment')->get() as $slot)
		{
			$query = DB::table('items AS i')
				->select('i.id', 'i.name', 'i.href', 'i.vendors', 'i.gil', 'i.level', DB::raw('GROUP_CONCAT(DISTINCT rj.abbreviation) AS crafted_by'))
				->join('item_job AS ij', 'ij.item_id', '=', 'i.id')
				->join('jobs AS j', 'j.id', '=', 'ij.job_id')
				->leftJoin('recipes AS r', 'i.id', '=', 'r.item_id')
				->leftJoin('jobs AS rj', 'rj.id', '=', 'r.job_id')
				->where('j.id', $job->id)
				->where('i.slot_id', $slot->id)
				->where('i.level', '<=' , $level)
				->orderBy('i.level', 'DESC')
				->orderBy('i.ilvl', 'DESC')
				->groupBy('i.name', 'i.level'); // Fight off duplicates :(

			if ($craftable_only)
				$query->havingRaw('crafted_by IS NOT NULL');

			$equipment_list[$slot->name] = $query->get();

			// Go through the list.
			// If it's not the highest level, remove it
			// Unless it's the very first item
			// Then get the stats
			$highest_level = 0;
			foreach ($equipment_list[$slot->name] as $item)
				if ($item->level > $highest_level)
					$highest_level = $item->level;

			$i = 0;
			foreach ($equipment_list[$slot->name] as $key => $item)
			{
				if ($i++ != 0 && $item->level != $highest_level)
					unset($equipment_list[$slot->name][$key]);
				else
				{
					$item->stats = DB::table('item_stat AS istat')
						->select('s.name', 'istat.amount', 'istat.maximum')
						->join('stats AS s', 's.id', '=', 'istat.stat_id')
						->where('istat.item_id', $item->id)
						->get();

					$stats = array();
					foreach ($item->stats as $stat)
						$stats[$stat->name] = rtrim(rtrim(rtrim($stat->amount, '0'), '0'), '.');
					$item->stats = $stats;
				}
			}
		}
		
		// Cache the results
		Cache::put($cache_key, $equipment_list, Config::get('site.cache_length'));

		return $equipment_list;
	}

}
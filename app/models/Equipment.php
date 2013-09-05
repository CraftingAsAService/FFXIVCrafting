<?php

class Equipment extends Eloquent
{

	protected $table = 'equipment';
	public $timestamps = false;

	public function stats()
	{
		return $this->belongsToMany('Stat')->withPivot('amount');;
	}

	public function type()
	{
		return $this->belongsTo('EquipmentType');
	}

	public static function calculate($job, $disciple, $level = 1, $prefer_craftable = TRUE)
	{
		// Get the job IDs
		$job_ids = array(
			Job::where('abbreviation', $job)->first()->id,
			Job::where('abbreviation', $disciple)->first()->id
		);

		$equipment_list = array();

		foreach (EquipmentType::all() as $type)
		{
			$query = Equipment::with('stats')
				->where('level', '<=', $level)
				->where('type_id', $type->id)
				->whereIn('job_id', $job_ids)
				->orderBy('level', 'DESC');

			if ($prefer_craftable)
				$query->where(DB::raw('LENGTH(origin)'), 3);

			$equipment_list[$type->name] = $query->get();
			

			// Go through the list.
			// If it's not the desired level, remove it
			// Unless it's the very first item
			$i = 0;
			foreach ($equipment_list[$type->name] as $key => $item)
			{
				if ($i++ == 0)
					continue;

				if ($item->level != $level)
					unset($equipment_list[$type->name][$key]);
			}
		}

		return $equipment_list;
	}

}
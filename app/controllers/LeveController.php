<?php

class LeveController extends BaseController 
{

	public function __construct()
	{
		parent::__construct();
		View::share('active', 'leves');
	}

	public function getIndex()
	{
		$job_ids = Config::get('site.job_ids.crafting');

		return View::make('leve.index')
			->with('crafting_job_list', ClassJob::with('name', 'en_abbr')->whereIn('id', $job_ids)->get())
			->with('crafting_job_ids', $job_ids);
	}

	public function postIndex()
	{
		// Parse the Job IDs
		$selected_classes = Input::get('classes');
		foreach (ClassJob::get_id_abbr_list(true) as $abbr => $id)
			if (in_array($abbr, $selected_classes))
				$job_ids[] = $id;

		if (empty($job_ids))
			$job_ids[] = 1;

		// All Leves
		$query = Leve::with(array(
				'classjob', 'item', 'item.name', 'item.recipe',
			))
			->where('item_id', '>', 0) // Avoids mining/botany "bug"
			->orderBy('classjob_id')
			->orderBy('level')
			->orderBy('xp')
			->orderBy('gil');

		// Job IDs
		$query->whereIn('classjob_id', $job_ids);

		// Level Range
		$min = Input::get('min_level');
		$max = Input::get('max_level');

		// Invert if needed
		if ($min > $max) list($max, $min) = array($min, $max);

		$query->whereBetween('level', array($min, $max));
		
		// Triple Only
		if (Input::get('triple_only') == 'true')
			$query->where('triple', 1);

		// Types
		$query->whereIn('type', Input::get('types'));

		// Text Searches
		if (Input::get('leve_name'))
			$query->where('name', 'like', '%' . Input::get('leve_name') . '%');

		$leve_records = $query->remember(Config::get('site.cache_length'))->get();

		$location_search = strtolower(Input::get('leve_location'));
		$item_search = strtolower(Input::get('leve_item'));

		$rewards = LeveReward::with('item')
			->whereBetween('level', array($min, $max))
			->whereIn('classjob_id', $job_ids)
			->get();

		$leve_rewards = array();

		foreach ($leve_records as $k => $row)
		{
			if ($item_search && ! preg_match('/' . $item_search . '/', strtolower($row->item->name->term)))
			{
				unset($leve_records[$k]);
				continue;
			}
			
			// TODO this can be moved into the query itself now, most likely
			if ($location_search)
			{
				if ( ! preg_match('/' . $location_search . '/', strtolower($row->location)) &&
					 ! preg_match('/' . $location_search . '/', strtolower($row->major_location)) && 
					 ! preg_match('/' . $location_search . '/', strtolower($row->minor_location))
				)
				{
					unset($leve_records[$k]);
					continue;
				}
			}

			foreach($rewards as $reward)
				if ($reward->classjob_id == $row->classjob_id && $reward->level == $row->level)
					$leve_rewards[$row->id][] = $reward;
		}
		
		return View::make('leve.rows')
			->with('leves', $leve_records)
			->with('leve_rewards', $leve_rewards);
	}

	public function getBreakdown($leve_id = 1)
	{
		foreach ($this->_breakdown($leve_id) as $key => $value)
			$$key = $value;

		// Get other Leve's at this level
		$other_leves = Leve::where('level', $leve->level)
			->where('classjob_id', $leve->classjob_id)
			->where('id', '!=', $leve->id)
			->get();

		return View::make('leve.breakdown')
			->with(array(
				'leve' => $leve,
				'chart' => $chart,
				'others' => $other_leves
			));
	}

	private function _breakdown($leve_id = 0)
	{
		$leve = Leve::with('item', 'item.name', 'item.recipe', 'item.recipe.reagents', 'item.recipe.reagents.name')->find($leve_id);
		$experience = Experience::whereBetween('level', array($leve->level, $leve->level + 9))->get();
		
		$xp_rewarded = $leve->xp * 2; // 2.1 patch changed it from 200% to 100% bonus

		$chart = array();
		foreach ($experience as $xp)
		{
			$previous_overkill = isset($chart[$xp->level - 1]) ? $chart[$xp->level - 1]['overkill'] : 0;

			$needed = $xp->experience - $previous_overkill;

			$amount = $turnins = 0;
			if ($xp_rewarded > 0)
				while ($amount < $needed)
				{
					$amount += $xp_rewarded;
					$turnins++;
				}
			$chart[$xp->level] = array(
				'level' => $xp->level,
				'requires' => $xp->experience,
				'previous_overkill' => $previous_overkill,
				'turnins' => $turnins,
				'overkill' => $amount - $needed
			);
		}

		return array('leve' => $leve, 'chart' => $chart);
	}

	public function getVs($leveA = 1, $leveB = 1)
	{
		return View::make('leve.vs')
			->with(array(
				'a' => $this->_breakdown($leveA),
				'b' => $this->_breakdown($leveB)
			));
	}

}
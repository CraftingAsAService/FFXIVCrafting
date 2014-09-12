<?php

class LevequestsController extends BaseController 
{

	public function __construct()
	{
		parent::__construct();
		View::share('active', 'levequests');
	}

	public function getIndex()
	{
		$job_ids = Config::get('site.job_ids.crafting');
		$job_ids[] = Config::get('site.job_ids.fishing');

		$type_to_icon = array(
			'Field' => 'leaf',
			'Courier' => 'envelope',
			'Reverse Courier' => 'plane',
			'Town' => 'home',
		);

		// All Leves
		$all_leves = Leve::with(array(
				'classjob', 'classjob.en_abbr', 'item', 'item.name', 'item.recipe', 'item.vendors',
			))
			->where('item_id', '>', 0) // Avoids mining/botany "bug"
			->orderBy('classjob_id')
			->orderBy('level')
			->orderBy('triple', 'desc')
			->orderBy('xp', 'desc')
			->orderBy('gil', 'desc')
			->get();

		$leves = array();
		foreach ($all_leves as $leve)
			$leves[$leve->classjob->en_abbr->term][$leve->level][] = $leve;

		$rewards = LeveReward::with('item')
			->orderBy('classjob_id')
			->orderBy('level')
			->orderBy('item_id')
			->orderBy('amount')
			->get();

		$leve_rewards = array();
		foreach($rewards as $reward)
			if ($reward->item_id)
			{
				$leve_rewards[$reward->classjob_id][$reward->level][$reward->item_id]['item'] = $reward->item;
				$leve_rewards[$reward->classjob_id][$reward->level][$reward->item_id]['amounts'][] = $reward->amount;
			}

		return View::make('levequests.index')
			->with('crafting_job_list', ClassJob::with('name', 'en_name', 'en_abbr')->whereIn('id', $job_ids)->get())
			->with('crafting_job_ids', $job_ids)
			->with('leves', $leves)
			->with('rewards', $leve_rewards)
			->with('type_to_icon', $type_to_icon)
			->with('opening_level', 1)
			->with('opening_class', 'CRP');
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
		$leve = Leve::with('classjob', 'classjob.en_name', 'item', 'item.name', 'item.recipe', 'item.recipe.reagents', 'item.recipe.reagents.name')->find($leve_id);
		$experience = Experience::whereBetween('level', array($leve->level + 1, $leve->level + 10))->get();
		
		$chart = array();
		foreach ($experience as $xp)
		{
			// NQ Turnins
			$amount = $turnins = 0;
			if ($leve->xp > 0)
				while ($amount < $xp->experience)
				{
					$amount += $leve->xp;
					$turnins++;
				}

			// HQ Turnins
			$amount = $hq_turnins = 0;
			if ($leve->xp > 0)
				while ($amount < $xp->experience)
				{
					$amount += $leve->xp * 2; // 2.1 patch changed it from 200% to 100% bonus
					$hq_turnins++;
				}

			$chart[$xp->level] = array(
				'level' => $xp->level,
				'requires' => $xp->experience,
				'turnins' => $turnins,
				'hq_turnins' => $hq_turnins,
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
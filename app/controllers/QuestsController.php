<?php

class QuestsController extends BaseController 
{

	public function getIndex()
	{
		// All Jobs
		$job_list = array();
		foreach (Job::all() as $j)
			$job_list[$j->abbreviation] = $j->name;

		// All Quests
		$quest_records = QuestItem::with('job', 'item', 'item.recipes')
			->orderBy('job_id')
			->orderBy('level')
			->orderBy('item_id')
			->get();

		$quests = array();	
		foreach($quest_records as $quest)
		{
			if ( ! isset($quests[$quest->job->abbreviation]))
				$quests[$quest->job->abbreviation] = array();

			foreach ($quest->item->recipes as $r)
				if ($r->job_id == $quest->job_id)
					$quest->recipe = $r;

			$quests[$quest->job->abbreviation][] = $quest;
		}

		return View::make('quests')
			->with('active', 'quests')
			->with('quests', $quests)
			->with('job_list', $job_list);
	}

}
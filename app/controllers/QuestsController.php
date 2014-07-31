<?php

class QuestsController extends BaseController 
{

	public function __construct()
	{
		View::share('active', 'quests');
	}

	public function getIndex()
	{
		// All Quests
		$quest_records = QuestItem::with('classjob', 'classjob.abbr', 'item', 'item.name', 'item.recipe')
			->orderBy('classjob_id')
			->orderBy('level')
			->orderBy('item_id')
			->get();

		$quests = array();	
		foreach($quest_records as $quest)
		{
			if ( ! isset($quests[$quest->classjob->abbr->term]))
				$quests[$quest->classjob->abbr->term] = array();

			if (empty($quest->item->recipe))
			{
				var_dump($quest);
				exit;
			}
			foreach ($quest->item->recipe as $r)
				if ($r->classjob_id == $quest->classjob_id)
					$quest->recipe = $r;

			$quests[$quest->classjob->abbr->term][] = $quest;
		}

		return View::make('pages.quests')
			->with('quests', $quests)
			->with('job_list', ClassJob::get_name_abbr_list());
	}

}
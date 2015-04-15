<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Config;

use App\Models\CAAS\QuestItem;
use App\Models\CAAS\ClassJob;

class QuestsController extends Controller
{

	public function __construct()
	{
		parent::__construct();
		view()->share('active', 'quests');
	}

	public function getIndex()
	{
		// All Quests
		$quest_records = QuestItem::with('classjob', 'classjob.en_abbr', 'item', 'item.name', 'item.recipe')
			->orderBy('classjob_id')
			->orderBy('level')
			->orderBy('item_id')
			->get();

		$quests = array();	
		foreach($quest_records as $quest)
		{
			if ( ! isset($quests[$quest->classjob->en_abbr->term]))
				$quests[$quest->classjob->en_abbr->term] = array();

			if (empty($quest->item->recipe))
			{
				var_dump($quest);
				exit;
			}
			foreach ($quest->item->recipe as $r)
				if ($r->classjob_id == $quest->classjob_id)
					$quest->recipe = $r;

			$quests[$quest->classjob->en_abbr->term][] = $quest;
		}

		$job_ids = array_merge(Config::get('site.job_ids.crafting'), Config::get('site.job_ids.gathering'));
		$job_list = ClassJob::with('name', 'en_abbr')->whereIn('id', $job_ids)->get();

		return view('pages.quests', compact('quests', 'job_ids', 'job_list'));
	}

}

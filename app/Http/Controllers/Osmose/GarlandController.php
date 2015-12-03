<?php 

namespace App\Http\Controllers\Osmose;

use App\Models\Osmose\Garland;

class GarlandController extends \App\Http\Controllers\Controller
{
	protected $ffxivcrafting_repo = 'ffxivcrafting',
				$aspir_repo = 'aspir';

	public function getIndex()
	{
		Garland::scrape();

		flash()->success('Garland Core Scraped');

		return redirect()->back();
	}

	public function getView()
	{
		$core = json_decode(file_get_contents(storage_path() . '/app/osmose/garland-data-core.json'));

		// dd('node', $core->node->bonusIndex);
		// $id = "3";
		// dd('node', $core->node->index);//->$id);
		// dd('node', $core->node->index->$id);
		// dd('fishing', $core->fishing->index->$id);
		// $id = "13970000000117";
		// dd('mob', $core->mob->index);
		// dd('mob', $core->mob->index->$id);
		// dd('location', $core->location->index);
		// dd('skywatcher', $core->skywatcher);
		// $id = 1000193;
		// dd('npc, index', $core->npc->index->$id);
		// $id = "Merchant & Mender";
		// dd('npc, base index', $core->npc->baseIndex);//->$id);
		// dd('npc, shopnames', $core->npc->shopNames);
		// dd('npc, shops', $core->npc->shops);
		// dd('instance', $core->instance->rouletteIndex);
		// $id = 30;
		// dd('instance', $core->instance->index->$id);
		// dd('quest', $core->quest);
		// dd('achievement', $core->achievement->index);
		// dd('fate', $core->fate->index);
		// dd('jobCategories', $core->jobCategories);
		// dd('venture', $core->venture->index);
		// dd('action', $core->action->categoryIndex);
		// dd('action', $core->action->index);
		// dd('action', $core->action->categoryIndex);
		// dd('action', $core->action->statusIndex);
		dd('leve', $core->leve->partialIndex);
		// $id = 5718;
		// dd('item', $core->item->categoryIndex);
		// dd('item', $core->item->index);
		dd('gItemIndex', $core->gItemIndex);


		dd($core);
	}

}
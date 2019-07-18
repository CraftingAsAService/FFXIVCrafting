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

		flash('Garland Core Scraped')->success();

		return redirect()->back();
	}

	public function getView()
	{
		$core = json_decode(file_get_contents(storage_path() . '/app/osmose/garland-data-core.json'));

		dd($core->item);

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
		// dd('npc', $core->npc);
		// dd('npc', $core->npc);
		// dd('npc, 1001276', $core->npc->partialIndex->{'1001276'});
		// $id = 1000193;
		// dd('npc, index', $core->npc->index->$id);
		// $id = "Merchant & Mender";
		// dd('npc, base index', $core->npc->baseIndex);//->$id);
		// dd('npc, shopnames', $core->npc->shopNames);
		// echo '<pre>';
		// foreach ($core->npc->shops as $shop)
		// 	if (isset($shop->trade) && $shop->trade == 1)
		// 		var_dump($shop);
		// 		// dd($shop);
		// 	exit;
		// dd('npc, shops', $core->npc->shops);
		// dd('instance', $core->instance);
		// $id = 30;
		// dd('instance', $core->instance->index->$id);
		// dd('quest', $core->quest);
		// dd('achievement', $core->achievement->categoryIndex);
		// dd('fate', $core->fate->index);
		// dd('jobCategories', $core->jobCategories);

		// dd('venture', $core->venture->index);
		// dd('action', $core->action->categoryIndex);
		// dd('action', $core->action->index);
		// dd('action', $core->action->categoryIndex);
		// dd('action', $core->action->statusIndex);
		// dd('leve', $core->leve->partialIndex);
		// $id = 5718;
		// dd('item', $core->item->categoryIndex);
		// dd('item', $core->item->index);
		// $id = 4687;
		// dd('gItemIndex', $core->gItemIndex[$id]);
		// dd('gItemIndex', $core->gItemIndex);


		dd($core);
	}

}
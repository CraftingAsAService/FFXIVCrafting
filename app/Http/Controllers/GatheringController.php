<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\CAAS\Item;

class GatheringController extends Controller
{

	public function getClusters($id = 0)
	{
		$item = Item::with('name', 'clusters', 'clusters.classjob', 'clusters.classjob.name', 'clusters.location', 'clusters.location.name', 'clusters.nodes')
			->where('id', $id)
			// ->remember(Config::get('site.cache_length'))
			->first();

		$clusters = [];

		foreach ($item->clusters as $cluster)
			foreach($cluster->nodes as $node)
				@$clusters[$cluster->location->name->term][$cluster->level][$cluster->icon][$node->description]++;

		return [
			'html' => view('partials.clusters-modal', compact('item', 'clusters'))->render()
		];
	}

	public function getBeasts($id = 0)
	{
		$item = Item::with('name', 'beasts', 'beasts.name', 'beasts.location', 'beasts.location.name')
			->where('id', $id)
			// ->remember(Config::get('site.cache_length'))
			->first();

		$beasts = [];

		foreach ($item->beasts as $beast)
			foreach ($beast->location as $loc)
				@$beasts[$loc->name->term][ltrim($beast->name->term, '\x20') . ($loc->pivot->triggered ? '*' : '')][] = $loc->pivot->levels;

		return [
			'html' => view('partials.beasts-modal', compact('item', 'beasts'))->render()
		];
	}

}

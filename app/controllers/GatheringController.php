<?php

class GatheringController extends BaseController 
{

	public function getClusters($id = 0)
	{
		$item = Item::with('name', 'clusters', 'clusters.classjob', 'clusters.classjob.name', 'clusters.location', 'clusters.location.name', 'clusters.nodes')
			->where('id', $id)
			// ->remember(Config::get('site.cache_length'))
			->first();

		$clusters = array();

		foreach ($item->clusters as $cluster)
			foreach($cluster->nodes as $node)
				@$clusters[$cluster->location->name->term][$cluster->level][$cluster->icon][$node->description]++;

		exit(json_encode(array(
			'html' => View::make('clusters.modal', array(
				'item' => $item,
				'clusters' => $clusters
			))->render()
		)));
	}

	public function getBeasts($id = 0)
	{
		$item = Item::with('name', 'beasts', 'beasts.name', 'beasts.location', 'beasts.location.name')
			->where('id', $id)
			// ->remember(Config::get('site.cache_length'))
			->first();

		$beasts = array();

		foreach ($item->beasts as $beast)
			foreach ($beast->location as $loc)
				@$beasts[$loc->name->term][ltrim($beast->name->term, '\x20') . ($loc->pivot->triggered ? '*' : '')][] = $loc->pivot->levels;

		// echo View::make('beasts.modal', array(
		// 		'item' => $item,
		// 		'beasts' => $beasts
		// 	))->render();
		// exit;

		exit(json_encode(array(
			'html' => View::make('beasts.modal', array(
				'item' => $item,
				'beasts' => $beasts
			))->render()
		)));
	}

	public function getIndex()
	{
		return Redirect::to('/career');
	}

	public function getList()
	{
		return Redirect::to('/career');
	}

}
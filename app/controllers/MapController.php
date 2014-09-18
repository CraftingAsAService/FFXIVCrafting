<?php

class MapController extends BaseController
{

	public function getIndex()
	{
		// The config doesn't have the names, cache it per language
		$map_config = Cache::get('map_config_' . Config::get('language'), function() {
			$map_config = Config::get('site.map');

			foreach ($map_config as $area => &$section)
				foreach ($section['regions'] as $region => &$data)
					$data['name'] = PlaceName::find($data['id'])->name->term;

			Cache::put('map_config', $map_config, Config::get('site.cache_length'));

			return $map_config;
		});

		return View::make('map.index')
			->with('map', $map_config);
	}

	public function postIndex()
	{
		$title = preg_replace('/\s+/m', ' ', preg_replace("/\n/", '', trim(Input::get('title'))));
		View::share('map_title', $title);

		$posted_list = explode('||', Input::get('items'));
		$item_list = array();
		foreach ($posted_list as $row)
		{
			list($id, $amount) = explode('|', $row);
			$item_list[$id] = $amount;
		}

		if ( ! empty($item_list))
		{
			$items = Item::with(
				'name', 
				'vendors', 
					'vendors.npc', 
						'vendors.npc.name', 
						'vendors.npc.location', 
						'vendors.npc.location.name', 
				'clusters',
					'clusters.classjob',
						'clusters.classjob.name',
						'clusters.classjob.abbr',
					'clusters.location',
						'clusters.location.name',
					'clusters.nodes',
				'beasts',
					'beasts.name',
					'beasts.location'
			)->whereIn('id', array_keys($item_list))
			// ->remember(Config::get('site.cache_length'))
			->get();

			$map_data = array();

			foreach ($items as $item)
			{
				foreach ($item->vendors as $v)
				{
					foreach ($v->npc->location as $l)
					{
						if ( ! isset($map_data[$l->id]))
							$map_data[$l->id] = array(
								'name' => $l->name->term
							);

						if ( ! isset($map_data[$l->id]['vendors']))
							$map_data[$l->id]['vendors'] = array();

						//						// Map ID 			// NPC ID
						if ( ! isset($map_data[$l->id]['vendors'][$v->npc->id]))
							$map_data[$l->id]['vendors'][$v->npc->id] = array(
								'name' => $v->npc->name->term,
								'x' => $l->pivot->x,
								'y' => $l->pivot->y,
								'items' => array()
							);

						$map_data[$l->id]['vendors'][$v->npc->id]['items'][$item->id] = array(
							'needed' => $item_list[$item->id],
							'name' => $item->name->term,
							'min_price' => $item->min_price,
							'max_price' => $item->max_price
						);
					}
				}
				foreach ($item->clusters as $c)
				{
					if ( ! isset($map_data[$c->placename_id]))
						$map_data[$c->placename_id] = array(
							'name' => is_null($c->location) ? 'unknown' : $c->location->name->term
						);

					if ( ! isset($map_data[$c->placename_id]['clusters']))
						$map_data[$c->placename_id]['clusters'] = array();

					if ( ! isset($map_data[$c->placename_id]['clusters'][$c->id]))
						$map_data[$c->placename_id]['clusters'][$c->id] = array(
							'icon' => $c->icon,
							'level' => $c->level,
							'classjob' => $c->classjob_id,
							'classjob_name' => $c->classjob->name->term,
							'classjob_abbr' => $c->classjob->abbr->term,
							'x' => $c->x,
							'y' => $c->y,
							'items' => array()
						);

					$map_data[$c->placename_id]['clusters'][$c->id]['items'][$item->id] = array(
						'needed' => $item_list[$item->id],
						'name' => $item->name->term,
						'min_price' => $item->min_price,
						'max_price' => $item->max_price
					);
				}
				foreach ($item->beasts as $b)
				{
					foreach ($b->location as $l)
					{
						if ( ! isset($map_data[$l->id]))
							$map_data[$l->id] = array(
								'name' => $b->name->term
							);

						if ( ! isset($map_data[$l->id]['beasts']))
							$map_data[$l->id]['beasts'] = array();

						//						// Map ID 			// NPC ID
						if ( ! isset($map_data[$l->id]['beasts'][$b->id]))
							$map_data[$l->id]['beasts'][$b->id] = array(
								'name' => $b->name->term,
								'levels' => $b->pivot->levels,
								'triggered' => $b->pivot->triggered,
								'items' => array()
							);

						$map_data[$l->id]['beasts'][$b->id]['items'][$item->id] = array(
							'needed' => $item_list[$item->id],
							'name' => $item->name->term
						);
					}
				}
			}

			View::share('map_data', $map_data);
			View::share('items', $items);
		}

		return $this->getIndex();
	}

}
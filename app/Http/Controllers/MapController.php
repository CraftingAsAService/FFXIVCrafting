<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Config;
use Cache;

use App\Models\CAAS\PlaceName;
use App\Models\CAAS\Item;

class MapController extends Controller
{

	public function getIndex()
	{
		abort('404'); // FIXME, there's just not enough data
		
		// The config doesn't have the names, cache it per language
		$map = Cache::get('map_config_' . Config::get('language'), function() {
			$map = Config::get('site.map');

			foreach ($map as $area => &$section)
				foreach ($section['regions'] as $region => &$data)
					$data['name'] = PlaceName::find($data['id'])->name->term;

			Cache::put('map_config', $map, Config::get('site.cache_length'));

			return $map;
		});

		return view('map.index', compact('map'));
	}

	public function postIndex(Request $request)
	{
		$inputs = $request->all();

		$title = preg_replace('/\s+/m', ' ', preg_replace("/\n/", '', trim($inputs['title'])));
		view()->share('map_title', $title);

		$posted_list = explode('||', $inputs['items']);
		$item_list = array();
		foreach ($posted_list as $row)
		{
			list($id, $amount) = explode('|', $row);
			$item_list[$id] = $amount;
		}

		view()->share(compact('item_list'));

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

			view()->share(compact('map_data', 'items'));
		}

		return $this->getIndex();
	}

}

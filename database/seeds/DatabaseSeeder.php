<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{

	public function run()
	{
		set_time_limit(0);

		$start = new DateTime('now');

		if (app()->environment('local'))
			$this->local();
		else
			echo "\n" . 'Run locally instead!' . "\n";
		
		echo "\n" . 'Time Elapsed: ' . $start->diff(new DateTime('now'))->format('%I:%S') . "\n";

		return;
	}

	private function local()
	{
		Model::unguard();

		// Don't bother logging queries
		\DB::connection()->disableQueryLog();

		if (Cache::has('garland-seed'))
			$this->data = Cache::get('garland-seed');
		else
		{
			$core = json_decode(file_get_contents(storage_path() . '/app/osmose/garland-data-core.json'));

			$this->node_bonuses($core->node->bonusIndex);
			$this->node($core->node->index);
			$this->fishing($core->fishing->index);
			$this->mob($core->mob->index);
			$this->location($core->location->index);
			$this->npc($core->npc->index);
			$this->npc_base($core->npc->baseIndex);
			$this->shop_name($core->npc->shopNames);
			$this->shop($core->npc->shops);
			$this->instance($core->instance->index);
			$this->quest($core->quest->partialIndex);
			$this->achievement($core->achievement->index);
			$this->fate($core->fate->index);
			$this->job_category($core->jobCategories);
			$this->job(); // No provided data, hard coded
			$this->venture($core->venture->index);
			$this->leve($core->leve->partialIndex);
			$this->item_category($core->item->categoryIndex);
			$this->item($core->gItemIndex);

			// Custom Data Manipulation, careers section
			$this->career();

			Cache::forever('garland-seed', $this->data);
		}

		$this->batch_insert();
	}

	private function node_bonuses($node_bonuses)
	{
		// Setup Data Var
		$this->data['node_bonuses'] = [];

		// Loop through given data
		foreach ($node_bonuses as $nb)
		{
			$nb->bonus = trim($nb->bonus);
			$this->set_data('node_bonuses', (array) $nb);
		}

		echo __FUNCTION__ . ', ' . count($this->data['node_bonuses']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function node($node)
	{
		// Setup Data Var
		$this->data['node'] = [];
		$this->data['item_node'] = [];

		// Loop through nodes
		foreach ($node as $n)
		{
			$row = [
				'id' => $n->id,
				'name' => $n->name,
				'type' => $n->type,
				'level' => $n->lvl,
				'bonus_id' => isset($n->bonus) ? $n->bonus : null,
				'zone_id' => $n->zoneid,
				'area_id' => isset($n->areaid) ? $n->areaid : null,
			];

			$this->set_data('node', $row);

			// Loop through node items
			foreach ($n->items as $i)
			{
				$row = [
					'item_id' => $i->id,
					'node_id' => $n->id,
				];

				$this->set_data('item_node', $row);
			}
		}

		echo __FUNCTION__ . ', ' . count($this->data['node']) . ' rows' . PHP_EOL;
		echo 'item_node, ' . count($this->data['item_node']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function fishing($fishing)
	{
		// Setup Data Var
		$this->data['fishing'] = [];
		$this->data['fishing_item'] = [];

		// Loop through nodes
		foreach ($fishing as $f)
		{
			$row = [
				'id' => $f->id,
				'name' => $f->name,
				'category_id' => $f->category,
				'level' => $f->lvl,
				'radius' => $f->radius,
				'x' => $f->x,
				'y' => $f->y,
				'zone_id' => $f->zoneid,
				'area_id' => $f->areaid,
			];

			$this->set_data('fishing', $row);

			// Loop through fishing items
			foreach ($f->items as $i)
			{
				$row = [
					'item_id' => $i->id,
					'fishing_id' => $f->id,
					'level' => $i->lvl,
				];

				$this->set_data('fishing_item', $row);
			}
		}

		echo __FUNCTION__ . ', ' . count($this->data['fishing']) . ' rows' . PHP_EOL;
		echo 'fishing_item, ' . count($this->data['fishing_item']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function mob($mob)
	{
		// Setup Data Var
		$this->data['mob'] = [];
		$this->data['item_mob'] = [];

		// Loop through nodes
		foreach ($mob as $m)
		{
			$row = [
				'id' => $m->id,
				'name' => $m->name,
				'quest' => isset($m->quest) ? $m->quest : null,
				'level' => $m->lvl,
				'zone_id' => $m->zoneid,
			];

			$this->set_data('mob', $row);

			// Loop through mob items
			foreach ($m->drops as $item_id)
			{
				$row = [
					'item_id' => $item_id,
					'mob_id' => $m->id,
				];

				$this->set_data('item_mob', $row);
			}
		}

		echo __FUNCTION__ . ', ' . count($this->data['mob']) . ' rows' . PHP_EOL;
		echo 'item_mob, ' . count($this->data['item_mob']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function location($location)
	{
		// Setup Data Var
		$this->data['location'] = [];

		// Loop through given data
		foreach ($location as $l)
		{
			$row = [
				'id' => $l->id,
				'name' => $l->name,
				'location_id' => isset($l->parentId) ? $l->parentId : null,
				'size' => isset($l->size) ? $l->size : null,
			];

			$this->set_data('location', $row);
		}

		echo __FUNCTION__ . ', ' . count($this->data['location']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function npc($npc)
	{
		// Setup Data Var
		$this->data['npc'] = [];
		$this->data['npc_shop'] = [];
		$this->data['npc_quest'] = [];

		// Loop through nodes
		foreach ($npc as $n)
		{
			$row = [
				'id' => $n->id,
				'name' => $n->name,
				'zone_id' => isset($n->zoneid) ? $n->zoneid : null,
				'approx' => isset($n->approx) ? $n->approx : null,
				'x' => isset($n->coords) ? $n->coords[0] : null,
				'y' => isset($n->coords) ? $n->coords[1] : null,
			];

			$this->set_data('npc', $row);

			if (isset($n->shops))
				foreach ($n->shops as $shop_id)
				{
					$row = [
						'shop_id' => $shop_id,
						'npc_id' => $n->id,
					];

					$this->set_data('npc_shop', $row);
				}

			if (isset($n->quests))
				foreach ($n->quests as $quest_id)
				{
					$row = [
						'quest_id' => $quest_id,
						'npc_id' => $n->id,
					];

					$this->set_data('npc_quest', $row);
				}
		}

		echo __FUNCTION__ . ', ' . count($this->data['npc']) . ' rows' . PHP_EOL;
		echo 'npc_shop, ' . count($this->data['npc_shop']) . ' rows' . PHP_EOL;
		echo 'npc_quest, ' . count($this->data['npc_quest']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function npc_base($npc_base)
	{
		// Setup Data Var
		$this->data['npc_base'] = [];
		$this->data['npc_npc_base'] = [];

		// Loop through nodes
		foreach ($npc_base as $nb)
		{
			$row = [
				'name' => $nb->id, // id is actually the name
				'title' => isset($nb->title) ? $nb->title : null,
			];

			$given_id = $this->set_data('npc_base', $row);

			if (isset($nb->npcs))
				foreach ($nb->npcs as $npc_id)
				{
					$row = [
						'npc_id' => $npc_id,
						'npc_base_id' => $given_id,
					];

					$this->set_data('npc_npc_base', $row);
				}
		}

		echo __FUNCTION__ . ', ' . count($this->data['npc_base']) . ' rows' . PHP_EOL;
		echo 'npc_npc_base, ' . count($this->data['npc_npc_base']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function shop_name($shop_name)
	{
		// Setup Data Var
		$this->data['shop_name'] = [];

		// Loop through nodes
		foreach ($shop_name as $id => $name)
		{
			$row = compact('id', 'name');

			$this->set_data('shop_name', $row);
		}

		echo __FUNCTION__ . ', ' . count($this->data['shop_name']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function shop($shop)
	{
		// Setup Data Var
		$this->data['shop'] = [];
		$this->data['item_shop'] = [];

		// Loop through nodes
		foreach ($shop as $s)
		{
			$row = [
				'id' => $s->id,
				'name_id' => $s->nameId,
			];

			$this->set_data('shop', $row);

			foreach ($s->entries as $item_id)
			{
				if (gettype($item_id) == 'object')
				{
					foreach ($item_id->item as $iii)
					{
						$row = [
							'item_id' => $iii->id,
							'shop_id' => $s->id,
						];

						$this->set_data('item_shop', $row);
					}
				}
				else
				{
					$row = [
						'item_id' => $item_id,
						'shop_id' => $s->id,
					];

					$this->set_data('item_shop', $row);
				}
			}
		}

		echo __FUNCTION__ . ', ' . count($this->data['shop']) . ' rows' . PHP_EOL;
		echo 'item_shop, ' . count($this->data['item_shop']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function instance($instance)
	{
		// Setup Data Var
		$this->data['instance'] = [];
		$this->data['instance_item'] = [];
		$this->data['instance_mob'] = [];

		// Loop through nodes
		foreach ($instance as $i)
		{
			$row = [
				'id' => $i->id,
				'name' => $i->name,
				'type' => $i->type,
				'zone_id' => isset($i->zoneid) ? $i->zoneid : null,
				'icon' => $i->fullIcon,
			];

			$this->set_data('instance', $row);

			if (isset($i->fights))
				foreach ($i->fights as $f)
				{
					if (isset($f->coffer))
						foreach ($f->coffer->items as $item_id)
						{
							$row = [
								'item_id' => $item_id,
								'instance_id' => $i->id,
							];

							$this->set_data('instance_item', $row);
						}

					foreach ($f->mobs as $mob_id)
					{
						$row = [
							'mob_id' => $mob_id,
							'instance_id' => $i->id,
						];

						$this->set_data('instance_mob', $row);
					}
				}

			if (isset($i->rewards))
				foreach ($i->rewards as $item_id)
				{
					$row = [
						'item_id' => $item_id,
						'instance_id' => $i->id,
					];

					$this->set_data('instance_item', $row);
				}

			if (isset($i->coffers))
				foreach ($i->coffers as $c)
				{
					foreach ($c->items as $item_id)
					$row = [
						'item_id' => $item_id,
						'instance_id' => $i->id,
					];

					$this->set_data('instance_item', $row);
				}
		}

		echo __FUNCTION__ . ', ' . count($this->data['instance']) . ' rows' . PHP_EOL;
		echo 'instance_item, ' . count($this->data['instance_item']) . ' rows' . PHP_EOL;
		echo 'instance_mob, ' . count($this->data['instance_mob']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function quest($quest)
	{
		// Setup Data Var
		$this->data['quest'] = [];
		$this->data['quest_reward'] = [];
		$this->data['quest_required'] = [];

		// Loop through nodes
		foreach ($quest as $q)
		{
			// Get /db/data/quest/#.json
			$json_file = base_path() . '/../garlanddeploy/db/data/quest/' . $q->i . '.json';
			$q = $this->get_cleaned_json($json_file);
			$q = $q->quest;
			
			$row = [
				'id' => $q->id,
				'name' => $q->name,
				'job_category_id' => isset($q->reqs) && isset($q->reqs->jobs) ? $q->reqs->jobs[0]->id : null,
				'level' => isset($q->reqs) && isset($q->reqs->jobs) ? $q->reqs->jobs[0]->lvl : 1,
				'sort' => $q->sort,
				'zone_id' => $q->zoneid,
				'icon' => isset($q->icon) ? $q->icon : null,
				'issuer_id' => isset($q->issuer_id) ? $q->issuer_id : null,
				'target_id' => isset($q->target_id) ? $q->target_id : null,
				'genre' => $q->genre,
			];

			$this->set_data('quest', $row);

			if (isset($q->usedItems))
				foreach ($q->usedItems as $item_id)
				{
					$row = [
						'item_id' => $item_id,
						'quest_id' => $q->id,
					];

					$this->set_data('quest_required', $row);
				}

			if (isset($q->reward) && isset($q->reward->items))
				foreach ($q->reward->items as $i)
				{
					$row = [
						'item_id' => $i->id,
						'quest_id' => $q->id,
						'amount' => isset($i->num) ? $i->num : null,
					];

					$this->set_data('quest_reward', $row);
				}
		}

		echo __FUNCTION__ . ', ' . count($this->data['quest']) . ' rows' . PHP_EOL;
		echo 'quest_reward, ' . count($this->data['quest_reward']) . ' rows' . PHP_EOL;
		echo 'quest_required, ' . count($this->data['quest_required']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function achievement($achievement)
	{
		// Setup Data Var
		$this->data['achievement'] = [];

		// Loop through achievements
		foreach ($achievement as $a)
		{
			$row = [
				'id' => $a->id,
				'name' => $a->name,
				'item_id' => isset($a->item) ? $a->item : null,
				'icon' => $a->icon,
			];

			$this->set_data('achievement', $row);
		}

		echo __FUNCTION__ . ', ' . count($this->data['achievement']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function fate($fate)
	{
		// Setup Data Var
		$this->data['fate'] = [];

		// Loop through fates
		foreach ($fate as $f)
		{
			$row = [
				'id' => $f->id,
				'name' => $f->name,
				'type' => $f->type,
				'level' => $f->lvl,
				'max_level' => $f->maxlvl,
				'zone_id' => $f->zoneid,
				'x' => isset($f->coords) ? $f->coords[0] : null,
				'y' => isset($f->coords) ? $f->coords[1] : null,
			];

			$this->set_data('fate', $row);
		}

		echo __FUNCTION__ . ', ' . count($this->data['fate']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function job_category($job_category)
	{
		// Setup Data Var
		$this->data['job_category'] = [];
		$this->data['job_job_category'] = [];

		// Loop through nodes
		foreach ($job_category as $jc)
		{
			$row = [
				'id' => $jc->id,
				'name' => $jc->name,
			];

			$this->set_data('job_category', $row);

			// Loop through job_category items
			foreach ($jc->jobs as $j)
			{
				$row = [
					'job_id' => $j,
					'job_category_id' => $jc->id,
				];

				$this->set_data('job_job_category', $row);
			}
		}

		echo __FUNCTION__ . ', ' . count($this->data['job_category']) . ' rows' . PHP_EOL;
		echo 'job_job_category, ' . count($this->data['job_job_category']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function job()
	{
		// Setup Data Var
		$this->data['job'] = [];

		$job = [
			['1','Gladiator','GLA'],
			['2','Pugilist','PGL'],
			['3','Marauder','MRD'],
			['4','Lancer','LNC'],
			['5','Archer','ARC'],
			['6','Conjurer','CNJ'],
			['7','Thaumaturge','THM'],
			['8','Carpenter','CRP'],
			['9','Blacksmith','BSM'],
			['10','Armorer','ARM'],
			['11','Goldsmith','GSM'],
			['12','Leatherworker','LTW'],
			['13','Weaver','WVR'],
			['14','Alchemist','ALC'],
			['15','Culinarian','CUL'],
			['16','Miner','MIN'],
			['17','Botanist','BTN'],
			['18','Fisher','FSH'],
			['19','Paladin','PLD'],
			['20','Monk','MNK'],
			['21','Warrior','WAR'],
			['22','Dragoon','DRG'],
			['23','Bard','BRD'],
			['24','White Mage','WHM'],
			['25','Black Mage','BLM'],
			['26','Arcanist','ACN'],
			['27','Summoner','SMN'],
			['28','Scholar','SCH'],
			['29','Rogue','ROG'],
			['30','Ninja','NIN'],
			['31', 'Machinist', 'MCH'],
			['32', 'Dark Knight', 'DRK'],
			['33', 'Astrologian', 'AST'],
		];

		// Loop through nodes
		foreach ($job as $j)
		{
			$row = [
				'id' => $j[0],
				'name' => $j[1],
				'abbr' => $j[2],
			];

			$this->set_data('job', $row);
		}

		echo __FUNCTION__ . ', ' . count($this->data['job']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function venture($venture)
	{
		// Setup Data Var
		$this->data['venture'] = [];

		// Loop through nodes
		foreach ($venture as $v)
		{
			$row = [
				'id' => $v->id,
				'amounts' => isset($v->amounts) ? implode(',', $v->amounts) : null,
				'job_category_id' => $v->jobs,
				'level' => $v->lvl,
				'cost' => $v->cost,
				'minutes' => $v->minutes,
			];

			$this->set_data('venture', $row);
		}

		echo __FUNCTION__ . ', ' . count($this->data['venture']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function leve($leve)
	{
		// Setup Data Var
		$this->data['leve'] = [];
		$this->data['leve_reward'] = [];
		$this->data['leve_required'] = [];

		// Get some bonus data from the wiki
		// For an update, run /osmose
		$gamerescapewiki_data = json_decode(file_get_contents(storage_path() . '/app/osmose/cache/leves/leves.json'));

		$gamerescapewiki_leves = [];
		foreach ($gamerescapewiki_data as $gewd)
		{
			$search_name = trim(preg_replace("/\s|\-|\(.*\)| /", '', strtolower($gewd->name)));
			$gamerescapewiki_leves[$search_name] = $gewd;
		}

		// Loop through leves
		foreach ($leve as $l)
		{
			// Get /db/data/leve/#.json
			$json_file = base_path() . '/../garlanddeploy/db/data/leve/' . $l->i . '.json';
			$l = $this->get_cleaned_json($json_file);

			$rewards = isset($l->rewards) && isset($l->rewards->entries) ? $l->rewards->entries : [];
			$l = $l->leve;

			$search_name = trim(preg_replace("/\s|\-|\(.*\)| /", '', strtolower($l->name)));
			
			$row = [
				'id' => $l->id,
				'name' => $l->name,
				'type' => isset($gamerescapewiki_leves[$search_name]) ? $gamerescapewiki_leves[$search_name]->issuing_npc_information : null,
				'level' => $l->lvl,
				'job_category_id' => $l->jobCategory,
				'area_id' => $l->areaid,
				'repeats' => isset($l->repeats) ? $l->repeats : null,
				'xp' => isset($l->xp) ? $l->xp : null,
				'gil' => isset($l->gil) ? $l->gil : null,
				'plate' => $l->plate,
				'frame' => $l->frame,
				'area_icon' => $l->areaicon,
			];

			$this->set_data('leve', $row);

			foreach ($rewards as $r)
			{
				$row = [
					'item_id' => $r->item,
					'leve_id' => $l->id,
					'rate' => $r->rate * 100,
					'amount' => isset($r->amount) ? $r->amount : null,
				];

				$this->set_data('leve_reward', $row);
			}

			if (isset($l->requires))
				foreach ($l->requires as $r)
				{
					$row = [
						'item_id' => $r->item,
						'leve_id' => $l->id,
						'amount' => isset($r->amount) ? $r->amount : 1,
					];

					$this->set_data('leve_required', $row);
				}
		}

		echo __FUNCTION__ . ', ' . count($this->data['leve']) . ' rows' . PHP_EOL;
		echo 'leve_reward, ' . count($this->data['leve_reward']) . ' rows' . PHP_EOL;
		echo 'leve_required, ' . count($this->data['leve_required']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function item_category($item_category)
	{
		// Setup Data Var
		$this->data['item_category'] = [];

		// Loop through nodes
		foreach ($item_category as $ic)
		{
			$row = [
				'id' => $ic->id,
				'name' => $ic->name,
				'attribute' => isset($ic->attr) ? $ic->attr : null,
			];

			$this->set_data('item_category', $row);
		}

		echo __FUNCTION__ . ', ' . count($this->data['item_category']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function item($item)
	{
		// Setup Data Var
		$this->data['item'] = [];
		$this->data['item_venture'] = [];
		$this->data['item_attribute'] = [];
		$this->data['recipe'] = [];
		$this->data['recipe_reagents'] = [];

		// Eorzea Name Translations
		$translations = (array) json_decode(file_get_contents(storage_path() . '/app/osmose/i18n_names.json'));

		// Loop through items
		foreach ($item as $i)
		{
			// Get /db/data/item/#.json
			$json_file = base_path() . '/../garlanddeploy/db/data/item/' . $i->i . '.json';
			$i = $this->get_cleaned_json($json_file);
			$i = $i->item;

			// TMP handler
			// $whitelist = [
			// 	63,59,34,35,36,37,38,39,2,84,1,3,5,4,8,58,
			// ];
			// if ( ! in_array($i->category, $whitelist))
			// 	dd($i, 'Category OK?  Whitelist it.', $i->category);
			// if ($i->category == 58)
			// 	dd($i);
			// if ($i->id == 1609)
			// 	dd($i);
			
			$row = [
				'id' => $i->id,
				'eorzea_id' => isset($translations[$i->name]) ? $translations[$i->name]->eid : null,
				'name' => $i->name,
				'de_name' => isset($translations[$i->name]) ? $translations[$i->name]->de : $i->name,
				'fr_name' => isset($translations[$i->name]) ? $translations[$i->name]->fr : $i->name,
				'jp_name' => isset($translations[$i->name]) ? $translations[$i->name]->jp : $i->name,
				'help' => isset($i->help) ? $i->help : null,
				'price' => $i->price,
				'sell_price' => $i->sell_price,
				'ilvl' => $i->ilvl,
				'elvl' => isset($i->elvl) ? $i->elvl : null,
				'item_category_id' => $i->category,
				'unique' => isset($i->unique) ? $i->unique : null,
				'tradeable' => isset($i->tradeable) ? $i->tradeable : null,
				'desynthable' => isset($i->desynthable) ? $i->desynthable : null,
				'projectable' => isset($i->projectable) ? $i->projectable : null,
				'crestworthy' => isset($i->crestworty) ? $i->crestworty : null,
				'delivery' => isset($i->delivery) ? $i->delivery : null,
				'equip' => isset($i->equip) ? $i->equip : null,
				'repair' => isset($i->repair) ? $i->repair : null,
				'slot' => isset($i->slot) ? $i->slot : null,
				'rarity' => $i->rarity,
				'icon' => $i->icon,
				'sockets' => isset($i->sockets) ? $i->sockets : null,
				'job_category_id' => isset($i->jobs) ? $i->jobs : null,
			];

			$this->set_data('item', $row);

			if (isset($i->ventures))
				foreach ($i->ventures as $venture_id)
				{
					$row = [
						'venture_id' => $venture_id,
						'item_id' => $i->id,
					];

					$this->set_data('item_venture', $row);
				}

			if (isset($i->attr))
				foreach ((array) $i->attr as $attr => $amount)
				{
					if ($attr == 'action')
					{
						foreach ($amount as $attr => $data)
						{
							$row = [
								'item_id' => $i->id,
								'attribute' => $attr,
								'quality' => 'nq',
								'amount' => isset($data->rate) ? $data->rate : null,
								'limit' => isset($data->limit) ? $data->limit : null,
							];

							$this->set_data('item_attribute', $row);
						}

						continue;
					}

					$row = [
						'item_id' => $i->id,
						'attribute' => $attr,
						'quality' => 'nq',
						'amount' => $amount,
						'limit' => null,
					];

					$this->set_data('item_attribute', $row);
				}

			if (isset($i->attr_hq))
				foreach ((array) $i->attr_hq as $attr => $amount)
				{
					if ($attr == 'action')
					{
						foreach ($amount as $attr => $data)
						{
							$row = [
								'item_id' => $i->id,
								'attribute' => $attr,
								'quality' => 'hq',
								'amount' => isset($data->rate) ? $data->rate : null,
								'limit' => isset($data->limit) ? $data->limit : null,
							];

							$this->set_data('item_attribute', $row);
						}

						continue;
					}

					$row = [
						'item_id' => $i->id,
						'attribute' => $attr,
						'quality' => 'hq',
						'amount' => $amount,
						'limit' => null,
					];

					$this->set_data('item_attribute', $row);
				}

			if (isset($i->attr_max))
				foreach ((array) $i->attr_max as $attr => $amount)
				{
					$row = [
						'item_id' => $i->id,
						'attribute' => $attr,
						'quality' => 'max',
						'amount' => $amount,
						'limit' => null,
					];

					$this->set_data('item_attribute', $row);
				}

			if (isset($i->materia))
			{
				$row = [
					'item_id' => $i->id,
					'attribute' => $i->materia->attr,
					'quality' => 'nq',
					'amount' => $i->materia->value,
					'limit' => null,
				];

				$this->set_data('item_attribute', $row);
			}

			if (isset($i->craft))
				foreach ($i->craft as $r)
				{
					// We don't know the Recipe ID, so let the system give it one later.

					$row = [
						'item_id' => $i->id,
						'job_id' => $r->job,
						'recipe_level' => $r->rlvl,
						'level' => $r->lvl,
						'durability' => isset($r->durability) ? $r->durability : null,
						'quality' => isset($r->quality) ? $r->quality : null,
						'progress' => isset($r->progress) ? $r->progress : null,
						'yield' => isset($r->yield) ? $r->yield : 1,
						'quick_synth' => isset($r->quickSynth) ? $r->quickSynth : null,
						'hq' => isset($r->hq) ? $r->hq : null,
						'fc' => isset($r->fc) ? $r->fc : null,
					];

					$recipe_id = $this->set_data('recipe', $row);

					foreach ($r->ingredients as $in)
					{
						$row = [
							'item_id' => $in->id,
							'recipe_id' => $recipe_id,
							'amount' => $in->amount,
						];

						$this->set_data('recipe_reagents', $row);
					}
				}
		}

		echo __FUNCTION__ . ', ' . count($this->data['item']) . ' rows' . PHP_EOL;
		echo 'item_venture, ' . count($this->data['item_venture']) . ' rows' . PHP_EOL;
		echo 'item_attribute, ' . count($this->data['item_attribute']) . ' rows' . PHP_EOL;
		echo 'recipe, ' . count($this->data['recipe']) . ' rows' . PHP_EOL;
		echo 'recipe_reagents, ' . count($this->data['recipe_reagents']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	public $recipes = [],
			$item_to_recipe = [],
			$career_recipes = [],
			$career_reagents = [];
	private function career()
	{
		// Setup Data Var
		$this->data['career'] = [];
		$this->data['career_job'] = [];

		foreach ($this->data['recipe'] as $recipe_id => $recipe)
		{
			// Prepare the recipe
			$recipe['id'] = $recipe_id;
			$recipe['reagents'] = [];
			// Copy the recipe
			$this->recipes[$recipe_id] = $recipe;
			// Save the item produced in relation to the recipe
			$this->item_to_recipe[$recipe['item_id']][] = $recipe_id;
		}

		foreach ($this->data['recipe_reagents'] as $reagent)
			$this->recipes[$reagent['recipe_id']]['reagents'][] = ['item_id' => $reagent['item_id'], 'amount' => $reagent['amount']];

		foreach ($this->recipes as $recipe)
			$this->_recursive_career($recipe, $recipe['recipe_level'], $recipe['job_id'], $recipe['yield']);
		
		foreach (['recipe' => 'career_recipes', 'item' => 'career_reagents'] as $type => $key)
		{
			$data =& $this->$key;

			foreach ($data as $identifier => $i)
				foreach ($i as $level => $j)
				{
					$row = [
						'type' => $type,
						'identifier' => $identifier,
						'level' => $level,
					];

					$career_id = $this->set_data('career', $row);

					foreach ($j as $job_id => $amount)
					{
						$row = [
							'career_id' => $career_id,
							'job_id' => $job_id,
							'amount' => $amount,
						];
						
						$this->set_data('career_job', $row);
					}
				}
			unset($data);
		}

		echo __FUNCTION__ . ', ' . count($this->data['career']) . ' rows' . PHP_EOL;
		echo 'career_job, ' . count($this->data['career_job']) . ' rows' . PHP_EOL;
		$this->output_memory();
	}

	private function _recursive_career($recipe = [], $parent_level = 0, $parent_class = '', $make_this_many = 0, $depth = 0)
	{
		##echo str_pad('', $level * 2, "\t") . 'MAKING ' . ($make_this_many / $recipe->yields) . ' (' . $make_this_many . '/' . $recipe->yields . ') ' . $recipe->name . "\n";
		// For recipe W, At level X, to fulfil a Y class objective, make this many.
		// If I only need one item, but the recipe makes more than that, do the division.
		@$this->career_recipes[$recipe['id']][$parent_level][$parent_class] += $make_this_many / $recipe['yield'];

		// Loop through the reagents
		// Either add treat it as a recipe or add them to career reagents
		foreach ($recipe['reagents'] as $reagent)
			// Recipe
			if (isset($this->item_to_recipe[$reagent['item_id']]))
			{
				// Loop through the item's recipe. If the recipe is made in multiple (like bronze ingot for BSM/ARM), divide by two, because it will be reported on both. (or three, four, etc);
				foreach($this->item_to_recipe[$reagent['item_id']] as $reagent_recipe_id)
				{
					##echo str_pad('', ($level + 1) * 2, "\t") . 'LOOKING ' . ($reagent->amount * $make_this_many / $recipe->yields / count($this->item_to_recipe[$reagent->item_id])) . ' (' . $reagent->amount . '*' . $make_this_many . '/' . $recipe->yields . '/' . count($this->item_to_recipe[$reagent->item_id]) . ') ' . $this->recipes[$reagent_recipe_id]->name . "\n";
					$this->_recursive_career($this->recipes[$reagent_recipe_id], $parent_level, $parent_class, $reagent['amount'] * $make_this_many / $recipe['yield'] / count($this->item_to_recipe[$reagent['item_id']]), $depth + 1);
				}
			}
			// Reagent
			else
			{
				##echo str_pad('', ($level + 1) * 2, "\t") . 'ADDING ' . ($reagent->amount * $make_this_many / $recipe->yields) . ' (' . $reagent->amount . '*' . $make_this_many . '/' . $recipe->yields . ') ' . $reagent->item_id . "\n";
				// For item W, at level X, to fulfil a Y class objective, gather this many
				@$this->career_reagents[$reagent['item_id']][$parent_level][$parent_class] += $reagent['amount'] * $make_this_many / $recipe['yield'];
			}
	}

	/**
	 * Helper Functions
	 */
	
	private function batch_insert()
	{
		$batch_limit = 300;

		foreach ($this->data as $table => $rows)
		{
			echo '--------------------------------------------------' . PHP_EOL;
			// $count = 0;
			foreach (array_chunk($rows, $batch_limit) as $batch_id => $data)
			{
				echo 'Inserting ' . count($data) . ' rows for ' . $table . ' (' . ($batch_id + 1) . ')' . PHP_EOL;
				
				// if (++$count == 29 && $table == 'item_shop')
				// 	dd($data);

				$values = $pdo = [];
				foreach ($data as $row)
				{
					$values[] = '(' . str_pad('', count($row) * 2 - 1, '?,') . ')';
					
					// Cleanup value, if FALSE set to NULL
					foreach ($row as $value)
						$pdo[] = $value === FALSE ? NULL : $value;
				}

				$keys = ' (`' . implode('`,`', array_keys($data[0])) . '`)';

				\DB::insert('INSERT IGNORE INTO ' . $table . $keys . ' VALUES ' . implode(',', $values), $pdo);
			}
		}
	}
	
	private function output_memory()
	{
		echo '@' . $this->human_readable(memory_get_usage()) . PHP_EOL;
	}

	protected $data = [];
	
	private function get_data($table, $id)
	{
		return isset($this->data[$table][$id]) ? $this->data[$table][$id] : false;
	}
	
	private function set_data($table, $row, $id = null)
	{
		// If id is null, use the length of the existing data, or check in the $row for it
		$id = $id ?: (isset($row['id']) ? $row['id'] : count($this->data[$table]) + 1);

		$this->data[$table][$id] = $row;

		return $id;
	}

	private function human_readable($size)
	{
		$filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
		return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) .$filesizename[$i] : '0 Bytes';
	}

	private function get_cleaned_json($path)
	{
		$content = stripslashes(file_get_contents($path));

		// http://stackoverflow.com/questions/17219916/json-decode-returns-json-error-syntax-but-online-formatter-says-the-json-is-ok
		for ($i = 0; $i <= 31; ++$i) { 
			$content = str_replace(chr($i), "", $content); 
		}
		$content = str_replace(chr(127), "", $content);

		// This is the most common part
		$content = $this->binary_fix($content);

		return json_decode($content);
	}

	private function binary_fix($string)
	{
		// Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
		// here we detect it and we remove it, basically it's the first 3 characters 
		if (0 === strpos(bin2hex($string), 'efbbbf')) {
		   $string = substr($string, 3);
		}
		return $string;
	}
}
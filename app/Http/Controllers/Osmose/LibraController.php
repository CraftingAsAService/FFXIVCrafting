<?php namespace App\Http\Controllers\Osmose;

use App\Models\Osmose\AppData;
use App\Models\Osmose\FileHandler;
use App\Models\Osmose\Career;
use App\Models\Osmose\Nodes;

class LibraController extends \App\Http\Controllers\Controller
{

	public function __construct()
	{
		view()->share('active', 'osmose');
	}

	public $all = false;

	public function getAll()
	{
		$this->all = true;

		// Loop through the methods until it doesn't start with "get", 
		// this will eliminate all the BaseController mojo
		foreach (array_diff(get_class_methods($this), array('getAll')) as $method)
		{
			if (substr($method, 0, 3) != 'get')
				continue;

			$this->$method();
		}

		flash()->message('All tables parsed');

		return redirect('/osmose');
	}

	// Raw Data on classes
	public function getClassjob()
	{
		$results = \DB::connection('libra')->select('SELECT * FROM ClassJob');
		$data = array();

		foreach ($results as $row)
			$data[] = array(
				'id' => $row->Key,
				'name' => array(
					'en' => $row->Name_en,
					'ja' => $row->Name_ja,
					'fr' => $row->Name_fr,
					'de' => $row->Name_de
				),
				'abbr' => array(
					'en' => $row->Abbreviation_en,
					'ja' => $row->Abbreviation_ja,
					'fr' => $row->Abbreviation_fr,
					'de' => $row->Abbreviation_de
				),
				'is_job' => $row->IsJob,
				'rank' => $row->UIPriority
			);

		FileHandler::save('ClassJob', $data);

		flash()->message('ClassJob completed.');

		if ( ! $this->all) return redirect('/osmose');
	}

	// Items can be Categorized into what classes can equip them
	public function getClassjobcategory()
	{
		$results = \DB::connection('libra')->select('SELECT * FROM ClassJobCategory');
		$data = array();

		foreach ($results as $row)
		{
			// First and last classjob's are bad
			$classjob = explode(',', $row->classjob);
			array_pop($classjob);
			array_shift($classjob);

			$data[] = array(
				'id' => $row->Key,
				'name' => array(
					'en' => $row->Name_en,
					'ja' => $row->Name_ja,
					'fr' => $row->Name_fr,
					'de' => $row->Name_de
				),
				'classjob' => implode(',', $classjob)
			);
		}

		FileHandler::save('ClassJobCategory', $data);

		flash()->message('ClassJobCategory completed.');

		if ( ! $this->all) return redirect('/osmose');
	}

	public function getPlacename()
	{
		$results = \DB::connection('libra')->select('SELECT * FROM PlaceName');
		$data = array();

		foreach ($results as $row)
		{
			$data[] = array(
				'id' => $row->Key,
				'region' => $row->region,
				'name' => array(
					'en' => $row->SGL_en,
					'ja' => $row->SGL_ja,
					'fr' => $row->SGL_fr,
					'de' => $row->SGL_de
				)
			);
		}

		FileHandler::save('PlaceName', $data);

		flash()->message('PlaceName completed.');

		if ( ! $this->all) return redirect('/osmose');
	}

	public function getItemuikind()
	{
		return $this->simple('ItemUIKind');
	}

	public function getItemuicategory()
	{
		$results = \DB::connection('libra')->select('SELECT * FROM ItemUICategory');
		$data = array();

		foreach ($results as $row)
		{
			$data[] = array(
				'id' => $row->Key,
				'kind' => $row->Kind,
				'name' => array(
					'en' => $row->Name_en,
					'ja' => $row->Name_ja,
					'fr' => $row->Name_fr,
					'de' => $row->Name_de
				),
				'rank' => $row->Priority
			);
		}

		FileHandler::save('ItemUICategory', $data);

		flash()->message('ItemUICategory completed.');

		if ( ! $this->all) return redirect('/osmose');
	}

	public function getItemcategory()
	{
		return $this->simple('ItemCategory');
	}

	public function getItemseries()
	{
		return $this->simple('ItemSeries');
	}

	public function getItemspecialbonus()
	{
		return $this->simple('ItemSpecialBonus');
	}

	public function getBaseparam()
	{
		return $this->simple('BaseParam');
	}

	public function getItem()
	{
		$results = \DB::connection('libra')->select('SELECT * FROM Item WHERE Legacy = 0');
		$contents = array();

		foreach ($results as $row)
		{
			// Drop out weird "Fits" stuff
			$bad_fits = array('Fits: Game Masters', 'Fits: Hyur', 'Fits: Elezen', 'Fits: Lalafell', 'Fits: Miqo\'te', 'Fits: Roegadyn');

			foreach ($bad_fits as $bad)
				if (strpos($row->Help_en, $bad) !== FALSE)
					continue 2;

			// First and last classjob's are bad
			$classjob = explode(',', $row->classjob);
			array_pop($classjob);
			array_shift($classjob);

			// Clean up Data
			$extra = array();
			if ($row->data)
			{
				$data = json_decode($row->data);

				if (isset($data->bonus))
				{
					$extra['stats'] = array();

					foreach ($data->bonus as $ignore)
						foreach ($ignore as $stat => $amount)
							$extra['stats'][$stat]['nq'] = $amount;

					if (isset($data->bonus_hq))
						foreach ($data->bonus_hq as $ignore)
							foreach ($ignore as $stat => $amount)
								$extra['stats'][$stat]['hq'] = $amount;
				}

				if (isset($data->series_bonus))
				{
					if (isset($data->series_bonus->SpecialBonus))
						$extra['special_bonus'] = $data->series_bonus->SpecialBonus;

					if (isset($data->series_bonus->bonus))
						foreach ($data->series_bonus->bonus as $ignore)
							foreach ($ignore as $stat => $amount)
								$extra['bonus_stats'][$stat] = $amount;
				}

				if (isset($data->action))
				{
					$extra['boost'] = array();

					foreach ($data->action as $ignore)
						foreach ($ignore as $stat => $changes)
							foreach ((array) $changes as $name => $amount)
								$extra['boost'][$stat]['nq'][$name] = $amount;


					if (isset($data->action_hq))
						foreach ($data->action_hq as $ignore)
							foreach ($ignore as $stat => $changes)
								foreach ((array) $changes as $name => $amount)
									$extra['boost'][$stat]['hq'][$name] = $amount;
				}

				if (isset($data->effect))
				{
					if ( ! isset($extra['boost']))
						$extra['boost'] = array();

					foreach ($data->effect as $stat_id => $amount)
						$extra['boost'][$stat_id] = $amount;
				}

				if (isset($data->color))
					$extra['color'] = implode(',', $data->color);

				if (isset($data->sell_price))
					$extra['sell_price'] = $data->sell_price;
				if (isset($data->CondClassJob))
					$extra['classjob_category'] = $data->CondClassJob;
				if (isset($data->MateriaSocket))
					$extra['materia'] = $data->MateriaSocket;
				if (isset($data->DisablePassedOthers))
					$extra['untradable'] = $data->DisablePassedOthers;
				if (isset($data->OnlyOne))
					$extra['unique'] = $data->OnlyOne;
				if (isset($data->achievement))
					$extra['achievable'] = true;
				if (isset($data->quest))
					$extra['rewarded'] = true;
				if (isset($data->instance_content) && ! empty($data->instance_content))
					$extra['dungeon_drop'] = implode(',', $data->instance_content);

				// On second though, we don't need Repair info

				// if (isset($data->repair_price))
				// 	$extra['repair_price'] = $data->repair_price;
				// if (isset($data->RepairItem))
				// 	$extra['repair_with'] = $data->RepairItem;
				// if (isset($data->RepairClassJob))
				// 	$extra['repair_class'] = $data->Repair;
				
				// Basic Param I think indicates that it's not a piece of equipment
				// And we don't care about quests, or who it drops/sells from (as we get that elsewhere)
				// I don't know what MaterializeType is... They're all values 1-12 (sans 10), and nothing else matches that setup that makes sense.
				// Don't care about crests or dyes
				unset($data->basic_param, $data->quest, $data->bnpc, $data->shopnpc, $data->classjob, $data->basic_param_hq, $data->MaterializeType, $data->MateriaSocket);
				unset($data->sell_price, $data->repair_price, $data->RepairItem, $data->Repair, $data->CondClassJob, $data->recipe, $data->bonus, $data->bonus_hq);
				unset($data->DisablePassedOthers, $data->OnlyOne, $data->Series, $data->series_bonus, $data->Crest, $data->Stain, $data->achievement, $data->RecastTime);
				unset($data->action, $data->action_hq, $data->effect, $data->color);
				unset($data->instance_content);


				if (count(get_object_vars($data)) != 0)
				{
					echo "<h1>Missed this, Item</h1>";
					var_dump($row);
					var_dump($extra);
					dd($data);
				}
			}

			$contents[] = array(
				'id' => $row->Key,
				'category' => $row->Category,
				'ui_category' => $row->UICategory,
				'ui_name' => array(
					'en' => $row->UIName_en,
					'ja' => $row->UIName_ja,
					'fr' => $row->UIName_fr,
					'de' => $row->UIName_de
				),
				// Ignoring Help text
				'level' => $row->Level,
				'equip_level' => $row->EquipLevel,
				'rarity' => $row->Rarity,
				'hq' => $row->HQ,
				'special_bonus' => $row->SpecialBonus,
				'series' => $row->Series,
				'slot' => $row->Slot,
				'stats' => array(
					'nq' => array(
						'damage' => $row->Damage,
						'magic_damage' => $row->MagicDamage,
						'defense' => $row->Defense,
						'magic_defense' => $row->MagicDefense,
						'shield_rate' => $row->ShieldRate,
						'shield_block_rate' => $row->ShieldBlockRate,
						'attack_interval' => $row->AttackInterval,
						'auto_attack' => $row->AutoAttack
					),
					'hq' => array(
						'damage' => $row->Damage_hq,
						'magic_damage' => $row->MagicDamage_hq,
						'defense' => $row->Defense_hq,
						'magic_defense' => $row->MagicDefense_hq,
						'shield_rate' => $row->ShieldRate_hq,
						'shield_block_rate' => $row->ShieldBlockRate_hq,
						'attack_interval' => $row->AttackInterval_hq,
						'auto_attack' => $row->AutoAttack_hq
					)
				),
				'icon' => array(
					'nq' => $row->icon,
					'hq' => $row->icon_hq
				),
				'price' => array(
					'min' => $row->Price,
					'max' => $row->PriceMin
				),
				'classjob' => implode(',', $classjob),
				'rank' => $row->SortId,
				'extra' => $extra
			);
		}

		FileHandler::save('Item', $contents);

		flash()->message('Item completed.');

		if ( ! $this->all) return redirect('/osmose');
	}

	public function getRace()
	{
		return $this->simple('Race');
	}

	public function getBnpcname()
	{
		$results = \DB::connection('libra')->select('SELECT * FROM BNpcName');
		$contents = array();

		foreach ($results as $row)
		{
			// Clean up Data
			$extra = array();
			if ($row->data)
			{
				$data = json_decode($row->data);

				if (isset($data->item))
					$extra['drops'] = $data->item;

				if (isset($data->region)) // && $data->nonpop
					foreach ($data->region as $region => $loc)
						foreach ($loc as $area => $levels)
							$extra['location'][$region][$area . (isset($data->nonpop) && in_array($area, $data->nonpop) ? '*' : '')][] = implode(',', $levels);
						// Warning, levels may be "??"
				
				// Testing for more
				unset($data->item, $data->region, $data->nonpop);
				// App v1.4
				unset($data->instance_contents);

				if (count(get_object_vars($data)) != 0)
				{
					echo "<h1>Missed this, Bnpcname</h1>";
					var_dump($row);
					var_dump($extra);
					dd($data);
				}
			}

			$contents[] = array(
				'id' => $row->Key,
				'name' => array(
					'en' => $row->SGL_en,
					'ja' => $row->SGL_ja,
					'fr' => $row->SGL_fr,
					'de' => $row->SGL_de
				),
				// Ignoring Area, covered by extra
				'extra' => $extra
			);
		}

		FileHandler::save('BNpcName', $contents);

		flash()->message('BNpcName completed.');

		if ( ! $this->all) return redirect('/osmose');
	}

	public function getEnpcresident()
	{
		$results = \DB::connection('libra')->select('SELECT * FROM ENpcResident');
		$contents = array();

		foreach ($results as $row)
		{
			// First and last area's are bad, and there's only one
			$area = array_values(array_diff(explode(',', $row->area), array('')))[0];
			//$region = array_values(array_diff(explode(',', $row->region), array('')))[0];

			// Clean up Data
			$extra = array();
			if ($row->data)
			{
				$data = json_decode($row->data);

				// if (isset($data->item))
				// 	$extra['drops'] = $data->item;

				if (isset($data->coordinate))
				{
					$extra['coords'] = array();
					foreach ($data->coordinate->$area as $c)
						$extra['coords'][] = implode(',', $c);
					$extra['coords'] = array_values(array_unique($extra['coords']));
				}

				if (isset($data->shop))
				{
					$extra['shop'] = array();
					foreach($data->shop as $shop)
						foreach ($shop as $label => $items)
							foreach ($items as $ignore)
								foreach ($ignore as $item_id => $rgb)
									$extra['shop'][$label][$item_id] = implode(',', $rgb);
				}
				
				// We don't care about quests
				unset($data->client_quest, $data->quest);
				unset($data->coordinate, $data->shop);

				if (count(get_object_vars($data)) != 0)
				{
					echo "<h1>Missed this, Enpcresident</h1>";
					var_dump($row);
					var_dump($extra);
					dd($data);
				}
			}

			// Actually, if they don't have a shop, we don't want them
			if ( ! $row->has_shop)
				continue;

			$contents[] = array(
				'id' => $row->Key,
				'name' => array(
					'en' => $row->SGL_en,
					'ja' => $row->SGL_ja,
					'fr' => $row->SGL_fr,
					'de' => $row->SGL_de
				),
				'area' => $area,
				//'region' => $region, // 1.1.3 removed this
				'has_shop' => $row->has_shop,
				'extra' => $extra//,
				//'has_quest' => $row->Key
			);
		}

		FileHandler::save('ENpcResident', $contents);

		flash()->message('ENpcResident completed.');

		if ( ! $this->all) return redirect('/osmose');
	}

	public function getShop() // And BeastTribe
	{
		// Select BeastTribe data first so we wind up with the Shop Key
		$results = \DB::connection('libra')->select('SELECT BeastTribe.*, Shop.* FROM Shop LEFT JOIN BeastTribe ON Shop.BeastTribe = BeastTribe.Key');
		$data = array();

		foreach ($results as $row)
		{
			$tmp = array(
				'id' => $row->Key,
				'name' => array(
					'en' => $row->Name_en,
					'ja' => $row->Name_ja,
					'fr' => $row->Name_fr,
					'de' => $row->Name_de
				)
			);

			if ($row->BeastTribe)
				$tmp['beast_tribe'] = array(
					'en' => $row->SGL_en,
					'ja' => $row->SGL_ja,
					'fr' => $row->SGL_fr,
					'de' => $row->SGL_de
				);

			$data[] = $tmp;
		}

		FileHandler::save('Shop', $data);

		flash()->message('Shop completed.');

		if ( ! $this->all) return redirect('/osmose');
	}

	public function getRecipeelement()
	{
		return $this->simple('RecipeElement');
	}

	public function getNotebookdivision()
	{
		return $this->simple('NotebookDivision');
	}

	public function getRecipe() // and CraftType
	{
		$results = \DB::connection('libra')->select('SELECT * FROM CraftType');
		$type_to_class = array();
		foreach($results as $row)
			$type_to_class[$row->Key] = $row->ClassJob;

		$results = \DB::connection('libra')->select('SELECT * FROM Recipe');

		$content = array();

		foreach ($results as $row)
		{
			// Clean up Data
			$extra = array();
			if ($row->data)
			{
				$data = json_decode($row->data);

				$extra['reagents'] = array();

				foreach (array('Crystal', 'Item') as $var)
					if (isset($data->$var))
						foreach ($data->$var as $ignore)
							foreach ($ignore as $item_id => $required)
								$extra['reagents'][$item_id] = $required;

				if (isset($data->material_point))
					$extra['durability'] = $data->material_point;
				if (isset($data->quality_max))
					$extra['quality_max'] = $data->quality_max;
				if (isset($data->work_max))
					$extra['difficulty'] = $data->work_max;

				unset($data->Crystal, $data->Item);
				unset($data->material_point, $data->quality_max, $data->work_max);

				if (count(get_object_vars($data)) != 0)
				{
					echo "<h1>Missed this, Recipe</h1>";
					var_dump($row);
					var_dump($extra);
					dd($data);
				}
			}

			$content[] = array(
				'id' => $row->Key,
				// ignoring CanAutoCraft
				'can_hq' => $row->CanHq,
				'item_id' => $row->CraftItemId,
				'yields' => $row->CraftNum,
				'classjob' => $type_to_class[$row->CraftType],
				'level' => $row->Level,
				'levelView' => $row->levelView,
				'stars' => $row->levelDiff,
				'element' => $row->Element,
				'required' => array(
					'craftsmanship' => $row->NeedCraftmanship,
					'control' => $row->NeedControl
				),
				'rank' => $row->Number,
				'extra' => $extra
			);
		}

		FileHandler::save('Recipe', $content);

		flash()->message('Recipe completed.');

		if ( ! $this->all) return redirect('/osmose');
	}

	public function getGuardiandeity()
	{
		return $this->simple('GuardianDeity');
	}

	public function getCareers()
	{
		$c = new Career();
		$c->run();

		flash()->message('Careers completed.');

		if ( ! $this->all) return redirect('/osmose');
	}

	public function getNodes()
	{
		$n = new Nodes();
		$n->run();

		flash()->message('Nodes completed.');

		if ( ! $this->all) return redirect('/osmose');
	}

	// KEEP THIS LAST, it's important to getAll in this position in the code

	private function simple($table)
	{
		$results = \DB::connection('libra')->select('SELECT * FROM ' . $table);
		$data = array();

		foreach ($results as $row)
		{
			$data[] = array(
				'id' => $row->Key,
				'name' => array(
					'en' => $row->Name_en,
					'ja' => $row->Name_ja,
					'fr' => $row->Name_fr,
					'de' => $row->Name_de
				)
			);
		}

		FileHandler::save($table, $data);

		flash()->message($table . ' completed.');

		if ( ! $this->all) return redirect('/osmose');
	}

}

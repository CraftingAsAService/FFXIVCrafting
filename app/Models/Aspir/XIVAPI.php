<?php

/**
 * XIVAPI
 * 	Get data from XIVAPI
 */

namespace App\Models\Aspir;

use Cache;

class XIVAPI
{

	public $aspir;

	private $api;

	public $limit = null;

	public function __construct(&$aspir)
	{
		$this->api = new \XIVAPI\XIVAPI();
		$this->api->environment->key(config('services.xivapi.key'));

		$this->aspir =& $aspir;
	}

	public function achievements()
	{
		$this->loopEndpoint('achievement', [
			'ID',
			'Name',
			'ItemTargetID',
			'IconID',
		], function($data) {
			// We only care about achievements that provide an item
			if ( ! $data->ItemTargetID)
				return;

			$this->aspir->setData('achievement', [
				'id'      => $data->ID,
				'name'    => $data->Name,
				'item_id' => $data->ItemTargetID,
				'icon'    => $data->IconID,
			], $data->ID);
		});
	}

	public function locations()
	{
		$this->loopEndpoint('placename', [
			'ID',
			'Name',
			'Maps.0.PlaceNameRegionTargetID',
		], function($data) {
			// Skip empty names
			if ($data->Name == '')
				return;

			$this->aspir->setData('location', [
				'id'          => $data->ID,
				'name'        => $data->Name,
				'location_id' => $data->Maps[0]->PlaceNameRegionTargetID ?? null,
			], $data->ID);
		});
	}

	public function nodes()
	{
		// You must be looking at gathering items.  What you're looking for there is the GatheringPoint table, which has a PlaceName (i.e., Cedarwood) and a TerritoryType.  The TerritoryType then has the PlaceName you're looking for - Lower La Noscea.
		// Be warned that what I referred to as a 'node' is really a GatheringPointBase.  There are lots of gathering points with the same items because they appear in different places on the map.
		$this->loopEndpoint('gatheringpointbase', [
			'ID',
			'GatheringType.ID',
			'GatheringLevel',
			'GameContentLinks.GatheringPoint.GatheringPointBase.0',
			// Items go from Item0 to Item7; There's rumor of this being Array'd
			'Item0',
			'Item1',
			'Item2',
			'Item3',
			'Item4',
			'Item5',
			'Item6',
			'Item7',
		], function($data) {
			if ($data->GameContentLinks->GatheringPoint->GatheringPointBase['0'] == null)
				return;

			// Loop through Item#
			$hasItems = false;
			foreach (range(0,7) as $i)
				if ($data->{'Item' . $i})
				{
					$hasItems = true;
					$this->aspir->setData('item_node', [
						'item_id' => $data->{'Item' . $i},
						'node_id' => $data->ID,
					]);
				}

			// Don't include the node if there aren't any items attached
			if ( ! $hasItems)
				return;

			$gp = $this->request('gatheringpoint/' . $data->GameContentLinks->GatheringPoint->GatheringPointBase['0'], ['columns' => [
				'PlaceName.ID',
				'PlaceName.Name',
				'TerritoryType.PlaceName.ID',
				'TerritoryType.PlaceName.Name',
			]]);

			$this->aspir->setData('node', [
				'id'          => $data->ID,
				'name'        => $gp->PlaceName->Name,
				'type'        => $data->GatheringType->ID,
				'level'       => $data->GatheringLevel,
				'zone_id'     => $gp->TerritoryType->PlaceName->ID,
				'area_id'     => $gp->PlaceName->ID,
				'coordinates' => null, // Filled in later
			], $data->ID);
		});
	}

	public function fishingSpots()
	{
		$this->loopEndpoint('fishingspot', [
			'ID',
			'PlaceName.Name',
			'FishingSpotCategory',
			'GatheringLevel',
			'Radius',
			'X',
			'Z',
			'TerritoryType.PlaceName.ID',
			'PlaceName.ID',
			// Items go from Item0 to Item9; There's rumor of this being Array'd
			'Item0.ID',
			'Item0.LevelItem',
			'Item1.ID',
			'Item1.LevelItem',
			'Item2.ID',
			'Item2.LevelItem',
			'Item3.ID',
			'Item3.LevelItem',
			'Item4.ID',
			'Item4.LevelItem',
			'Item5.ID',
			'Item5.LevelItem',
			'Item6.ID',
			'Item6.LevelItem',
			'Item7.ID',
			'Item7.LevelItem',
			'Item8.ID',
			'Item8.LevelItem',
			'Item9.ID',
			'Item9.LevelItem',
		], function($data) {
			// Skip empty names
			if ($data->PlaceName->Name == '')
				return;

			// Loop through Item#
			$hasItems = false;
			foreach (range(0,9) as $i)
				if ($data->{'Item' . $i}->ID)
				{
					$hasItems = true;
					$this->aspir->setData('fishing_item', [
						'item_id'    => $data->{'Item' . $i}->ID,
						'fishing_id' => $data->ID,
						'level'      => $data->{'Item' . $i}->LevelItem,
					]);
				}

			// Don't include the fishing node if there aren't any items attached
			if ( ! $hasItems)
				return;

			$this->aspir->setData('fishing', [
				'id'          => $data->ID,
				'name'        => $data->PlaceName->Name,
				'category_id' => $data->FishingSpotCategory,
				'level'       => $data->GatheringLevel,
				'radius'      => $data->Radius,
				'x'           => 1 + ($data->X / 50), // Translate a number like 1203 to something like 25.06
				'y'           => 1 + ($data->Z / 50),
				'zone_id'     => $data->TerritoryType->PlaceName->ID,
				'area_id'     => $data->PlaceName->ID,
			], $data->ID);
		});
	}

	public function mobs()
	{
		$this->loopEndpoint('bnpcname', [
			'ID',
			'Name',
		], function($data) {
			// Skip empty names
			if ($data->Name == '')
				return;

			$this->aspir->setData('mob', [
				'id'      => $data->ID,
				'name'    => $data->Name,
				'quest'   => null, // Filled in later
				'level'   => null, // Filled in later
				'zone_id' => null, // Filled in later
			], $data->ID);
		});
	}

	public function npcs()
	{
		// 3000 calls were taking over the allotted 10s call limit imposed by XIVAPI's Guzzle Implementation
		$this->limit = 1000;

		$this->loopEndpoint('enpcresident', [
			'ID',
			'Name',
			'Quests.*.ID',
			'GilShop.*.ID',
			'GilShop.*.Name',
			'SpecialShop.*.ID',
			'SpecialShop.*.Name',
		], function($data) {
			// Skip empty names
			if ($data->Name == '')
				return;

			$this->aspir->setData('npc', [
				'id'      => $data->ID,
				'name'    => $data->Name,
				'zone_id' => null, // Filled in later
				'approx'  => null, // Filled in later
				'x'       => null, // Filled in later
				'y'       => null, // Filled in later
			], $data->ID);

			if ($data->Quests[0]->ID)
				foreach ($data->Quests as $quest)
					if ($quest->ID)
						$this->aspir->setData('npc_quest', [
							'quest_id' => $quest->ID,
							'npc_id' => $data->ID,
						]);

			foreach (['GilShop', 'SpecialShop'] as $shopType)
				if ($data->$shopType)
					foreach ($data->$shopType as $shop)
						if ($shop->ID)
						{
							$this->aspir->setData('shop', [
								'id'   => $shop->ID,
								'name' => $shop->Name,
							], $shop->ID);

							$this->aspir->setData('npc_shop', [
								'shop_id' => $shop->ID,
								'npc_id' => $data->ID,
							]);
						}
		}, [
			'ids' => function($value, $key) {
				// After ID 1028800, Quests, GilShop and SpecialShop all disappear, causing errors
				return $value < 1028800;
			}
		]);

		$limit = null;
	}

	public function quests()
	{
		// 3000 calls were taking over the allotted 10s call limit imposed by XIVAPI's Guzzle Implementation
		$this->limit = 500;

		$this->loopEndpoint('quest', [
			'ID',
			'Name',
			'ClassJobCategory0TargetID',
			'ClassJobLevel0',
			'SortKey',
			'PlaceNameTargetID',
			'IconID',
			'IssuerStart',
			'TargetEnd',
			'JournalGenreTargetID',
			// Rewards; 00-05 are guaranteed. 10-14 are choices. Catalysts are likely guaranteed as well.
			// 	Make sure the Target's are "Item"
			'ItemReward00',
			'ItemCountReward00',
			'ItemReward01',
			'ItemCountReward01',
			'ItemReward02',
			'ItemCountReward02',
			'ItemReward03',
			'ItemCountReward03',
			'ItemReward04',
			'ItemCountReward04',
			'ItemReward05',
			'ItemCountReward05',
			'ItemReward10Target',
			'ItemReward10TargetID',
			'ItemCountReward10',
			'ItemReward11Target',
			'ItemReward11TargetID',
			'ItemCountReward11',
			'ItemReward12Target',
			'ItemReward12TargetID',
			'ItemCountReward12',
			'ItemReward13Target',
			'ItemReward13TargetID',
			'ItemCountReward13',
			'ItemReward14Target',
			'ItemReward14TargetID',
			'ItemCountReward14',
			'ItemCatalyst0Target',
			'ItemCatalyst0TargetID',
			'ItemCountCatalyst0',
			'ItemCatalyst1Target',
			'ItemCatalyst1TargetID',
			'ItemCountCatalyst1',
			'ItemCatalyst2Target',
			'ItemCatalyst2TargetID',
			'ItemCountCatalyst2',
			// Required; There's like 40 of these, but I'm only going to go for 10
			'ScriptInstruction0',
			'ScriptArg0',
			'ScriptInstruction1',
			'ScriptArg1',
			'ScriptInstruction2',
			'ScriptArg2',
			'ScriptInstruction3',
			'ScriptArg3',
			'ScriptInstruction4',
			'ScriptArg4',
			'ScriptInstruction5',
			'ScriptArg5',
			'ScriptInstruction6',
			'ScriptArg6',
			'ScriptInstruction7',
			'ScriptArg7',
			'ScriptInstruction8',
			'ScriptArg8',
			'ScriptInstruction9',
			'ScriptArg9',
		], function($data) {
			// Skip empty names
			if ($data->Name == '')
				return;

			$this->aspir->setData('quest', [
				'id'              => $data->ID,
				'name'            => $data->Name,
				'job_category_id' => $data->ClassJobCategory0TargetID,
				'level'           => $data->ClassJobLevel0,
				'sort'            => $data->SortKey,
				'zone_id'         => $data->PlaceNameTargetID,
				'icon'            => $data->IconID,
				'issuer_id'       => $data->IssuerStart,
				'target_id'       => $data->TargetEnd,
				'genre'           => $data->JournalGenreTargetID,
			], $data->ID);

			// Required Items
			foreach (range(0, 9) as $slot)
				if (substr($data->{'ScriptInstruction' . $slot}, 0, 5) == 'RITEM')
					$this->aspir->setData('quest_required', [
						'item_id'  => $data->{'ScriptArg' . $slot},
						'quest_id' => $data->ID,
					]);

			// Reward Items, Guaranteed, 00-05
			foreach (range(0, 5) as $slot)
				if ($data->{'ItemReward0' . $slot})
					$this->aspir->setData('quest_reward', [
						'item_id'  => $data->{'ItemReward0' . $slot},
						'quest_id' => $data->ID,
						'amount'   => $data->{'ItemCountReward0' . $slot},
					]);

			// Reward Items, Optional, 10-14
			foreach (range(10, 14) as $slot)
				if ($data->{'ItemReward' . $slot . 'TargetID'} && $data->{'ItemReward' . $slot . 'Target'} == 'Item')
					$this->aspir->setData('quest_reward', [
						'item_id'  => $data->{'ItemReward' . $slot . 'TargetID'},
						'quest_id' => $data->ID,
						'amount'   => $data->{'ItemCountReward' . $slot},
					]);

			// Reward Items/Catalyst Items
			foreach (range(0, 2) as $slot)
				if ($data->{'ItemCatalyst' . $slot . 'TargetID'} && $data->{'ItemCatalyst' . $slot . 'Target'} == 'Item')
					$this->aspir->setData('quest_reward', [
						'item_id'  => $data->{'ItemCatalyst' . $slot . 'TargetID'},
						'quest_id' => $data->ID,
						'amount'   => $data->{'ItemCountCatalyst' . $slot},
					]);
		});

		$limit = null;
	}

	public function instances()
	{
		$this->loopEndpoint('instancecontent', [
			'ID',
			'Name',
			'ContentType.ID',
			'ContentFinderCondition.TerritoryType.PlaceName.ID',
			'ContentFinderCondition.ImageID',
		], function($data) {
			// Skip empty names
			if ($data->Name == '')
				return;

			$this->aspir->setData('instance', [
				'id'      => $data->ID,
				'name'    => $data->Name,
				'type'    => $data->ContentType->ID,
				'zone_id' => $data->ContentFinderCondition->TerritoryType->PlaceName->ID,
				'icon'    => $data->ContentFinderCondition->ImageID,
			], $data->ID);
		});
	}

	public function jobs()
	{
		$this->loopEndpoint('classjob', [
			'ID',
			'NameEnglish', // `NameEnglish` is capitalized; `Name` is not
			'Abbreviation',
		], function($data) {
			$this->aspir->setData('job', [
				'id'   => $data->ID,
				'name' => $data->NameEnglish,
				'abbr' => $data->Abbreviation,
			], $data->ID);
		});
	}

	public function jobCategories()
	{
		// classjobcategory has a datapoint for every job abbreviation
		//  Dynamically collect them. The key's will stay as the ID, which will be helpful
		$abbreviations = collect($this->aspir->data['job'])->map(function($job) {
			return $job['abbr'];
		});

		$this->loopEndpoint('classjobcategory', array_merge([
			'ID',
			'Name',
		], $abbreviations->toArray()), function($data) use ($abbreviations) {
			$this->aspir->setData('job_category', [
				'id'   => $data->ID,
				'name' => $data->Name,
			]);

			foreach ($abbreviations as $jobId => $abbr)
				if ($data->$abbr == 1)
					$this->aspir->setData('job_job_category', [
						'job_id'          => $jobId,
						'job_category_id' => $data->ID,
					]);
		});
	}

	public function ventures()
	{
		$this->loopEndpoint('retainertask', [
			'ID',
			'ClassJobCategory.ID',
			'RetainerLevel',
			'MaxTimeMin',
			'VentureCost',
			'IsRandom',
			'Task',
		], function($data) {
			// The Quantities are only applicable for "Normal" Ventures
			$quantities = [];
			$name = null;
			if ($data->IsRandom)
			{
				$q = $this->request('retainertaskrandom/' . $data->Task, ['columns' => [
					'Name',
				]]);

				$name = $q->Name;
			}
			else
			{
				$q = $this->request('retainertasknormal/' . $data->Task, ['columns' => [
					'Quantity0',
					'Quantity1',
					'Quantity2',
					'ItemTarget',
					'ItemTargetID',
				]]);

				foreach (range(0, 2) as $slot)
					$quantities[] = $q->{'Quantity' . $slot};

				if ($q->ItemTarget == 'Item' && $q->ItemTargetID)
					$this->aspir->setData('item_venture', [
						'item_id'    => $q->ItemTargetID,
						'venture_id' => $data->ID,
					]);
			}

			$this->aspir->setData('venture', [
				'id'              => $data->ID,
				'name'            => $name,
				'amounts'         => empty($quantities) ? null : implode(',', $quantities),
				'job_category_id' => $data->ClassJobCategory->ID,
				'level'           => $data->RetainerLevel,
				'cost'            => $data->VentureCost,
				'minutes'         => $data->MaxTimeMin,
			]);
		});
	}

	public function leves()
	{
		$this->loopEndpoint('leve', [
			'ID',
			'Name',
			'ClassJobCategory.ID',
			'LeveVfx.IconID',
			'LeveVfxFrame.IconID',
			'GilReward',
			'ExpReward',
			'ClassJobLevel',
			'PlaceNameIssued.ID',
			'IconIssuerID',
			'CraftLeve.Repeats',
			'CraftLeve.Item0TargetID',
			'CraftLeve.ItemCount0',
			// Inefficient catchall, but there are a large number of datapoints in there I need to sift through
			'LeveRewardItem',
		], function($data) {
			// No rewards? Don't bother.
			if ($data->LeveRewardItem == null)
				return;

			$this->aspir->setData('leve', [
				'id'              => $data->ID,
				'name'            => $data->Name,
				'type'            => null, // Filled in later
				'level'           => $data->ClassJobLevel,
				'job_category_id' => $data->ClassJobCategory->ID,
				'area_id'         => $data->PlaceNameIssued->ID,
				'repeats'         => $data->CraftLeve->Repeats, // Only CraftLeves can repeat
				'xp'              => $data->ExpReward,
				'gil'             => $data->GilReward,
				'plate'           => $data->LeveVfx->IconID,
				'frame'           => $data->LeveVfxFrame->IconID,
				// This never was the "Area" icon, but the Issuer's image
				//  I don't think I'm using this datapoint, but it's not nullable
				'area_icon'       => $data->IconIssuerID,
			]);

			// Rewards come in 8 total "Groups"
			foreach (range(0, 7) as $slot)
			{
				$probability = $data->LeveRewardItem->{'Probability%' . $slot};

				if ( ! $probability)
					continue;

				$rewardGroup =& $data->LeveRewardItem->{'LeveRewardItemGroup' . $slot};

				// Up to 9 total items can be in a group
				foreach (range(0, 8) as $itemSlot)
					// Count0 should be higher than 0, Item0Target should be set to "Item", and the item shouldn't be a crystal
					//  Crystals are Category 59, there's too many to bother with, and it's not a particularly useful piece of information
					if ($rewardGroup->{'Count' . $itemSlot} && $rewardGroup->{'Item' . $itemSlot . 'Target'} == 'Item' && $rewardGroup->{'Item' . $itemSlot . 'TargetID'} && $rewardGroup->{'Item' . $itemSlot}->ItemUICategory != 59)
						$this->aspir->setData('leve_reward', [
							'item_id' => $rewardGroup->{'Item' . $itemSlot . 'TargetID'},
							'leve_id' => $data->ID,
							'rate'    => $probability,
							'amount'  => $rewardGroup->{'Count' . $itemSlot},
						]);
			}

			// Requirements
			// Up to slot 3 targets exist, however I couldn't find a use-case where a leve required more than one
			if ($data->CraftLeve->Item0TargetID)
				$this->aspir->setData('leve_required', [
					'item_id' => $data->CraftLeve->Item0TargetID,
					'leve_id' => $data->ID,
					'amount'  => $data->CraftLeve->ItemCount0,
				]);
		});
	}

	public function itemCategories()
	{
		$this->loopEndpoint('itemuicategory', [
			'ID',
			'Name',
		], function($data) {
			$this->aspir->setData('item_category', [
				'id'        => $data->ID,
				'name'      => $data->Name,
				// Previously listed "Physical Damage" or "Magic Damage"
				//  I'm not using this datapoint, and it would require a Manual function to figure it
				//  See Garland's "GetCategoryDamageAttribute" function within "Hacks.cs"
				// 'attribute' => null,
			]);
		});
	}

	public function items()
	{
		$rootParamConversion = [
			'Block' => 'Block Strength',
			'BlockRate' => 'Block Rate',
			'DefenseMag' => 'Magic Defense',
			'DefensePhys' => 'Defense',
			'DamageMag' => 'Magic Damage',
			'DamagePhys' => 'Physical Damage',
			'DelayMs' => 'Delay',
		];

		$this->loopEndpoint('item', [
			'ID',
			'Name',
			'Name_de',
			'Name_fr',
			'Name_ja',
			'PriceLow',
			'PriceMid',
			'LevelEquip',
			'LevelItem',
			'ItemUICategory.ID',
			'IsUnique',
			'ClassJobCategoryTargetID',
			'IsUntradable',
			'EquipRestriction',
			'EquipSlotCategory.ID',
			'Rarity',
			'IconID',
			'MateriaSlotCount',
			// Attribute Hunting
			'BaseParam0.Name',
			'BaseParamValue0',
			'BaseParam1.Name',
			'BaseParamValue1',
			'BaseParam2.Name',
			'BaseParamValue2',
			'BaseParam3.Name',
			'BaseParamValue3',
			'BaseParam4.Name',
			'BaseParamValue4',
			'BaseParam5.Name',
			'BaseParamValue5',
			// Special X != Normal X
			'CanBeHq', // AKA Special
			'BaseParamSpecial0.Name',
			'BaseParamValueSpecial0',
			'BaseParamSpecial1.Name',
			'BaseParamValueSpecial1',
			'BaseParamSpecial2.Name',
			'BaseParamValueSpecial2',
			'BaseParamSpecial3.Name',
			'BaseParamValueSpecial3',
			'BaseParamSpecial4.Name',
			'BaseParamValueSpecial4',
			'BaseParamSpecial5.Name',
			'BaseParamValueSpecial5',
			// Base Attributes
			// HQs of these exist as Special, will need to match on names
			'Block', // As "Block Strength"
			'BlockRate', // As "Block Rate"
			'DefenseMag', // As "Magic Defense"
			'DefensePhys', // As "Defense"
			'DamageMag', // As "Magic Damage"
			'DamagePhys', // As "Physical Damage"
			'DelayMs', // As "Delay"
			// Materia Values might always be 0, TODO double check and Manual if needed
			'Materia.BaseParam.Name',
			'Materia.Value',
			// Shop Data
			'GameContentLinks.GilShopItem.Item',
			// Special Shop contains all Beast Traders, ItemCurrency for Item trades
			// 'GameContentLinks.SpecialShop',
		], function($data) use ($rootParamConversion) {
			$this->aspir->setData('item', [
				'id'               => $data->ID,
				'name'             => $data->Name,
				'de_name'          => $data->Name_de,
				'fr_name'          => $data->Name_fr,
				'jp_name'          => $data->Name_ja,
				'price'            => $data->PriceMid,
				'sell_price'       => $data->PriceLow,
				'ilvl'             => $data->LevelItem,
				'elvl'             => $data->LevelEquip,
				'item_category_id' => $data->ItemUICategory->ID,
				'job_category_id'  => $data->ClassJobCategoryTargetID,
				'unique'           => $data->IsUnique,
				'tradeable'        => $data->IsUntradable ? null : 1,
				'equip'            => $data->EquipRestriction,
				'slot'             => $data->EquipSlotCategory->ID,
				'rarity'           => $data->Rarity,
				'icon'             => $data->IconID,
				'sockets'          => $data->MateriaSlotCount,
				// Ignoring these, not used
				// 'desynthable'      => $data->TODO,
				// 'projectable'      => $data->TODO,
				// 'crestworthy'      => $data->TODO,
				// 'delivery'         => $data->TODO,
				// 'repair'           => $data->TODO,
			]);

			// Attribute Data
			$nqParams = [];

			foreach ($rootParamConversion as $key => $name)
				if ($data->$key)
					$nqParams[$rootParamConversion[$key]] = $data->$key;

			// Delay comes through as "2000", but we want it as "2.00"
			if (isset($nqParams['Delay']))
				$nqParams['Delay'] /= 1000;

			if ($data->Materia->BaseParam->Name && $data->Materia->Value)
				$nqParams[$data->Materia->BaseParam->Name] = $data->Materia->Value;

			foreach (range(0, 5) as $slot)
				if ($data->{'BaseParam' . $slot}->Name)
					$nqParams[$data->{'BaseParam' . $slot}->Name] = $data->{'BaseParamValue' . $slot};

			$hqParams = [];

			if ($data->CanBeHq)
				foreach (range(0, 5) as $slot)
					if ($data->{'BaseParamSpecial' . $slot}->Name && isset($nqParams[$data->{'BaseParamSpecial' . $slot}->Name]))
						$hqParams[$data->{'BaseParamSpecial' . $slot}->Name] = $nqParams[$data->{'BaseParamSpecial' . $slot}->Name] + $data->{'BaseParamValueSpecial' . $slot};

			foreach (['nq', 'hq'] as $quality)
				foreach (${$quality . 'Params'} as $attribute => $amount)
					$this->aspir->setData('item_attribute', [
						'item_id'   => $data->ID,
						'attribute' => $attribute,
						'quality'   => $quality,
						'amount'    => $amount,
						'limit'     => null,
					]);

			// Shopping Data
			if ($data->GameContentLinks->GilShopItem->Item)
				foreach ($data->GameContentLinks->GilShopItem->Item as $item)
					$this->aspir->setData('item_shop', [
						'item_id' => $data->ID,
						// Shops come through as "262175.11", we only need what's before the dot
						'shop_id' => explode('.', $item)[0],
					]);




			// item_shop
			// TODO - Shop Items ^ Here
			// "GameContentLinks": {
			//   "GilShopItem": {
			//       "Item": [
			//           "262157.0", // <-- TAKE OFF THE . and anything after
			//     "GameContentLinks": {
			// "SpecialShop": {
			//     "ItemCost***": [
			// 1769514,

			// Ignoring "Attack" attribute, it's all minions
			// Same with "Skill Cost", and "Speed"
			// 357	10071	nq	Attack	50	NULL
			// 356	10071	nq	Skill Cost	20	NULL
			// 359	10071	nq	Speed	2	NULL
			// "HP", "MP",
			//  But it might be medicine, again, how to get Medicine amounts?
			// 355	10071	nq	HP	455	NULL
			// 45836	13638	hq	MP	20	1700
			// 308232	6954	nq	TP	0	NULL
			// "Name": "Medicines & Meals",
			// 463	10146	nq	Increased Spiritbond Gain	0	NULL
			// if $data->ItemUICategory->ID == 44
			// 	It's medicine, How to get medicine amounts?
			// 308229	6952	hq	Bind Resistance	0	NULL
			// 308221	6948	hq	Blind Resistance	0	NULL
			// 385	10086	nq	Careful Desynthesis	0	NULL
			// 386	10086	hq	Careful Desynthesis	0	NULL
			// Garland...
			    // "attr": {
			    //   "action": {
			    //     "Bind Resistance": 15
			    //   }
			    // },
			    // "attr_hq": {
			    //   "action": {
			    //     "Bind Resistance": 20
			    //   }
			    // },
			// NOT SURE HOW TO GET `max`
			// 269	10052	max	Blind Resistance	4	NULL
			// 7867	10664	max	Block Rate	211	NULL
			// 7901	10665	max	Block Strength	222	NULL
			// 21018	10964	max	Blunt Resistance	3	NULL
			// 277	10052	max	Control	39	NULL
			// 252	10052	max	CP	3	NULL
			// 276	10052	max	Craftsmanship	119	NULL
			// 255	10052	max	Critical Hit	24	NULL
			// 17442	10883	max	Defense	58	NULL
			// 262	10052	max	Determination	17	NULL
			// 246	10052	max	Dexterity	16	NULL
			// 254	10052	max	Direct Hit Rate	24	NULL
			// 275	10052	max	Doom Resistance	4	NULL
			// 259	10052	max	Earth Resistance	9	NULL
			// 144646	18813	max	Elemental Bonus	300	NULL
			// 256	10052	max	Fire Resistance	9	NULL
			// 278	10052	max	Gathering	82	NULL
			// 251	10052	max	GP	4	NULL
			// 274	10052	max	Heavy Resistance	4	NULL
			// 257	10052	max	Ice Resistance	9	NULL
			// 248	10052	max	Intelligence	16	NULL
			// 260	10052	max	Lightning Resistance	9	NULL
			// 47284	13809	max	Magic Defense	56	NULL
			// 249	10052	max	Mind	16	NULL
			// 267	10052	max	Paralysis Resistance	4	NULL
			// 279	10052	max	Perception	41	NULL
			// 266	10052	max	Petrification Resistance	4	NULL
			// 21017	10964	max	Piercing Resistance	3	NULL
			// 250	10052	max	Piety	15	NULL
			// 270	10052	max	Poison Resistance	4	NULL
			// 268	10052	max	Silence Resistance	4	NULL
			// 263	10052	max	Skill Speed	24	NULL
			// 21016	10964	max	Slashing Resistance	3	NULL
			// 272	10052	max	Sleep Resistance	4	NULL
			// 265	10052	max	Slow Resistance	4	NULL
			// 264	10052	max	Spell Speed	24	NULL
			// 245	10052	max	Strength	16	NULL
			// 271	10052	max	Stun Resistance	4	NULL
			// 253	10052	max	Tenacity	24	NULL
			// 247	10052	max	Vitality	18	NULL
			// 261	10052	max	Water Resistance	9	NULL
			// 258	10052	max	Wind Resistance	9	NULL

			// if (isset($i->attr))
			// 	foreach ((array) $i->attr as $attr => $amount)
			// 	{
			// 		if ($attr == 'action')
			// 		{
			// 			foreach ($amount as $attr => $data)
			// 			{
			// 				$this->setData('item_attribute', [
			// 					'item_id' => $i->id,
			// 					'attribute' => $attr,
			// 					'quality' => 'nq',
			// 					'amount' => isset($data->rate) ? $data->rate : null,
			// 					'limit' => isset($data->limit) ? $data->limit : null,
			// 				]);
			// 			}

			// 			continue;
			// 		}
			// 	}

			// if (isset($i->attr_hq))
			// 	foreach ((array) $i->attr_hq as $attr => $amount)
			// 	{
			// 		if ($attr == 'action')
			// 		{
			// 			foreach ($amount as $attr => $data)
			// 			{
			// 				$this->setData('item_attribute', [
			// 					'item_id' => $i->id,
			// 					'attribute' => $attr,
			// 					'quality' => 'hq',
			// 					'amount' => isset($data->rate) ? $data->rate : null,
			// 					'limit' => isset($data->limit) ? $data->limit : null,
			// 				]);
			// 			}
			// 			continue;
			// 		}
			// 	}

			// if (isset($i->attr_max))
			// 	foreach ((array) $i->attr_max as $attr => $amount)
			// 	{
			// 		$this->setData('item_attribute', [
			// 			'item_id' => $i->id,
			// 			'attribute' => $attr,
			// 			'quality' => 'max',
			// 			'amount' => $amount,
			// 			'limit' => null,
			// 		]);
			// 	}

		});
	}

	public function recipes()
	{

	}








	/**
	 * loopEndpoint - Loop around an XIVAPI Endpoint
	 * @param  string   $endpoint Any type of `/content`
	 * @param  array    $columns  Specific columns to reduce XIVAPI Load
	 * @param  function $callback $data is passed into here
	 * @param  array    $filters  An array of callback functions; A way to reduce identifiers even more
	 */
	private function loopEndpoint($endpoint, $columns, $callback, $filters = [])
	{
		$request = $this->listRequest($endpoint, ['columns' => ['ID']]);
		foreach ($request->chunk(100) as $chunk)
		{
			$ids = $chunk->map(function($item) {
				return $item->ID;
			});

			if (isset($filters['ids']))
				$ids = $ids->filter($filters['ids']);

			if (empty($ids))
				continue;

			$chunk = $this->request($endpoint, ['ids' => $ids->join(','), 'columns' => $columns]);

			foreach ($chunk->Results as $data)
				$callback($data);
		}
	}

	private function listRequest($content, $queries = [])
	{
		$queries['limit'] = $this->limit !== null ? $this->limit : 3000; // Maximum allowed per https://xivapi.com/docs/Game-Data#lists
		$queries['page'] = 1;

		$results = [];

		while (true)
		{
			// $response now contains ->Pagination and ->Results
			$response = $this->request($content, $queries);

			$results = array_merge($results, $response->Results);

			if ($response->Pagination->PageTotal == $response->Pagination->Page)
				break;

			$queries['page'] = $response->Pagination->PageNext;
		}

		return collect($results);
	}

	private function request($content, $queries = [])
	{
		$command =& $this->aspir->command;
		$api =& $this->api;

		return Cache::rememberForever($content . serialize($queries), function() use ($content, $queries, $api, $command) {
			$command->info(
				'Querying: ' . $content .
				(isset($queries['ids']) ? ' ' . preg_replace('/,.+,/', '-', $queries['ids']) : '')
			);
			return $api->queries($queries)->content->{$content}()->list();
		});
	}

}
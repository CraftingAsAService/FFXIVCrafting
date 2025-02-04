<?php

/**
 * XIVAPI
 * 	Get data from XIVAPI
 */

namespace App\Models\Aspir;

use Illuminate\Support\Facades\Cache;

class XIVAPI
{

	public $aspir;

    public ?string $lang = null;
	public $limit = null;
	public $chunkLimit = null;

	public function __construct(&$aspir)
	{
		$this->aspir =& $aspir;
	}

	public function achievements(): void
	{
		$this->loopEndpoint(
            'Achievement',
            [
                'Name',
                'Item.row_id',
                'Icon',
            ],
            function ($data) {
                $id = $this->xivData($data, 'id');
                $itemId = $this->xivData($data, 'Item.id');

                // We only care about achievements that provide an item
                if ( ! $itemId) {
                    return;
                }

                $this->aspir->setData('achievement', [
                    'id'      => $id,
                    'name'    => $this->xivData($data, 'Name'),
                    'item_id' => $this->xivData($data, 'Item.id'),
                    'icon'    => $this->xivData($data, 'Icon.id'),
                ], $id);
            }
        );
	}

	public function locations(): void
	{
        // "Map" alone doesn't get all the placenames. "Placename" alone doesn't get all the relations.
        $this->loopEndpoint(
            'PlaceName',
            [
                'Name',
            ],
            function ($data) {
                $id = $this->xivData($data, 'id');
                $name = $this->xivData($data, 'Name');

                $this->aspir->setData('location', [
                    'id'           => $id,
                    'name'         => $name,
                    'location_id'  => null,
                ], $id);
            }
        );

		$this->loopEndpoint(
            'Map',
			[
                'PlaceName.Name',
                'PlaceNameRegion.Name',
                'PlaceNameSub.Name',
            ],
            function ($data) {
                $id = $this->xivData($data, 'PlaceName.id');
                $name = $this->xivData($data, 'PlaceName.Name');

                // Skip empty names
                if (!$name) {
                    return;
                }

                $regionId = $this->xivData($data, 'PlaceNameRegion.id');
                $subRegionId = $this->xivData($data, 'PlaceNameSub.id');
                $subRegionName = $this->xivData($data, 'PlaceNameSub.Name');

                // If this is a subregion; that becomes the ID, everything shifts up
                if ($subRegionId) {
                    // But still add the original data
                    $this->aspir->setData('location', [
                        'id'           => $id,
                        'name'         => $name,
                        'location_id'  => $regionId,
                    ], $id);

                    $regionId = $id;
                    $id = $subRegionId;
                    $name = $subRegionName;
                }

                $this->aspir->setData('location', [
                    'id'           => $id,
                    'name'         => $name,
                    'location_id'  => $regionId,
                ], $id);
            }
        );
	}

	public function nodes()
	{
        $tcBase = 'https://raw.githubusercontent.com/ffxiv-teamcraft/ffxiv-teamcraft/master';
        $teamcraftNodes = json_decode(
            file_get_contents($tcBase . '/libs/data/src/lib/json/nodes.json'),
            true
        );

        // $garBase = 'https://raw.githubusercontent.com/ufx/GarlandTools/master';
        // $garContents = file_get_contents($garBase . '/Garland.Web/bell/nodes.js');
        // $garContents = preg_replace(['/^.*gt\.bell\.nodes = /', "/;\\n$/"], '', $garContents);
        // $garNodes = json_decode($garContents, true);

        $timeConverter = [
            0 =>  'Midnight',
            // 1 => '1am', etc
            ...array_map(fn ($i) => $i . "am", range(1, 11)),
            12 => 'Noon',
            // 13 => '1pm', etc
            ...array_map(fn ($i) => ($i - 12) . "pm", range(13, 23)),
        ];

		// You must be looking at gathering items.  What you're looking for there is the GatheringPoint table,
        // which has a PlaceName (i.e., Cedarwood) and a TerritoryType.
        //  The TerritoryType then has the PlaceName you're looking for - Lower La Noscea.
		// Be warned that what I referred to as a 'node' is really a GatheringPointBase.
        //  There are lots of gathering points with the same items because they appear in different places on the map.
		$this->loopEndpoint(
            'GatheringPoint',
            [
                'GatheringPointBase.GatheringType.row_id',
                'GatheringPointBase.GatheringLevel',
                'PlaceName.Name',
                'TerritoryType.PlaceName.row_id',
            ],
            function ($data) use ($teamcraftNodes, $timeConverter/*, $garNodes*/) {
                $id = $this->xivData($data, 'GatheringPointBase.id');
                $placeNameID = $this->xivData($data, 'PlaceName.id');

                if ($placeNameID === 0) {
                    return;
                }

                $nodeData = [
                    'id'          => $id,
                    'name'        => $this->xivData($data, 'PlaceName.Name'),
                    'type'        => $this->xivData($data, 'GatheringPointBase.GatheringType.id'),
                    'level'       => $this->xivData($data, 'GatheringPointBase.GatheringLevel'),
                    'zone_id'     => $this->xivData($data, 'TerritoryType.PlaceName.id'),
                    'area_id'     => $placeNameID,
                    'coordinates' => null,
                    'timer'       => null,
                    'timer_type'  => null,
                ];

                $tcNodeData = $teamcraftNodes[$id] ?? false;

                if ($tcNodeData) {
                    if (isset($tcNodeData['x'])) {
                        $nodeData['coordinates'] = $tcNodeData['x'] . ' x ' . $tcNodeData['y'];
                    }
                    if ( ! empty($tcNodeData['spawns'])) {
                        $spawns = array_map(fn ($time) => $timeConverter[$time], $tcNodeData['spawns']);
                        $nodeData['timer'] = implode(', ', $spawns) . ' for ' . $tcNodeData['duration'] . 'm';

                        if ($tcNodeData['legendary']) {
                            $nodeData['timer_type'] = 'legendary';
                        } elseif ($tcNodeData['ephemeral']) {
                            $nodeData['timer_type'] = 'ephemeral';
                        }
                    }
                }

                $this->aspir->setData('node', $nodeData, $id);
            }
        );

        // GatheringPoint doesn't go "deep" enough to pull back ItemIDs
        $this->loopEndpoint(
            'GatheringPointBase',
            [
                'Item[].Item.row_id',
            ],
            function ($data) use ($teamcraftNodes) {
                $id = $this->xivData($data, 'id');

                $tcNodeData = $teamcraftNodes[$id] ?? false;

                $items = [];
                foreach ($this->xivData($data, 'Item') as $item) {
                    $itemId = $this->xivData($item, 'Item.id');
                    if ( ! $itemId) {
                        continue;
                    }
                    $items[] = $itemId;
                }

                if ( ! empty($tcNodeData['hiddenItems'])) {
                    $items = array_merge($items, $tcNodeData['hiddenItems']);
                }

                foreach ($items as $itemId) {
                    $this->aspir->setData('item_node', [
                        'item_id' => $itemId,
                        'node_id' => $id,
                    ]);
                }
            }
        );
    }

	public function fishingSpots()
	{
		$this->loopEndpoint(
            'FishingSpot',
            [
                'Item[].LevelItem.row_id',
                'PlaceName.Name',
                'TerritoryType.PlaceName.row_id',
                'FishingSpotCategory',
                'GatheringLevel',
                'Radius',
                'X',
                'Z',
            ],
            function ($data) {
                $id = $this->xivData($data, 'id');
                $placeName = $this->xivData($data, 'PlaceName.Name');

                // Skip empty names
                if ($placeName === '') {
                    return;
                }

                $placeNameID = $this->xivData($data, 'PlaceName.id');

                $hasItems = false;
                foreach ($this->xivData($data, 'Item') as $item) {
                    $itemId = $this->xivData($item, 'id');

                    if ($itemId) {
                        $hasItems = true;
                        $this->aspir->setData('fishing_item', [
                            'item_id'    => $itemId,
                            'fishing_id' => $id,
                            'level'      => $this->xivData($item, 'LevelItem.id'),
                        ]);
                    }
                }

                // Don't include the fishing node if there aren't any items attached
                if ( ! $hasItems) {
                    return;
                }

                $this->aspir->setData('fishing', [
                    'id'          => $id,
                    'name'        => $placeName,
                    'category_id' => $this->xivData($data, 'FishingSpotCategory'),
                    'level'       => $this->xivData($data, 'GatheringLevel'),
                    'radius'      => $this->xivData($data, 'Radius'),
                    'x'           => 1 + ($this->xivData($data, 'X') / 50), // Translate a number like 1203 to something like 25.06
                    'y'           => 1 + ($this->xivData($data, 'Z') / 50),
                    'zone_id'     => $this->xivData($data, 'TerritoryType.PlaceName.id'),
                    'area_id'     => $placeNameID,
                ], $id);
            }
        );
	}

	public function mobs()
	{
		$this->loopEndpoint(
            'BNpcName',
            [
                'Singular', // Literally only one datapoint available (their name, with the key `Singular`)
            ],
            function ($data) {
                $id = $this->xivData($data, 'id');
                $name = $this->xivData($data, 'Singular');

                // Skip empty names
                if (!$name) {
                    return;
                }

                $this->aspir->setData('mob', [
                    'id'      => $id,
                    'name'    => $name,
                    'quest'   => null, // Filled in later
                    'level'   => null, // Filled in later
                    'zone_id' => null, // Filled in later
                ], $id);
            }
        );
	}

	public function npcs()
	{
        // ENpcResident has their name, and that's it.


        // GilShop is worthless, other than "it's an entity"
        // https://beta.xivapi.com/api/1/sheet/GilShop/262218
        // GilShopInfo is worthless
        // GilShopItem is worthless? On its own.
        // ... https://beta.xivapi.com/api/1/sheet/GilShopItem/263000

        // I can't find anything linking an NPC to a specific shop
        // So, I'm creating fake NPCs that have "everything" in that category
        $this->gilShop();
        $this->gcscripShop();
        $this->specialShop();
	}

    private function gilShop()
    {
        // This "GilShop" NPC will house all items you can buy with gil
        $npcID = $shopID = 1;

        $this->aspir->setData('npc', [
            'id'      => $npcID,
            'name'    => 'GilShopNPC',
            'zone_id' => null,
            'approx'  => null,
            'x'       => null,
            'y'       => null,
        ], $npcID);

        $this->aspir->setData('shop', [
            'id'   => $shopID,
            'name' => 'GilShop',
        ], $shopID);

        $this->aspir->setData('npc_shop', [
            'shop_id' => $shopID,
            'npc_id' => $npcID,
        ]);

        $this->loopEndpoint(
            'GilShopItem',
            [
                'Item.row_id'
            ],
            function ($data) use ($shopID) {
                $id = $this->xivData($data, 'Item.id');

                $this->aspir->setData('item_shop', [
                    'item_id' => $id,
                    'shop_id' => $shopID,
                    'alt_currency' => false, // Gil isn't alt currency
                ]);
            }
        );
    }

    private function gcscripShop()
    {
        // This "GCScripShop" NPC will house all items you can buy with GrandCompany Scrips
        $npcID = $shopID = 2;

        $this->aspir->setData('npc', [
            'id'      => $npcID,
            'name'    => 'GCScripShopNPC',
            'zone_id' => null,
            'approx'  => null,
            'x'       => null,
            'y'       => null,
        ], $npcID);

        $this->aspir->setData('shop', [
            'id'   => $shopID,
            'name' => 'GCScripShop',
        ], $shopID);

        $this->aspir->setData('npc_shop', [
            'shop_id' => $shopID,
            'npc_id' => $npcID,
        ]);

        $this->loopEndpoint(
            'GCScripShopItem',
            [
                // NOTE: Keep in sync with the GCScripShopItem call in items(); it'll be cached
                'CostGCSeals',
                'Item.row_id'
            ],
            function ($data) use ($shopID) {
                $id = $this->xivData($data, 'Item.id');

                $this->aspir->setData('item_shop', [
                    'item_id' => $id,
                    'shop_id' => $shopID,
                    'alt_currency' => false, // Gil isn't alt currency
                ]);
            }
        );
    }

    private function specialShop()
    {
        // This "SpecialShop" NPC will house all items you can buy... specially
        $npcID = $shopID = 3;

        $this->aspir->setData('npc', [
            'id'      => $npcID,
            'name'    => 'SpecialShopNPC',
            'zone_id' => null,
            'approx'  => null,
            'x'       => null,
            'y'       => null,
        ], $npcID);

        $this->aspir->setData('shop', [
            'id'   => $shopID,
            'name' => 'SpecialShop',
        ], $shopID);

        $this->aspir->setData('npc_shop', [
            'shop_id' => $shopID,
            'npc_id' => $npcID,
        ]);

        $i = 0;
        $this->loopEndpoint(
            'SpecialShop',
            [
                // NOTE: Keep in sync with the SpecialShop call in items(); it'll be cached
                'Item[].Item[].row_id',
            ],
            function ($data) use ($shopID, &$i) {
                $parentItemLoop = $this->xivData($data, 'Item');
                foreach ($parentItemLoop as $pil) {
                    foreach ($pil['Item'] as $item) {
                        $id = $item['row_id'];
                        if (!$id) {
                            continue;
                        }
                        $i++;
                        $this->aspir->setData('item_shop', [
                            'item_id' => $id,
                            'shop_id' => $shopID,
                            'alt_currency' => true,
                        ]);
                    }
                }
            }
        );
    }

	public function quests()
	{
		// 3000 calls were taking over the allotted 10s call limit imposed by XIVAPI's Guzzle Implementation
		// $this->limit = 400;

		$this->loopEndpoint(
            'Quest',
            [
                'Name',
                'ClassJobCategory0.row_id',
                'ClassJobLevel',
                'IssuerStart.row_id', // ENpcResident
                'Icon',
                'ItemCountReward',
                'OptionalItemCountReward',
                'OptionalItemReward[].row_id',
                'PlaceName.row_id',
                'Reward[].row_id',
                'SortKey',
                'TargetEnd.row_id', // ENpcResident
                'ItemCatalyst[].row_id',
                'ItemCountCatalyst',
                'JournalGenre.row_id',
                'QuestParams', // RITEM ScriptInstruction === ScriptArg [ItemID]
            ],
            function ($data) {
                $id = $this->xivData($data, 'id');
                $name = $this->xivData($data, 'Name');

                // Skip empty names
                if (!$name) {
                    return;
                }

                $this->aspir->setData('quest', [
                    'id'              => $id,
                    'name'            => $name,
                    'job_category_id' => $this->xivData($data, 'ClassJobCategory0.id'),
                    'level'           => $this->xivData($data, 'ClassJobLevel')[0],
                    'sort'            => $this->xivData($data, 'SortKey'),
                    'zone_id'         => $this->xivData($data, 'PlaceName.id'),
                    'icon'            => $this->xivData($data, 'Icon')['id'],
                    'issuer_id'       => $this->xivData($data, 'IssuerStart.id'),
                    'target_id'       => $this->xivData($data, 'TargetEnd.id'),
                    'genre'           => $this->xivData($data, 'JournalGenre.id'),
                ], $id);

                // Required Items
                foreach ($this->xivData($data, 'QuestParams') as $param) {
                    if (str_starts_with($param['ScriptInstruction'], 'RITEM') && $param['ScriptArg']) {
                        $this->aspir->setData('quest_required', [
                            'item_id'  => $param['ScriptArg'],
                            'quest_id' => $id,
                        ]);
                    }
                }

                // Reward Items, Guaranteed
                $rewardCounts = $this->xivData($data, 'ItemCountReward');
                foreach ($this->xivData($data, 'Reward') as $key => $reward) {
                    $rewardId = $this->xivData($reward, 'id');
                    if ($this->xivData($reward, 'id') && $rewardCounts[$key]) {
                        $this->aspir->setData('quest_reward', [
                            'item_id'  => $rewardId,
                            'quest_id' => $id,
                            'amount'   => $rewardCounts[$key],
                        ]);
                    }
                }

                // Reward Items, Optional
                $rewardCounts = $this->xivData($data, 'OptionalItemCountReward');
                foreach ($this->xivData($data, 'OptionalItemReward') as $key => $reward) {
                    $rewardId = $this->xivData($reward, 'id');
                    if ($this->xivData($reward, 'id') && $rewardCounts[$key]) {
                        $this->aspir->setData('quest_reward', [
                            'item_id'  => $rewardId,
                            'quest_id' => $id,
                            'amount'   => $rewardCounts[$key],
                        ]);
                    }
                }

                // Reward Items/Catalyst Items
                $catalystCounts = $this->xivData($data, 'ItemCountCatalyst');
                foreach ($this->xivData($data, 'ItemCatalyst') as $key => $catalyst) {
                    $catalystId = $this->xivData($catalyst, 'id');
                    if ($this->xivData($catalyst, 'id') && $catalystCounts[$key]) {
                        $this->aspir->setData('quest_reward', [
                            'item_id'  => $catalystId,
                            'quest_id' => $id,
                            'amount'   => $catalystCounts[$key],
                        ]);
                    }
                }
            }
        );

		// $this->limit = null;
	}

	public function instances()
	{
		$this->loopEndpoint(
            'ContentFinderCondition',
            [
                'Name',
                'Content.row_id', // The "real" ID
                'ContentType.row_id',
                'TerritoryType.Map.PlaceName',
                'Image',
            ],
            function ($data) {
                $id = $this->xivData($data, 'Content.id');
                $name = ucfirst($this->xivData($data, 'Name')); // Name comes across as "the [Instance]", want "The [Instance]"

                // Skip empty names
                if (!$name) {
                    return;
                }

                $this->aspir->setData('instance', [
                    'id'      => $id,
                    'name'    => $name,
                    'type'    => $this->xivData($data, 'ContentType.id'),
                    'zone_id' => $this->xivData($data, 'TerritoryType.Map.PlaceName')['value'],
                    'icon'    => $this->xivData($data, 'Image')['id'],
                ], $id);
            }
        );
	}

	public function jobs()
	{
		$this->loopEndpoint(
            'ClassJob',
            [
                'NameEnglish', // `NameEnglish` is capitalized; `Name` is not
                'Abbreviation',
            ],
                function ($data) {
                $id = $this->xivData($data, 'id');

                $this->aspir->setData('job', [
                    'id'   => $id,
                    'name' => $this->xivData($data, 'NameEnglish'),
                    'abbr' => $this->xivData($data, 'Abbreviation'),
                ], $id);
            }
        );
	}

	public function jobCategories()
	{
		// classjobcategory has a datapoint for every job abbreviation
		//  Dynamically collect them. The key's will stay as the ID, which will be helpful
		$abbreviations = collect($this->aspir->data['job'])->map(function ($job) {
			return $job['abbr'];
		});

		$this->loopEndpoint(
            'ClassJobCategory',
            [
                'Name',
                ...$abbreviations->toArray(),
            ],
            function ($data) use ($abbreviations) {
                $id = $this->xivData($data, 'id');

                $this->aspir->setData('job_category', [
                    'id'   => $id,
                    'name' => $this->xivData($data, 'Name'),
                ], $id);

                foreach ($abbreviations as $jobId => $abbr) {
                    if ($this->xivData($data, $abbr)) {
                        $this->aspir->setData('job_job_category', [
                            'job_id'          => $jobId,
                            'job_category_id' => $id,
                        ]);
                    }
                }
            }
        );
	}

	public function ventures()
	{
		$this->loopEndpoint(
            'RetainerTask',
            [
                'ClassJobCategory.row_id',
                'RetainerLevel',
                'MaxTimemin',
                'VentureCost',
                'IsRandom',
                'Task',
            ],
            function ($data) {
                $id = $this->xivData($data, 'id');

                // The Quantities are only applicable for "Normal" Ventures
                $quantities = null;
                // Name is only applicable on "Random" Ventures
                $name = null;

                if ($this->xivData($data, 'Task')['sheet'] === 'RetainerTaskNormal') {
                    // RetainerTaskNormal
                    if ($this->xivData($data, 'Task.Item.id')) {
                        $quantities = implode(',', $this->xivData($data, 'Task.Quantity'));

                        $this->aspir->setData('item_venture', [
                            'item_id'    => $this->xivData($data, 'Task.Item.id'),
                            'venture_id' => $id,
                        ]);
                    }
                } else {
                    // RetainerRandomTask
                    $name = $this->xivData($data, 'Task.Name');
                }

                $this->aspir->setData('venture', [
                    'id'              => $id,
                    'name'            => $name,
                    'amounts'         => $quantities,
                    'job_category_id' => $this->xivData($data, 'ClassJobCategory.id'),
                    'level'           => $this->xivData($data, 'RetainerLevel'),
                    'cost'            => $this->xivData($data, 'VentureCost'),
                    'minutes'         => $this->xivData($data, 'MaxTimemin'),
                ], $id);
            }
        );
	}

	public function leves()
	{
		// 3000 calls were taking over the allotted 10s call limit imposed by XIVAPI's Guzzle Implementation
		// $this->limit = 1000;
		// $this->chunkLimit = 10;

		$this->loopEndpoint(
            'Leve',
            [
                'Name',
                'ClassJobCategory.row_id',
                'ClassJobLevel',
                'ExpReward',
                'GilReward',
                'IconIssuer',
                'LeveVfx.Icon',
                'LeveVfxFrame.Icon',
                'PlaceNameIssued.row_id',
                'LeveRewardItem',
            ],
            function ($data) {
                $id = $this->xivData($data, 'id');

                // No rewards? Don't bother.
                if (!$this->xivData($data, 'LeveRewardItem.id')) {
                    return;
                }

                $this->aspir->setData('leve', [
                    'id'              => $id,
                    'name'            => $this->xivData($data, 'Name'),
                    'type'            => null, // Filled in later
                    'level'           => $this->xivData($data, 'ClassJobLevel'),
                    'job_category_id' => $this->xivData($data, 'ClassJobCategory.id'),
                    'area_id'         => $this->xivData($data, 'PlaceNameIssued.id'),
                    'repeats'         => false, // Overridden next in the CraftLeve loop
                    'xp'              => $this->xivData($data, 'ExpReward'),
                    'gil'             => $this->xivData($data, 'GilReward'),
                    'plate'           => $this->xivData($data, 'LeveVfx.Icon')['id'],
                    'frame'           => $this->xivData($data, 'LeveVfxFrame.Icon')['id'],
                    // This never was the "Area" icon, but the Issuer's image
                    //  I don't think I'm using this datapoint, but it's not nullable
                    'area_icon'       => $this->xivData($data, 'IconIssuer')['id'],
                ], $id);

                $probability = $this->xivData($data, 'LeveRewardItem.ProbabilityPercent');

                foreach ($this->xivData($data, 'LeveRewardItem.LeveRewardItemGroup') as $slot => $group) {
                    $groupProbability = $probability[$slot];

                    if (!$groupProbability) {
                        continue;
                    }

                    foreach (range(0, 8) as $itemSlot) {
                        $amount = $group['fields']['Count'][$itemSlot] ?? null;
                        $itemId = $group['fields']['Item'][$itemSlot]['value'] ?? null;
                        // $isHQ = $group['fields']['IsHQ'][$itemSlot];

                        if (!$amount || !$itemId) {
                            continue;
                        }

                        $this->aspir->setData('leve_reward', [
                            'item_id' => $itemId,
                            'leve_id' => $id,
                            'rate'    => $groupProbability,
                            'amount'  => $amount,
                        ]);
                    }
                }
            }
        );

        $this->loopEndpoint(
            'CraftLeve',
            [
                'Leve.row_id',
                'Item[].row_id',
                'ItemCount',
                'Repeats',
            ],
            function ($data) {
                $leveId = $this->xivData($data, 'Leve.id');

                if (!$leveId || !isset($this->aspir->data['leve'][$leveId])) {
                    return;
                }

                $this->aspir->data['leve'][$leveId]['repeats'] = !!$this->xivData($data, 'Repeats');

                $counts = $this->xivData($data, 'ItemCount');

                foreach ($this->xivData($data, 'Item') as $key => $item) {
                    $amount = $counts[$key];

                    if (!$amount) {
                        continue;
                    }

                    $this->aspir->setData('leve_required', [
                        'item_id' => $this->xivData($item, 'id'),
                        'leve_id' => $leveId,
                        'amount'  => $amount,
                    ]);
                }
            }
        );

		// $this->chunkLimit = null;
		// $this->limit = null;
	}

	public function itemCategories()
	{
		$this->loopEndpoint(
            'ItemUICategory',
            [
                'Name',
                'OrderMajor',
                'OrderMinor',
            ],
            function ($data) {
                $id = $this->xivData($data, 'id');
                $major = $this->xivData($data, 'OrderMajor') ?? 0;
                $minor = $this->xivData($data, 'OrderMinor') ?? 0;

                $this->aspir->setData('item_category', [
                    'id'        => $id,
                    'name'      => $this->xivData($data, 'Name'),
                    'rank'      => $major . '.' . sprintf('%03d', $minor),
                ], $id);
            }
        );
	}

	public function items()
	{
		// 3000 calls were taking over the allotted 10s call limit imposed by XIVAPI's Guzzle Implementation
		// $this->limit = 1000;

		$rootParamConversion = [
			'Block'       => 'Block Strength',
			'BlockRate'   => 'Block Rate',
			'DefenseMag'  => 'Magic Defense',
			'DefensePhys' => 'Defense',
			'DamageMag'   => 'Magic Damage',
			'DamagePhys'  => 'Physical Damage',
			'DelayMs'     => 'Delay',
		];

        $foodTracker = [];

		$this->loopEndpoint(
            'Item',
            [
                'Name',
                'CanBeHq', // AKA Special
                'ClassJobCategory.row_id',
                'PriceMid',
                'PriceLow',
                'LevelEquip',
                'LevelItem.row_id',
                'ItemUICategory.row_id',
                'IsUnique',
                'IsUntradable',
                'EquipRestriction',
                'EquipSlotCategory.row_id',
                'Rarity',
                'Icon',
                'MateriaSlotCount',
                // Attribute Hunting
                'BaseParam[].Name',
                'BaseParamValue',
                'BaseParamSpecial[].Name',
                'BaseParamValueSpecial',
                // Base Attributes
                // HQs of these exist as Special, will need to match on names
                'Block', // As "Block Strength"
                'BlockRate', // As "Block Rate"
                'DefenseMag', // As "Magic Defense"
                'DefensePhys', // As "Defense"
                'DamageMag', // As "Magic Damage"
                'DamagePhys', // As "Physical Damage"
                'Delayms', // As "Delay"
                // ItemAction contains a myriad of things
                // https://github.com/viion/ffxiv-datamining/blob/master/docs/ItemActions.md
                //  max attribute values
                //  potion values
                //  item food connections
                'ItemAction',
		    ],
            function ($data) use ($rootParamConversion, &$foodTracker) {
                $id = $this->xivData($data, 'id');
                $name = $this->xivData($data, 'Name');

                // Ignore "Dated Bronze Gladius", etc
                if (!$name || str_starts_with($name, 'Dated ')) {
                    return;
                }

                $this->aspir->setData('item', [
                    'id'               => $id,
                    'name'             => $name,
                    'de_name'          => null,
                    'fr_name'          => null,
                    'jp_name'          => null,
                    'price'            => $this->xivData($data, 'PriceMid'),
                    'gc_price'         => null, // Updated below with GCScripShopItem loop
                    'special_buy'      => null, // Updated below with SpecialShop loop
                    'sell_price'       => $this->xivData($data, 'PriceLow'),
                    'ilvl'             => $this->xivData($data, 'LevelItem.id'),
                    'elvl'             => $this->xivData($data, 'LevelEquip'),
                    'item_category_id' => $this->xivData($data, 'ItemUICategory.id'),
                    'job_category_id'  => $this->xivData($data, 'ClassJobCategory.id'),
                    'unique'           => $this->xivData($data, 'IsUnique'),
                    'tradeable'        => $this->xivData($data, 'IsUntradable') ? null : 1,
                    'equip'            => $this->xivData($data, 'EquipRestriction'),
                    'slot'             => $this->xivData($data, 'EquipSlotCategory.id'),
                    'rarity'           => $this->xivData($data, 'Rarity'),
                    'icon'             => $this->xivData($data, 'Icon')['id'],
                    'sockets'          => $this->xivData($data, 'MateriaSlotCount'),
                ], $id);

                // Attribute Data
                $nqParams = $hqParams = [];

                foreach ($rootParamConversion as $key => $name) {
                    $val = $this->xivData($data, $key);
                    if ($val) {
                        $nqParams[$rootParamConversion[$key]] = $val;
                    }
                }

                // Delay comes through as "2000", but we want it as "2.00"
                if (isset($nqParams['Delay'])) {
                    $nqParams['Delay'] /= 1000;
                }

                $canBeHQ = $this->xivData($data, 'CanBeHq');
                $baseParams = $this->xivData($data, 'BaseParam');
                $baseParamValues = $this->xivData($data, 'BaseParamValue');
                $specialParams = $this->xivData($data, 'BaseParamSpecial');
                $specialParamValues = $this->xivData($data, 'BaseParamValueSpecial');

                foreach (range(0, 5) as $slot) {
                    $attr = $baseParams[$slot]['fields']['Name'] ?? null;
                    if ($attr && $baseParamValues[$slot]) {
                        $nqParams[$attr] = $baseParamValues[$slot];
                    }
                }

                // Slot numbers between base and special aren't necessarily the same, hence the split foreach loops
                if ($canBeHQ) {
                    foreach (range(0, 5) as $slot) {
                        $attr = $specialParams[$slot]['fields']['Name'] ?? null;
                        if ($attr && $specialParamValues[$slot] && isset($nqParams[$attr])) {
                            $hqParams[$attr] = $specialParamValues[$slot] + $nqParams[$attr];
                        }
                    }
                }

                // Item Actions provide Attribute Data
                $itemAction = $this->xivData($data, 'ItemAction');

                if ($this->xivData($itemAction, 'id')) {
                    $dataQuality = [
                        'nq' => $this->xivData($itemAction, 'Data'),
                    ];
                    if ($canBeHQ) {
                        $dataQuality['hq'] = $this->xivData($itemAction, 'DataHQ');
                    }

                    switch ($this->xivData($itemAction, 'Type')) {
                        case 844:
                        case 845:
                        case 846:
                            // Crafting + Gathering Food
                            // Battle Food
                            // Attribute Potions, eg: X-Potion of Dexterity
                            // Handled below with a ItemFood query
                            $foodId = $itemAction['fields']['Data'][1]; // ItemFood ID is in slot 1
                            $foodTracker[$foodId] = $id;
                            break;
                        case 847:
                            // Health potions, eg: X-Potion
                            foreach ($dataQuality as $quality => $qualityData) {
                                $this->aspir->setData('item_attribute', [
                                    'item_id'   => $id,
                                    'attribute' => 'HP',
                                    'quality'   => $quality,
                                    'amount'    => $qualityData[0], // data_0 = %
                                    'limit'     => $qualityData[1], // data_1 = max
                                ]);
                            }
                            break;
                        case 848:
                            // Ether MP potions, eg: X-Ether
                            foreach ($dataQuality as $quality => $qualityData) {
                                $this->aspir->setData('item_attribute', [
                                    'item_id'   => $id,
                                    'attribute' => 'MP',
                                    'quality'   => $quality,
                                    'amount'    => $qualityData[0], // data_0 = %
                                    'limit'     => $qualityData[1], // data_1 = max
                                ]);
                            }
                            break;
                        case 849:
                            // Elixir potions
                            foreach ($dataQuality as $quality => $qualityData) {
                                $this->aspir->setData('item_attribute', [
                                    'item_id'   => $id,
                                    'attribute' => 'HP',
                                    'quality'   => $quality,
                                    'amount'    => $qualityData[0], // data_0 = %
                                    'limit'     => $qualityData[1], // data_1 = max
                                ]);
                            }

                            foreach ($dataQuality as $quality => $qualityData) {
                                $this->aspir->setData('item_attribute', [
                                    'item_id'   => $id,
                                    'attribute' => 'MP',
                                    'quality'   => $quality,
                                    'amount'    => $qualityData[2], // data_3 = %
                                    'limit'     => $qualityData[3], // data_4 = max
                                ]);
                            }

                            break;
                    }
                }

                foreach (['nq', 'hq'] as $quality) {
                    foreach (${$quality . 'Params'} as $attribute => $amount) {
                        $this->aspir->setData('item_attribute', [
                            'item_id'   => $id,
                            'attribute' => $attribute,
                            'quality'   => $quality,
                            'amount'    => $amount,
                            'limit'     => null,
                        ]);
                    }
                }
            }
        );

        $languages = [
            'ja' => 'jp_name',
            'de' => 'de_name',
            'fr' => 'fr_name',
        ];
        foreach ($languages as $lang => $column) {
            $this->lang = $lang;
            $this->loopEndpoint(
                'Item',
                [ 'Name' ],
                function ($data) use ($column) {
                    $id = $this->xivData($data, 'id');
                    $name = $this->xivData($data, 'Name');

                    if (isset($this->aspir->data['item'][$id]) && $name) {
                        $this->aspir->data['item'][$id][$column] = $name;
                    }
                }
            );
        }
        $this->lang = null;

        $this->loopEndpoint(
            'Materia',
            [
                'BaseParam.Name',
                'Item[].row_id',
                'Value',
            ],
            function ($data) {
                $values = $this->xivData($data, 'Value');

                if (array_sum($values) === 0) {
                    return;
                }

                $attribute = $this->xivData($data, 'BaseParam.Name');
                $itemIds = array_map(fn ($item) => $item['row_id'], $this->xivData($data, 'Item'));

                foreach ($values as $key => $amount) {
                    $itemId = $itemIds[$key];

                    if (!$amount || !$itemId) {
                        continue;
                    }

                    $this->aspir->setData('item_attribute', [
                        'item_id'   => $itemId,
                        'attribute' => $attribute,
                        'quality'   => 'nq',
                        'amount'    => $amount,
                        'limit'     => null,
                    ]);
                }
            }
        );

        $this->loopEndpoint(
            'ItemFood',
            [
                'BaseParam[].Name',
                'Value',
                'ValueHQ',
                'Max',
                'MaxHQ',
                'IsRelative',
            ],
            function ($data) use ($foodTracker) {
                $id = $this->xivData($data, 'id');
                $itemId = $foodTracker[$id] ?? false;

                $nqValues = $this->xivData($data, 'Value');
                $hqValues = $this->xivData($data, 'ValueHQ');

                if (!$itemId || (array_sum($nqValues) + array_sum($hqValues)) === 0) {
                    return;
                }

                $nqMax = $this->xivData($data, 'Max');
                $hqMax = $this->xivData($data, 'MaxHQ');
                $isRelative = $this->xivData($data, 'IsRelative');
                $names = array_map(fn ($b) => $b['fields']['Name'], $this->xivData($data, 'BaseParam'));

                foreach (range(0, 2) as $slot) {
                    $attribute = $names[$slot];

                    if (!$attribute) {
                        continue;
                    }

                    $rel = $isRelative[$slot];

                    foreach (['nq', 'hq'] as $quality) {
                        $val =& ${$quality . 'Values'}[$slot]; // $nqValues, $hqValues
                        $max =& ${$quality . 'Max'}[$slot]; // $nqMax, $hqMax

                        $this->aspir->setData('item_attribute', [
                            'item_id'   => $itemId,
                            'attribute' => $attribute,
                            'quality'   => $quality,
                            'amount'    => $rel ? $val : null,
                            'limit'     => $rel ? $max : $val,
                        ]);
                    }
                }
            }
        );

        $this->loopEndpoint(
            'GCScripShopItem',
            [
                // NOTE: Keep in sync with the GCScripShopItem call in npcs(); it'll be cached
                'CostGCSeals',
                'Item.row_id',
            ],
            function ($data) {
                $gcPrice = $this->xivData($data, 'CostGCSeals');
                $itemId = $this->xivData($data, 'Item.id');

                if (!$gcPrice || !$itemId || !isset($this->aspir->data['item'][$itemId])) {
                    return;
                }

                $this->aspir->data['item'][$itemId]['gc_price'] = $gcPrice;
            }
        );

        $this->loopEndpoint(
            'SpecialShop',
            [
                // NOTE: Keep in sync with the SpecialShop call in npcs(); it'll be cached
                'Item[].Item[].row_id',
            ],
            function ($data) {
                $parentItemLoop = $this->xivData($data, 'Item');
                foreach ($parentItemLoop as $pil) {
                    foreach ($pil['Item'] as $item) {
                        $itemId = $item['row_id'];

                        if (!$itemId || !isset($this->aspir->data['item'][$itemId])) {
                            continue;
                        }

                        $this->aspir->data['item'][$itemId]['special_buy'] = true;
                    }
                }
            }
        );

		// $this->limit = null;
	}

	public function recipes()
	{
		// 3000 calls were taking over the allotted 10s call limit imposed by XIVAPI's Guzzle Implementation
		// $this->limit = 500;

        $craftType = [
            // CraftTypeID -> ClassJobID
            0 => 8, // Woodworking -> Carpenter
            1 => 9, // Smithing -> Blacksmith
            2 => 10, // Armorcraft -> Armorer
            3 => 11, // Goldsmithing -> Goldsmith
            4 => 12, // Leatherworking -> Leatherworker
            5 => 13, // Clothcraft -> Weaver
            6 => 14, // Alchemy -> Alchemist
            7 => 15, // Cooking -> Culinarian
        ];

		$this->loopEndpoint(
            'Recipe',
            [
                'AmountIngredient',
                'AmountResult',
                'CanHq',
                'CanQuickSynth',
                'Ingredient[].row_id', // Reagents
                'ItemResult.LevelItem',
                'CraftType.row_id',
                'RecipeLevelTable.ClassJobLevel',
                'RecipeLevelTable.Difficulty',
                'RecipeLevelTable.Durability',
                'RecipeLevelTable.Quality',
                'RecipeLevelTable.Stars',
            ],
            function ($data) use ($craftType) {
                $id = $this->xivData($data, 'id');
                $itemId = $this->xivData($data, 'ItemResult.id');

                if ( ! $itemId) {
                    return;
                }

                $this->aspir->setData('recipe', [
                    'id'           => $id,
                    'item_id'      => $itemId,
                    'job_id'       => $craftType[$this->xivData($data, 'CraftType.id')],
                    'level'        => $this->xivData($data, 'ItemResult.LevelItem.id'),
                    'recipe_level' => $this->xivData($data, 'RecipeLevelTable.ClassJobLevel'),
                    'stars'        => $this->xivData($data, 'RecipeLevelTable.Stars'),
                    'difficulty'   => $this->xivData($data, 'RecipeLevelTable.Difficulty'),
                    'durability'   => $this->xivData($data, 'RecipeLevelTable.Durability'),
                    'quality'      => $this->xivData($data, 'RecipeLevelTable.Quality'),
                    'yield'        => $this->xivData($data, 'AmountResult'),
                    'quick_synth'  => $this->xivData($data, 'CanQuickSynth') ? 1 : null,
                    'hq'           => $this->xivData($data, 'CanHq') ? 1 : null,
                    'fc'           => null,
                ], $id);

                $ingredients = $this->xivData($data, 'Ingredient');
                $amounts = $this->xivData($data, 'AmountIngredient');

                foreach ($ingredients as $key => $reagent) {
                    if (($reagent['row_id'] ?? false) && $amounts[$key]) {
                        $this->aspir->setData('recipe_reagents', [
                            'item_id'   => $reagent['row_id'],
                            'recipe_id' => $id,
                            'amount'    => $amounts[$key],
                        ]);
                    }
                }
            }
        );

        $this->loopEndpoint(
            'RecipeNotebookList',
            [
                'Recipe[].row_id',
            ],
            function ($data) {
                $id = $this->xivData($data, 'id') + 1; // Avoid 0 index, accounted for later as well

                foreach ($this->xivData($data, 'Recipe') as $slot => $recipe) {
                    $recipeId = $recipe['row_id'] ?? false;

                    if (!$recipeId) {
                        continue;
                    }

                    $this->aspir->setData('notebook_recipe', [
                        'recipe_id'   => $recipeId,
                        'notebook_id' => $id,
                        'slot'        => $slot,
                    ]);
                }
            }
        );

        // $this->limit = null;
	}

	public function companyCrafts()
	{
		// Recipes and Company Crafts can overlap on IDs. Give them some space.
		$idBase = max(array_keys($this->aspir->data['recipe']));

        $processRecipeRelation = [];

		$this->loopEndpoint(
            'CompanyCraftSequence',
            [
                'CompanyCraftPart[].CompanyCraftProcess[].row_id',
                'ResultItem.row_id',
		    ],
            function ($data) use ($idBase, &$processRecipeRelation) {
                $recipeId = $this->xivData($data, 'id') + 1 + $idBase; // plus company crafts are zero index'd

                $this->aspir->setData('recipe', [
                    'id'           => $recipeId,
                    'item_id'      => $this->xivData($data, 'ResultItem.id'),
                    'job_id'       => 0,
                    'level'        => 1,
                    'recipe_level' => 1,
                    'stars'        => null,
                    'difficulty'   => null,
                    'durability'   => null,
                    'quality'      => null,
                    'yield'        => 1,
                    'quick_synth'  => null,
                    'hq'           => null,
                    'fc'           => 1,
                ], $recipeId);

                $ccp = $this->xivData($data, 'CompanyCraftPart');

                foreach ($ccp as $part) {
                    foreach ($this->xivData($part, 'CompanyCraftProcess') as $process) {
                        if ($process['row_id']) {
                            $processRecipeRelation[$process['row_id']][] = $recipeId;
                        }
                    }
                }
            }
        );

        // Sequence->Part->Process->SupplyItem->Item is too deep from the above query to get ItemID, another loop is required
        $this->loopEndpoint(
            'CompanyCraftProcess',
            [
                'SetQuantity',
                'SetsRequired',
                'SupplyItem[].Item.row_id',
            ],
            function ($data) use ($processRecipeRelation) {
                $id = $this->xivData($data, 'id');

                $setQuantity = $this->xivData($data, 'SetQuantity');
                $setsRequired = $this->xivData($data, 'SetsRequired');
                $supplyItem = $this->xivData($data, 'SupplyItem');

                foreach ($processRecipeRelation[$id] as $recipeId) {
                    foreach ($setQuantity as $key => $quantity) {
                        $amount = $quantity * $setsRequired[$key];
                        $itemId = $this->xivData($supplyItem[$key], 'Item.id');

                        if (!$amount || !$itemId) {
                            continue;
                        }

                        $this->aspir->setData('recipe_reagents', [
                            'item_id'   => $itemId,
                            'recipe_id' => $recipeId,
                            'amount'    => $amount,
                        ]);
                    }
                }
            }
        );
	}

	public function notebookDivisions()
	{

		$this->loopEndpoint('NotebookDivision', [
			'Name',
			'NotebookDivisionCategory.Name',
		], function ($data) {
            $id = $this->xivData($data, 'id') + 1; // 0 index'd, artificially +1'd

            $categoryId = $this->xivData($data, 'NotebookDivisionCategory.id') + 1; // Also 0 index'd

			$this->aspir->setData('notebookdivision', [
				'id'          => $id,
				'name'        => $this->xivData($data, 'Name'),
				'category_id' => $categoryId,
			], $id);

            // Category `0` has no name, name it Leveling
            $categoryName = $categoryId === 1
                ? 'Leveling'
                : $this->xivData($data, 'NotebookDivisionCategory.Name');

            $this->aspir->setData('notebookdivision_category', [
                'id'   => $categoryId,
                'name' => $categoryName,
            ], $categoryId);
		});
	}

	/**
	 * loopEndpoint - Loop around an XIVAPI Endpoint
	 */
	private function loopEndpoint(string $endpoint, string|array $fields, callable $callback, int $limit = 100)
	{
        if (is_array($fields)) {
            $fields = implode(',', $fields);
        }

        $url = "https://beta.xivapi.com/api/1/sheet/{$endpoint}?fields={$fields}";

        if ($this->lang) {
            $url .= '&language=' . $this->lang;
        }

        echo 'Starting: ' . $endpoint . ($this->lang ? ' (' . $this->lang . ')' : '') . PHP_EOL;

        $after = 0;
        while (true) {
            $output = $this->curl($url . '&limit=' . $limit . '&after=' . $after);

            $result = json_decode($output, true);

            if (!isset($result['rows'])) {
                dd($result, 'ERROR? No rows?');
            }

            if ($result['rows']) {
                foreach ($result['rows'] as $data) {
                    ($callback)($data);
                }
                $after = end($result['rows'])['row_id'];
            }

            if (count($result['rows']) < $limit) {
                break;
            }
        }
	}

    private function xivData(array $data, string $key): mixed
    {
        // Convert 'GatheringPointBase.GatheringType.id' to
        // $data['fields']['GatheringPointBase']['fields']['GatheringType']['row_id']
        // Convert 'GatheringPointBase.GatheringType.Value' to
        // $data['fields']['GatheringPointBase']['fields']['GatheringType']['fields']['Value']
        $ref =& $data;
        foreach (explode('.', $key) as $p) {
            // Special case: The ID isn't in fields, and I want to use the shorthand `id` instead of `row_id`
            if ($p === 'id') {
                $ref =& $ref['row_id'];
            } else {
                $ref =& $ref['fields'][$p];
            }
        }

        return $ref;
    }

    private function curl($url)
    {
        return Cache::store('file')->rememberForever('aspir:' . $url, function () use ($url) {
            echo 'Querying: ' . preg_replace('/^.*&after=(\d+).*$/', '$1', $url) . PHP_EOL;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'FFXIVCrafting');
            $output = curl_exec($ch);
            curl_close($ch);

            return $output;
        });
    }

	// private function listRequest($content, $queries = [])
	// {
	// 	$queries['limit'] = $this->limit !== null ? $this->limit : 3000; // Maximum allowed per https://xivapi.com/docs/Game-Data#lists
	// 	$queries['page'] = 1;
    //
	// 	$results = [];
    //
	// 	while (true)
	// 	{
	// 		// $response now contains ->Pagination and ->Results
	// 		$response = $this->request($content, $queries);
    //
	// 		$results = array_merge($results, $response->Results);
    //
	// 		if ($response->Pagination->PageTotal == $response->Pagination->Page)
	// 			break;
    //
	// 		$queries['page'] = $response->Pagination->PageNext;
	// 	}
    //
	// 	return collect($results);
	// }
    //
	// private function request($content, $queries = [])
	// {
	// 	$command =& $this->aspir->command;
	// 	$api =& $this->api;
    //
	// 	return Cache::store('file')->rememberForever($content . serialize($queries), function () use ($content, $queries, $api, $command) {
	// 		$command->info(
	// 			'Querying: ' . $content .
	// 			(isset($queries['ids']) ? ' ' . preg_replace('/,.+,/', '-', $queries['ids']) : '')
	// 		);
	// 		return $api->queries($queries)->content->{$content}()->list();
	// 	});
	// }

}

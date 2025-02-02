<?php

/**
 * ManualData
 * 	Manually parsed data; by hand
 */

namespace App\Models\Aspir;

class ManualData
{

	public $aspir;

	private $path;

	public function __construct(&$aspir)
	{
		$this->path = storage_path('app/osmose/');

		$this->aspir =& $aspir;
	}

    // public function nodes()
    // {
    //     // https://github.com/ffxiv-teamcraft/ffxiv-teamcraft/blob/master/libs/data/src/lib/json/gathering-point-to-node-id.json
    //     // "30051": 10,
    //     // "30052": 14,
    //     // "30053": 14,
    //
    //
    //     $teamcraftNodes = file_get_contents('https://raw.githubusercontent.com/ffxiv-teamcraft/ffxiv-teamcraft/master/libs/data/src/lib/json/nodes.json');
    //     $nodes = json_decode($teamcraftNodes, true);
    //     dd($nodes);
    //     // Looking for nodes with hidden items
    //     $nodesWithHiddenItems = $nodes->filter(fn ($node) => ! empty($node['hiddenItems']) && $node['zoneid']);
    //     dd($nodesWithHiddenItems->keys());
    //
    //
    //
    // }

	public function nodeCoordinates()
	{
		// Node Coordinates file is manually built
		$coordinates = $this->readTSV($this->path . 'nodeCoordinates.tsv');

		// Gather all locations, ID => LocationID for the parental relationship
		$areaFinder = collect($this->aspir->data['location'])->pluck('name', 'id');

		// GatheringPointName Conversions, for coordinate matching
		$typeConverter = [
			// Type => Matching Text
			0 => 'Mineral Deposit', // via Mining
			1 => 'Rocky Outcrop', // via Quarrying
			2 => 'Mature Tree', // via Logging
			3 => 'Lush Vegetation Patch', // via Harvesting
		];

		foreach ($this->aspir->data['node'] as &$node)
			$node['coordinates'] = isset($areaFinder[$node['zone_id']]) && isset($typeConverter[$node['type']])
				? $coordinates
					->where('location', $areaFinder[$node['zone_id']])
					->where('level', $node['level'])
					->where('type', $typeConverter[$node['type']])
					->pluck('coordinates')->join(', ', ' or ')
				: null;
	}

	public function nodeTimers()
	{
		$timeConverter = [
			 0 =>  'Midnight',
			 1 =>  '1am',
			 2 =>  '2am',
			 3 =>  '3am',
			 4 =>  '4am',
			 5 =>  '5am',
			 6 =>  '6am',
			 7 =>  '7am',
			 8 =>  '8am',
			 9 =>  '9am',
			10 => '10am',
			11 => '11am',
			12 => 'Noon',
			13 =>  '1pm',
			14 =>  '2pm',
			15 =>  '3pm',
			16 =>  '4pm',
			17 =>  '5pm',
			18 =>  '6pm',
			19 =>  '7pm',
			20 =>  '8pm',
			21 =>  '9pm',
			22 => '10pm',
			23 => '11pm',
		];

		$nodeTimers = $this->readTSV($this->path . 'nodeTimers.tsv')
			->mapWithKeys(function($record) use ($timeConverter) {
				$hours = collect(explode(',', $record['times']))->map(function($entry) use ($timeConverter) {
					return $timeConverter[trim($entry)] ?? $entry;
				})->implode(', ');
				return [ $record['node_id'] => $record['type'] . ' - ' . $hours . ' for ' . $record['uptime'] . 'm' ];
			});

		foreach ($nodeTimers as $nodeId => $timer)
			if (isset($this->aspir->data['node'][$nodeId]))
				$this->aspir->data['node'][$nodeId]['timer'] = $timer;
	}

	public function randomVentureItems()
	{
		// Random Venture Items file is manually built
		$randomVentureItems = $this->readTSV($this->path . 'randomVentureItems.tsv')
			->pluck('items', 'venture');

		$ventures = collect($this->aspir->data['venture'])->pluck('id', 'name')->toArray();
		$items = collect($this->aspir->data['item'])->pluck('id', 'name')->toArray();

		// The `venture` column should match against a `venture.name`
		//  Likewise, exploding the `items` column on a comma, then looping those against the `item.name` should produce a match
		//  And voila, populate `item_venture`
		foreach ($randomVentureItems as $venture => $ventureItems)
		{
			$ventureItems = explode(',', str_replace(', ', ',', $ventureItems));

			foreach ($ventureItems as $itemName)
				if (isset($items[$itemName]) && isset($ventures[$venture]))
					$this->aspir->setData('item_venture', [
						'venture_id' => $ventures[$venture],
						'item_id'    => $items[$itemName],
					]);
		}
	}

	public function leveTypes()
	{
		$leveTypes = $this->readTSV($this->path . 'leveTypes.tsv')
			->pluck('type', 'plate');

		foreach ($this->aspir->data['leve'] as &$leve)
			if (isset($leveTypes[$leve['plate']]))
				$leve['type'] = $leveTypes[$leve['plate']];
	}

	public function iconTransition()
	{
		// Runonce
		// Converts Garland IDs to raw icons
		$iconTransition = $this->readTSV($this->path . 'iconTransition.tsv')
			->pluck('new', 'original');

		$basePath = '/mnt/Projects/ffxiv/assets/ffxiv/';

		foreach ($iconTransition as $original => $new)
		{
			$original = $basePath . 'item/' . $original . '.png';

			// All icons are five digits, otherwise we'd have different rules.
			//  See https://xivapi.com/docs/Icons
			$new = '0' . $new;
			$folder = substr($new, 0, 3) . "000";
			$newBase = $basePath . 'i/' . $folder . '/';
			$new = $newBase . $new . '.png';

			if ( ! is_dir($newBase))
				exec('mkdir "' . $newBase . '"');

			exec('ln "' . $original . '" "' . $new . '" 2>/dev/null &');
		}
	}

	public function getIcons()
	{
		$domain = 'https://xivapi.com/i/';
        $baseDomain = 'https://beta.xivapi.com/api/1/asset?path=ui/icon/%s/%s.tex&format=png';

        $basePath = '/mnt/Projects/ffxiv/assets/ffxiv/i/';

		// A stream context to ignore http warnings
		$streamContext = stream_context_create([
			'http' => ['ignore_errors' => true],
            'ssl' => [
                "allow_self_signed" => true,
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
		]);

		$iconSets = [
			'item'        => \App\Models\Garland\Item::select('icon')->pluck('icon'),
			'instance'    => \App\Models\Garland\Instance::select('icon')->pluck('icon'),
			'quest'       => \App\Models\Garland\Quest::select('icon')->pluck('icon'),
			'achievement' => \App\Models\Garland\Achievement::select('icon')->pluck('icon'),
			'levePlates'  => \App\Models\Garland\Leve::select('plate')->pluck('plate'),
			'leveFrames'  => \App\Models\Garland\Leve::select('frame')->pluck('frame'),
		];

		exec('find "' . $basePath . '" -name *.png', $existingImages);
		$existingImages = array_map(function ($value) use ($basePath) {
			return str_replace($basePath, '', $value);
		}, $existingImages);

		foreach ($iconSets as $set) {
            foreach ($set as $icon) {
                // All icons are five digits, otherwise we'd have different rules.
                //  See https://xivapi.com/docs/Icons
                $icon = (strlen($icon) === 6 ? '' : '0') . $icon;

                if (strlen($icon) !== 6) {
                    continue;
                }

                $folder = substr($icon, 0, 3) . "000";

                $apiUrl = sprintf($baseDomain, $folder, $icon);

                $iconBase = $basePath . $folder . '/';
                $icon = $icon . '.png';

                if (in_array($folder . '/' . $icon, $existingImages)) {
                    continue;
                }

                $this->aspir->command->info('Downloading ' . $icon);

                $retryLimit = 3;
                $tries = 0;
                retry:
                try {
                    $tries++;
                    $image = file_get_contents($apiUrl, false, $streamContext);
                } catch (\Exception $e) {
                    $this->aspir->command->info('Retrying ' . $icon . ' (' . $tries . ')');
                    if ($tries <= $retryLimit) {
                        goto retry;
                    }
                }

                if (str_contains($image, '"code":404')) {
                    $this->aspir->command->error('Download failed, 404');
                    continue;
                }

                if (!is_dir($iconBase)) {
                    exec('mkdir "' . $iconBase . '"');
                }

                file_put_contents($iconBase . $icon, $image);

                $existingImages[] = $folder . '/' . $icon;
            }
        }
	}

	private function readTSV($filename)
	{
		$tsv = array_map(function($l) { return str_getcsv($l, '	'); }, file($filename));

		array_walk($tsv, function(&$a) use ($tsv) {
			$a = array_combine($tsv[0], $a);
		});
		array_shift($tsv);

		return collect($tsv);
	}

}

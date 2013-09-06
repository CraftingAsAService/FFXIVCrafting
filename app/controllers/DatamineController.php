<?php

class DatamineController extends BaseController 
{

	public function getIndex()
	{
		if (App::environment() != 'local')
			exit('Only runs locally.');

		$this->_xivdb_parse(
			'http://xivdb.com/modules/search/search.php?query=!filters&pagearray=%7B%7D&language=1&filters=%22ITEMS%3AICI_DATED_0%3AIGA_71_G_0_OR%3AIGA_11_G_0_OR%3AIGA_70_G_0_OR%3AIGA_72_G_0_OR%3AIGA_73_G_0_OR%3AIGA_10_G_0_OR%3AIOI_IOIILVL_IOIASC%3A%22&page=', // URL, stripped of the page number
			16 // Pages
		);
	}

	private function _xivdb_parse($url = '', $pages = '')
	{
		//foreach (range(1, $pages) as $page)
		foreach (range(3, $pages) as $page)
			$items = $this->_curl($url . $page);

		var_dump($items);
	}

	private function _curl($url)
	{
		static $items = array(
				'equipment' => array(),
				'food' => array(),
				'materia' => array()
			);

		$j = 0;

		// Load the URL/DOM
		$dom = new DOMDocument();
		@$dom->loadHTMLFile($url);
		
		// Find the items
		$finder = new DomXPath($dom);
		$class_name = 'search_result_box';
		$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $class_name ')]");

		unset($dom, $finder);

		// Go through the nodes, add a parsed version into $items
		foreach ($nodes as $element)
		{
			// Link to item
			$href = $element->getElementsByTagName('a')->item(0)->getAttribute('href');

			// The good data is in the tooltip!
			$tooltip = html_entity_decode($element->getAttribute('data-tooltip'));

			// Parse tooltip
			$dom = new DOMDocument();
			@$dom->loadHTML($tooltip);
			$finder = new DomXPath($dom);

			// Basic attributes
			$name = trim($finder->query("//h2")->item(0)->nodeValue);
			$slot = trim($finder->query("//span[@class='hud-tooltip-header-roles']")->item(0)->nodeValue);
			$icon = trim($finder->query("//img[@class='hud-page-icon']")->item(0)->getAttribute('src'));

			// Advanded attributes - These may or may not exist
			$class = $finder->query("//div[@class='hud-widget-text-5 hud-widget-text-shadow'][1]");
			$class = $class->item(0) ? trim($class->item(0)->nodeValue) : '';

			$level = $finder->query("//div[@class='hud-widget-text-5 hud-widget-text-shadow'][2]");
			$level = $level->item(0) ? trim($level->item(0)->nodeValue) : '';

			$materia = $finder->query("//div[@class='hud-widget-text-6 hud-widget-text-shadow']");
			$materia = $materia->item(0) ? preg_replace('/Slots (\d+)/', '$1', $materia->item(0)->nodeValue) : 0;
			
			// Stat Attributes
			$attributes = array();
			$attributeNodes = $finder->query("//div[@class='hud-widget-text-1 hud-widget-text-shadow hud-tooltip-info-block2']/span");
			$a_name = '';
			$i = 0;
			foreach ($attributeNodes as $node)
			{
				if ($i++ % 2 == 0)
					$a_name = trim($node->nodeValue);
				else
					$attributes[$a_name] = trim($node->nodeValue);
			}

			// Slot Cleanup
			if (preg_match('/Main Hand/', $slot))
				$slot = 'Primary';
			elseif (preg_match('/Off Hand/', $slot))
				$slot = 'Secondary';
			elseif (preg_match('/Stack Size/', $slot))
			{
				$slot = preg_replace('/^.*\s(\w+)$/', '$1', $slot);
				if ($slot == 'Meal')
					$slot = 'Food';
			}
			else
				// Neck appeared as "Neck [weird characters] Necklace"
				// Same with Ear / Earings
				$slot = preg_replace('/^(\w+)\s.*/', '$1', $slot);

			// Class Cleanup
			if (preg_match('/Requires/', $class))
			{
				if ($class == 'Requires All Classes')
				{
					// Look through the attributes to figure out if it's supposed to be for DOH or DOL
					if (count(array_intersect(array('Craftsmanship', 'Control', 'CP'), array_keys($attributes))))
						$class = 'DOH';
					else
						$class = 'DOL';
				}
				else
					$class = preg_replace('/Requires\s/', '', $class);
			}

			// Level Cleanup
			if ($level)
			{
				preg_match('/^Requires Lv\. (\d+)/', $level, $matches);
				$level = $matches[1];
			}


			#### TMP
			// don't want a lot, but want some
			if ($j++ % 10 != 0)
				continue;



			// Crafted By?
			// Purchasable from?
			// 	Save 1st, say "and more"

			// Load the ajax call for the item page
			// POST: http://xivdb.com/modules/item/item.php
				// Form Data:
				// lang: 1
				// args: strip out "?item/" from the href

			$context = stream_context_create(array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-type: application/x-www-form-urlencoded',
					'content' => http_build_query(
						array(
							'lang' => '1',
							'args' => preg_replace('/\?item\//', '', $href)
						)
					)
				)
			));

			$fulltip = file_get_contents('http://xivdb.com/modules/item/item.php', false, $context);

			// Parse tooltip
			$dom = new DOMDocument();
			@$dom->loadHTML($fulltip);
			$finder = new DomXPath($dom);


			$crafted_by = $finder->query("//div[@class='hud-page-content'][5]/div[1]/div[@class='hud-page-content-top hud-widget-text-shadow']/div[2]/div[@class='hud-page-statsbox hud-widget-text-shadow']/div[2]/div/span[1]");
			$crafted_by = $crafted_by->item(0) ? $crafted_by->item(0)->nodeValue : '';


			//div[@class='hud-page-statsbox hud-widget-text-shadow']/div/div/span[1]

			echo $fulltip;
			//exit;










			// All of the additions have these basic things
			$add_me = array(
				'name' => $name,
				'attributes' => $attributes,
				'href' => $href,
				'icon' => $icon,
				'tooltip' => base64_encode($tooltip),
				'fulltip' => base64_encode($fulltip),
				#'purchasable' => $purchasable
			);

			if ($slot == 'Materia' || $slot == 'Food')
				$items[strtolower($slot)][] = $add_me;
			else
			{
				$items['equipment'][] = array_merge($add_me, array(
					'slot' => $slot,
					'class' => $class,
					'level' => $level,
					'crafted_by' => $crafted_by
				));
			}
		}

		echo '<pre>';
		print_r($items);

		exit;

		return $items;
	}

}

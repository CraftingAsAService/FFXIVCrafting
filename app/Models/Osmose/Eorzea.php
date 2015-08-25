<?php

// Scraper for http://na.finalfantasyxiv.com/lodestone/playguide/db/item/

namespace App\Models\Osmose;

use Cache;

class Eorzea
{
    
	static public function scrape_names()
	{
		$cache_path = storage_path() . '/app/osmose/cache/eorzea/item_lists/';

		set_time_limit(0);

		Eorzea::scrape_item_list($cache_path);
	}

	static public function scrape_item_list($cache_path = '')
	{
		$url = 'http://%s.finalfantasyxiv.com/lodestone/playguide/db/item/?order=1&page=%d';
		$max_page = Eorzea::get_last_page_number(sprintf($url, 'na', 1));

		// Will create a lot of simultaneous curl requests.  With 224 pages, divided by 10, that's 23 simultaneous requests
		// Each request will look at 4 languages * 10 pages.
		chdir($cache_path);
		foreach (array_chunk(range(1, $max_page), 10) as $pages)
		{
			$start = $pages[0];
			$end = end($pages);
			exec('curl "http://{na,jp,fr,de}.finalfantasyxiv.com/lodestone/playguide/db/item/?order=1&page=[' . $start . '-' . $end . ']" --output "#1_#2.html" > /dev/null &');
		}
	}

	static public function parse_names()
	{
		$cache_path = storage_path() . '/app/osmose/cache/eorzea/item_lists/';

		set_time_limit(0);

		Eorzea::parse_item_list($cache_path);
	}

	static public function parse_item_list($cache_path)
	{
		$names = [];
		chdir($cache_path);
		$files = glob('*.html');

		foreach ($files as $file)
		{
			$html = file_get_contents($file);
			$lang = explode('_', $file)[0];

			$dom = new \DOMDocument();
			@$dom->loadHTML($html);
			$finder = new \DomXPath($dom);

			$found_names = $finder->query("//div[@class='ic_link_txt']/a");
			$found_hrefs = $finder->query("//div[@class='ic_link_txt']/a/@href");

			foreach ($found_names as $key => $name)
			{
				$name = $name->nodeValue;
				$id = preg_replace('/^.*\/([^\/]+)\/$/', '$1', $found_hrefs->item($key)->nodeValue);
				$names[$id][$lang] = $name;
			}
		}

		// Transpose file so English name is the key, and current Key is an "eid" (eorzea id)
		$results = [];
		foreach ($names as $eid => $name_list)
		{
			$na = $name_list['na'];
			unset($name_list['na']);

			$name_list['eid'] = $eid;

			$results[$na] = $name_list;
		}
		
		file_put_contents(storage_path() . '/app/osmose/i18n_names.json', json_encode($results));
	}

	static public function get_last_page_number($url)
	{
		$html = Cache::store('file')->remember(md5($url), 600, function() use ($url) {
			return Eorzea::curl($url);
		});

		$dom = new \DOMDocument();
		@$dom->loadHTML($html);
		$finder = new \DomXPath($dom);

		$last_page_href = $finder->query("//li[@class='next_all']/a/@href");
		
		foreach ($last_page_href as $href_el)
		{
			$last_page = $href_el->nodeValue;
			break;
		}

		return preg_replace('/^.*page=(\d+).*$/', '$1', $last_page);
	}

	static private function curl($url)
	{
		// Reset timeout on each iteration
		set_time_limit(0);

		// Get the website information
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 0);

		// Thank you, we'll take that!
		$results = curl_exec($ch);

		// Cleanup
		curl_close($ch);
		unset($ch);

		return $results;
	}

}
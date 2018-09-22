<?php namespace App\Http\Controllers\Osmose;

// use App\Models\Osmose\AppData;
// use App\Models\Osmose\FileHandler;
// use App\Models\Osmose\Career;
// use App\Models\Osmose\Nodes;

// use Session;

class LevesController extends \App\Http\Controllers\Controller
{
	protected $finder;

	public function __construct()
	{
		view()->share('active', 'osmose');
	}

	public function getCrawl()
	{
		// Get all leve names
		$base = 'http://ffxiv.gamerescape.com';
		$starting_pages = array(
			'/wiki/Category:Tradecraft_Levequest',
			'/wiki/Category:Fieldcraft_Levequest'
		);

		$html_base = storage_path() . '/app/osmose/cache/leves/';

		$categories = [];
		foreach($starting_pages as $url)
		{
			// Basic caching
			$html_file = $html_base . md5($base . $url) . '.html';
			if ( ! is_file($html_file))
			{
				$html = file_get_contents($base . $url);
				file_put_contents($html_file, $html);
			}
			else
				$html = file_get_contents($html_file);

			// Load the URL/DOM
			$dom = new \DOMDocument();
			@$dom->loadHTML($html);
			$finder = new \DomXPath($dom);

			$categories_found = $finder->query("//table[@class='itembox']/tr/td/a/@href");

			foreach ($categories_found as $link)
				$categories[] = $link->nodeValue;
		}

		$pages = [];
		foreach ($categories as $url)
		{
			// Basic caching
			$html_file = $html_base . md5($base . $url) . '.html';
			if ( ! is_file($html_file))
			{
				$html = file_get_contents($base . $url);
				file_put_contents($html_file, $html);
			}
			else
				$html = file_get_contents($html_file);

			// Load the URL/DOM
			$dom = new \DOMDocument();
			@$dom->loadHTML($html);
			$finder = new \DomXPath($dom);

			$pages_found = $finder->query("//table[@class='GEtable sortable']/tr/td[1]/a/@href");

			foreach ($pages_found as $link)
				$pages[] = $link->nodeValue;
		}

		$leves = [];
		foreach ($pages as $url)
		{
			// Basic caching
			$html_file = $html_base . md5($base . $url) . '.html';
			if ( ! is_file($html_file))
			{
				$html = file_get_contents($base . $url);
				file_put_contents($html_file, $html);
			}
			else
				$html = file_get_contents($html_file);

			// Load the URL/DOM
			$dom = new \DOMDocument();
			@$dom->loadHTML($html);
			$finder = new \DomXPath($dom);
			$this->finder = $finder;

			$level = $this->basic_finder("//table[@class='itembox shadowed']//tr/td[1]/table//tr/td[2]/span/font/b");
			$level = preg_replace('/\D/', '', $level);

			$name = $this->basic_finder("//tr/td[1]/table//tr/td[2]/text()");
			$name = substr($name, 4);

			// echo 'Parsing ' . $name . '<br>';

			$amounts = $this->basic_finder("//div/table/tr/td[1]/span");

			if ( ! is_array($amounts))
			{
				flash()->error($url . ' Failed');
				continue;
			}

			$xp = array_shift($amounts);

			$gil = $this->basic_finder("//tr[4]/td[1]/div[@class='arrquestbox']/div[@class='tls']/div[@class='trt']/div[@class='tl']/div[@class='trs']/div[@class='tr']/div[@class='content']/table/tr[2]/td/div/table/tr/td[2]/span");
			if (preg_match('/-/', $gil))
				list($gil_min, $gil_max) = explode('-', $gil);
			elseif (preg_match('/^(\d+)$/', $gil, $matches))
				$gil_min = $gil_max = $matches[1];
			else
				$gil_min = $gil_max = null;

			// Remove the first two because they're XP and Gil
			$items = $this->basic_finder("//tr[4]/td[1]/div[@class='arrquestbox']/div[@class='tls']/div[@class='trt']/div[@class='tl']/div[@class='trs']/div[@class='tr']/div[@class='content']/table/tr/td/div/table/tr/td/a/@title");
			array_shift($items);
			array_shift($items);

			$rewards = [];
			foreach ($items as $key => $value)
			{
				if ( ! isset($amounts[$key]))
					$amounts[$key] = 1;

				$rewards[] = array(
					'item_name' => $value,
					'amount' => preg_replace('/\D/', '', $amounts[$key])
				);
			}

			$npc_involved = $this->basic_finder("//tr[5]/td/a/font/span");

			$issuing_npc = $this->basic_finder("//tr[1]/td/a[1]/font/span");

			$issuing_npc_information = $this->basic_finder("//table[@class='itembox shadowed']/tr/td[3]/table[@class='rightbox']/tr[4]/td[2]/a");

			$leve_type = $this->basic_finder("//div[@id='mw_main'][1]/div[@id='mw_contentwrapper']/div[@id='mw_content']/div[@id='mw_contentholder']/div[@id='mw-content-text']/table[@class='itembox shadowed']/tr[4]/td[3]/table[@class='rightbox']/tr[2]/td[2]/a");

			$leves[] = array(
				'level' => $level,
				'name' => $name,
				'xp' => $xp,
				'gil_min' => $gil_min,
				'gil_max' => $gil_max,
				'rewards' => $rewards,
				'npc_involved' => $npc_involved,
				'issuing_npc' => $issuing_npc,
				'issuing_npc_information' => $issuing_npc_information,
				'leve_type' => $leve_type
			);
		}

		file_put_contents($html_base . 'leves.json', json_encode($leves));

		flash()->message('Leves Crawler finished');
		return redirect('/osmose');
	}

	public function getCompile()
	{
		$gamerescapewiki_data = json_decode(file_get_contents(storage_path() . '/app/osmose/cache/leves/leves.json'));

		// dd($gamerescapewiki_data);

		$all_leves = \App\Models\Garland\Leve::pluck('id', 'name')->all();
		$all_leves = array_change_key_case($all_leves);
		$leves = [];
		foreach ($all_leves as $key => $value)
			if ($value == 483) // Brute force a weird spacing issue fix
				$leves['actually,it\'sloyalty'] = $value;
			else
				$leves[trim(preg_replace('/\s|\-|\(.*\)/', '', $key))] = $value;

		// $gamerescapewiki_leves = [];

		// foreach ($gamerescapewiki_data as $gewd)
		// {
		// 	$search_name = trim(preg_replace('/\s|\-|\(.*\)/', '', strtolower($gewd->name)));

		// 	if ( ! isset($leves[$search_name]))
		// 		continue;

		// 	dd($gewd);
		// 		// dd('NOT FOUND', $search_name, $leves);


		// 	// $leves[$search_name];

		// 	// dd($gewd, in_array($gewd->name, $leves));
		// 	// $extra->xp = preg_replace('/,/', '', $extra->xp);

		// 	// $leve = null;
		// 	// foreach ($original_leves as $original)
		// 	// 	if (preg_replace('/\'/', '', strtolower($original->name)) == preg_replace('/\s\(levequest\)/', '', preg_replace('/\'/', '', strtolower($extra->name))))
		// 	// 	{
		// 	// 		$leve = $original;
		// 	// 		break;
		// 	// 	}

		// 	// if ($leve == null)
		// 	// 	continue;

		// 	// echo 'Improving ' . $leve->name . '<br>';

		// 	// continue;

		// 	// foreach ($extra->rewards as $reward)
		// 	// {
		// 	// 	$tmp =& $rewards[$leve->class][$leve->level][$reward->item_name];
		// 	// 	$tmp[$reward->amount ?: 1]++;
		// 	// }

		// 	// $leve->xp_min = (int) min($leve->xp, $extra->xp);
		// 	// $leve->xp_max = (int) max($leve->xp, $extra->xp);

		// 	// if (is_null($extra->gil_max))
		// 	// 	$extra->gil_max = $extra->gil_min;

		// 	// $leve->gil_min = (int) min($leve->gil, $extra->gil_min);
		// 	// $leve->gil_max = (int) max($leve->gil, $extra->gil_max);

		// 	// unset($leve->gil, $leve->xp);

		// 	$gamerescapewiki_leves[] = $leve;
		// }

		// dd('hi');

		// foreach ($rewards as $class => $levels)
		// 	foreach ($levels as $level => $r)
		// 		foreach ($r as $item => $amounts)
		// 		{
		// 			$rewards[$class][$level][$item] = array_keys($rewards[$class][$level][$item]);
		// 			sort($rewards[$class][$level][$item]);
		// 		}
			// dd($gamerescapewiki_data[12], $gamerescapewiki_data[536]);
			echo 'hi';

		file_put_contents(storage_path() . '/app/osmose/gamerescapewiki/leves.json', json_encode($gamerescapewiki_data));
		// file_put_contents(storage_path() . '/app/osmose/gamerescapewiki/leve-rewards.json', json_encode($rewards));

		// echo 'Files built and placed in storage<br>';

		flash()->message('Leves Compiler finished');
		return redirect('/osmose');
	}

	private function basic_finder($query, $return_type = 'default')
	{
		$found = $this->finder->query($query);

		$results = [];
		foreach ($found as $f)
			$results[] = trim($f->nodeValue);

		$results = array_diff($results, array(''));

		if (count($results) == 1 && $return_type != 'array')
			return end($results);
		elseif (empty($results))
			return FALSE;

		return $results;
	}

}
<?php

	function cdn($asset)
	{
		$cdn = config('site.cdn');

		// Check if we added cdn's to the config file
		if( ! $cdn)
			return asset( $asset );

		// Cache md5 results, no need to touch the file every time
		$md5_filename = Cache::get('md5:' . $asset, function() use ($asset) {
			// md5 file contents, only first 8 characters
			$md5 = is_file(public_path() . $asset) ? ('.' . substr(md5_file(public_path() . $asset), 0, 8)) : '';
			// Place md5 string inside filename
			return preg_replace('/\.([^\.]+)$/', $md5 . '.$1', $asset);
		});

		return '//' . $cdn . $md5_filename;
	}

	function assetcdn($asset)
	{
		$cdn = config('site.asset_cdn');

		// Check if we added cdn's to the config file
		if( ! $cdn)
			return asset( $asset );

		return '//' . $cdn . '/' . $asset;
	}

	function random_guardian_name()
	{
		$g = Guardians::with('name')->where('id', rand(1,12))->remember(config('site.cache_length'))->first();
		return $g->name->term;
	}

	function random_donation_slogan()
	{
		$slogans = config('site.donation_slogans');
		return $slogans[array_rand($slogans)];
	}

	function menu_item($href = '', $label = '', $active = '', $class = '')
	{
		$class .= View::shared('active') == $active ? ' active' : '';
		$tag = $class ? '<li class="' . trim($class) . '">' : '<li>';
		return $tag . '<a href=\'' . $href . '\'>' . $label . '</a></li>';
	}

	function recent_posts($limit = 3)
	{
		return Cache::remember('reddit-posts', 30, function() use($limit)
		{
			$user_agent = 'User-Agent: php:ffxivcrafting:v0.0.1 (by /u/tickthokk)';

			$posts = [];

			try {
				$request = new Jyggen\Curl\Request('http://api.reddit.com/user/tickthokk/submitted.json');
				$request->setOption(CURLOPT_HTTPHEADER, [$user_agent]);
				$request->execute();

				if ($request->isSuccessful())
				{
					$response = json_decode($request->getResponse()->getContent());

					foreach ($response->data->children as $child)
					{
						if ($child->data->subreddit == 'ffxivcrafting')
						{
							$posts[] = [
								'title' => $child->data->title,
								'url' => 'http://reddit.com' . $child->data->permalink,
								'created' => Carbon\Carbon::createFromTimeStamp($child->data->created)->format('M d, Y')
							];
						}

						if (count($posts) == $limit)
							break;
					}
				}
			} catch (Exception $e) {
				// Do nothing
			}

			return $posts;
		});
	}

	function stat_name($attribute)
	{
		return \App\Models\CAAS\Stat::name($attribute);
	}

	function toggle_query_string($argument = '', $value = 1)
	{
		$uri = parse_url($_SERVER['REQUEST_URI']);

		if (isset($uri['query']))
			parse_str($uri['query'], $queries);
		else
			$queries = [];

		if (isset($queries[$argument]))
			unset($queries[$argument]);
		else
			$queries[$argument] = $value;

		$uri['query'] = http_build_query($queries);

		if (empty($uri['query']))
			return $uri['path'];

		return implode('?', $uri);
	}

	function xivdb_item_link() {
		return 'http://xivdb.com/item/';
	}

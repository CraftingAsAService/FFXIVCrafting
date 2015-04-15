<?php

	function cdn($asset)
	{
		$cdn = Config::get('site.cdn');
		
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
		$cdn = Config::get('site.asset_cdn');
		return '//' . $cdn . '/' . $asset;
	}

	function random_guardian_name()
	{
		$g = Guardians::with('name')->where('id', rand(1,12))->remember(Config::get('site.cache_length'))->first();
		return $g->name->term;
	}

	function random_donation_slogan()
	{
		$slogans = Config::get('site.donation_slogans');
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
			
			$request = new Jyggen\Curl\Request('http://api.reddit.com/user/tickthokk/submitted.json');
			$request->setOption(CURLOPT_HTTPHEADER, [$user_agent]);
			$request->execute();
			
			if ( ! $request->isSuccessful())
				return [];

			$response = json_decode($request->getResponse()->getContent());
		
			$posts = [];
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

			return $posts;
		});
	}
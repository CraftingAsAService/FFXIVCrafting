<?php
	
	function cdn($asset)
	{
		$cdn = Config::get('app.cdn');
		// Check if we added cdn's to the config file
		if( ! $cdn)
			return asset( $asset );

		// Cache md5 results, no need to touch the file every time
		$md5_filename = Cache::get('md5:' . $asset, function() use ($asset) {
			// md5 file contents
			$md5 = md5_file(public_path() . $asset);
			// Place md5 string inside filename
			return preg_replace('/\.([^\.]+)$/', '.' . $md5 . '.$1', $asset);
		});

		return '//' . $cdn . $md5_filename;
	}
	
	function assetcdn($asset)
	{
		$cdn = Config::get('app.asset_cdn');
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
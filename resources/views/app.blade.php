<?php
	// Config options not always present (maintenance page)
	$lang = Config::get('language');
	if ( ! is_string($lang)) $lang = 'en';
	$lbu = Config::get('language_base_url');
	if ( ! is_string($lbu)) $lbu = $_SERVER['REQUEST_URI'];
	$lbu = preg_replace(['/www\./', '/\/\//'], ['', '/'], $lbu);
?><!DOCTYPE html>
<html lang='en-us'>
	<head>
		<meta http-equiv='X-UA-Compatible' content='IE=Edge'>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="/img/favicon@2x.png" rel="icon" type="image/png">
		<meta name='csrf-token' content='{{ Session::token() }}'>

		<!-- Google Webmaster Tools Verification -->
		<meta name="google-site-verification" content="31bB29x-UyxdPFc_t--x2BnBY1mGDooQCfGo2XcmlAI">

		<!-- IE11 is stupid -->
		<meta name="msapplication-config" content="none"/>

		<title>Crafting as a Service | Final Fantasy XIV ARR Crafting Information</title>
		<meta name='description' content='Final Fantasy XIV ARR Crafting Information and Planning'>
		<meta name='keywords' content=''>

		<meta charset='utf-8'>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>

		@yield('meta')

		<link href='{!! cdn('/css/bootstrap.css') !!}' rel='stylesheet' />
		<link href='{!! cdn('/css/bootstrap-theme.css') !!}' rel='stylesheet' />
		{{-- DO NOT HOST ON CDN --}}<link href='/css/local.css' rel='stylesheet' />{{-- /DO NOT HOST ON CDN --}}

		@yield('vendor-css')

		<link href='{!! cdn('/css/global.css') !!}' rel='stylesheet' />

		<!-- New Theme, woot woot! -->
		<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,700' rel='stylesheet' type='text/css'>
		<link href='{!! cdn('/css/theme.css') !!}' rel='stylesheet' />

		@yield('css')
	</head>
	<body>

		{{-- MOBILE MENU ONLY --}}
		<nav id='mobile-nav'>
			<div class='header-bar'>
				<div class="container">
					<ul>
						<li>
							<label id='mobile-menu-button' class='toggle-mobile-nav'>
								Close Menu
							</label>
						</li>
					</ul>
				</div>
			</div>
			<div class="container navbox">
				<ul class='nav navbar-nav'>

					<li>
						<a href="/list"{!! isset($active) && $active == 'list' ? ' class="active"' : '' !!}>
							<img src="/img/icons/bag.png">
							<span>Crafting List</span>
						</a>
					</li>

					<li>
						<a href="/account"{!! isset($active) && $active == 'account' ? ' class="active"' : '' !!}>
							@if(isset($account) && $account)
							<img src='{!! $account['avatar'] !!}' width='16' height='16' class='border-radius'>
							<span>{!! $character_name !!}</span>
							@else
							<img src="/img/icons/account.png">
							<span>Account</span>
							@endif
						</a>
					</li>

					<li>
						<hr>
					</li>

					{{-- See /app/Helpers/ViewHelper.php for menu_item() function --}}
					{!! menu_item('/',			'Home',			'home'		) !!}
					{!! menu_item('/equipment',	'Equipment',	'equipment'	) !!}
					{!! menu_item('/crafting',	'Crafting',		'crafting'	) !!}
					{!! menu_item('/career',	'Career',		'career'	) !!}
					{!! menu_item('/recipes',	'Recipe Book',	'recipes'	) !!}
					{!! menu_item('/food',		'Food',			'food'		) !!}
					{!! menu_item('/levequests','Leves',		'leves'		) !!}
					{!! menu_item('/stats',		'Stats',		'stats'		) !!}
					{!! menu_item('/materia',	'Materia',		'materia'	) !!}

					<li>
						<hr>
					</li>

					@foreach(Config::get('site.full_languages') as $slug => $language)
					<?php if ($slug == $lang) continue; ?>
					<li>
						<a tabindex='-1' href='http://{{ ($slug != 'en' ? $slug . '.' : '') . $lbu }}'>
							<img src="/img/icons/flags/{{ $slug }}.png"> {!! $language !!}
						</a>
					</li>
					@endforeach
				</ul>

			</div>
		</nav>
		<main id='main-container'>
			<header id='main-header'>
				<div id="account">
					<div class="container">
						<ul class='hidden-xs hidden-sm'>
							@if(app()->environment('local'))
							<li>
								<a href='/osmose'{!! isset($active) && $active == 'osmose' ? ' class="active"' : '' !!}>
									<img src='/img/osmose.png' width='14' height='14'>
									Osmose
								</a>
							</li>
							@endif
							<li class='language-selector dropdown'>
								<a href="#" class='dropdown-toggle' data-toggle='dropdown'>
									<img src="/img/icons/flags/{!! $lang !!}.png">
									<span>{!! Config::get('site.full_languages')[$lang] !!}</span>
								</a>
								<ul class='dropdown-menu' role='menu'>
									@foreach(Config::get('site.full_languages') as $slug => $language)
									<?php if ($slug == $lang) continue; ?>
									<li>
										<a tabindex='-1' href='http://{!! ($slug != 'en' ? $slug . '.' : '') . $lbu !!}'>
											<img src="/img/icons/flags/{!! $slug !!}.png"> {!! $language !!}
										</a>
									</li>
									@endforeach
								</ul>
							</li>
							<li>
								<a href="/account"{!! isset($active) && $active == 'account' ? ' class="active"' : '' !!}>
									@if(isset($account) && $account)
									<img src='{!! $account['avatar'] !!}' width='16' height='16' class='border-radius'>
									<span>{!! $character_name !!}</span>
									@else
									<img src="/img/icons/account.png">
									<span>Account</span>
									@endif
								</a>
							</li>
							<li>
								<a href="/list"{!! isset($active) && $active == 'list' ? ' class="active"' : '' !!}>
									<img src="/img/icons/bag.png">
									<span>Crafting List</span>
								</a>
							</li>
						</ul>
						<ul class='visible-xs visible-sm'>
							<li>
								<label id='mobile-menu-button' class='toggle-mobile-nav'>
									<img src="/img/reward.png" width='12' height='12'>
									<span>Menu</span>
								</label>
							</li>
						</ul>
					</div>
				</div>
				<div id="header">
					<div class='navbar'>
						<div class='container'>
							<div class="row">
								<div class="col-xs-12 col-md-3 logo">
									<a href='/'>
										<img src="/img/logo.png" class="img-responsive" width='263' height='45' alt='FFXIV Crafting'>
										<span class='tagline'>Crafting as a Service</span>
									</a>
								</div>
								<div class="hidden-xs hidden-sm col-md-9 menu-navbar">
									<div class='navbar-header'>
									</div>
									<div class='collapse navbar-collapse'>
										<ul class='nav navbar-nav'>
											{{-- See /app/helpers.php for menu_item() function --}}
											{{-- menu_item('/',			'Home',			'home'		) --}}
											{!! menu_item('/equipment',	'Equipment',	'equipment'	) !!}
											{!! menu_item('/crafting',	'Crafting',		'crafting'	) !!}
											{!! menu_item('/recipes',	'Recipe Book',	'recipes'	) !!}
											{!! menu_item('/levequests','Leves',		'leves'		) !!}
											{!! menu_item('/career',	'Career',		'career'	) !!}
											{!! menu_item('/food',		'Food',			'food'		) !!}
											<li class='dropdown{!! (isset($active) && in_array($active, array('stats', 'materia', 'quests'))) || Request::segment(1) == 'blog' ? ' active' : '' !!}'>
												<a href='#' class='dropdown-toggle' data-toggle="dropdown">Resources <b class='caret'></b></a>
												<ul class='dropdown-menu dropdown-menu-right'>
													<li{!! isset($active) && $active == 'stats' ? ' class="active"' : '' !!}><a href='/stats'>Stats</a></li>
													<li{!! isset($active) && $active == 'materia' ? ' class="active"' : '' !!}><a href='/materia'>Materia</a></li>
													<li{!! isset($active) && $active == 'quests' ? ' class="active"' : '' !!}><a href='/quests'>Quests</a></li>
													<li class='divider'></li>
													<li><a href='http://www.reddit.com/r/ffxivcrafting'>Subreddit</a></li>
												</ul>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</header>

			<section id='content-container'>
				<div id="banner">
					<div class="container">
						@yield('banner')
					</div>
				</div>
				<div id="content">
					@yield('precontent')

					<div class='container'>

						@include('partials.flash')

						@yield('content')

					</div>
				</div>

				<div id='footer'>
					<div class='container'>

						<div class="row">
							<div class="col-sm-3">
								<p class="headline">Recent News</p>

								{{-- See /app/Helpers/ViewHelper.php for recent_posts() function --}}
								@foreach(recent_posts() as $post)
								<div class="post">
									<div class="title">
										<a href="{{ $post['url'] }}">{{ $post['title'] }}</a>
									</div>
									<div class="date">
										<img src="/img/icons/time.png"><span>{{ $post['created'] }}</span>
									</div>
									<hr>
								</div>
								@endforeach

								<p class="view-all"><a href="http://www.reddit.com/r/ffxivcrafting">View All Recent News</a></p>
							</div>
							<div class="col-sm-3">
								<p class="headline">Current Patch</p>
								<img src="/img/patch/3.5.png" class="img-responsive">
								<p>This site has been optimized for Patch 3.5</p>
							</div>
							<div class="col-sm-3">
								<p class="headline">Donations</p>
								<p>I've spent more time building this site than actually playing.  Buy me a beer!</p>
								<p class="view-all"><a href="#buymeabeer" id='buymeabeer'>Donate Today!</a></p>
								<form action='https://www.paypal.com/cgi-bin/webscr' method='post' target='_top' class='hidden'>
									<input type='hidden' name='cmd' value='_s-xclick'>
									<input type='hidden' name='hosted_button_id' value='NWDCLNE6FY76U'>
									<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif' border='0' name='submit' id='buymeabeer_button'>
									<img alt='' border='0' src='https://www.paypalobjects.com/en_US/i/scr/pixel.gif' width='1' height='1'>
								</form>
							</div>
							<div class="col-sm-3">
								<p class="headline">Other Links</p>

								<div class='row'>
									<div class='col-xs-12 col-md-6'>
										<p><a href="http://www.reddit.com/r/ffxivcrafting">Subreddit</a></p>
										<hr>
									</div>
									<div class='col-xs-12 col-md-6'>
										<p><a href="/report">Report a bug</a></p>
										<hr>
									</div>
									<div class='col-xs-12 col-md-6'>
										<p><a href="http://na.finalfantasyxiv.com/lodestone/character/2859264/">My Character</a></p>
										<hr>
									</div>
									<div class='col-xs-12 col-md-6'>
										<p><a href="mailto:tickthokk@gmail.com">Contact Me</a></p>
										<hr>
									</div>
									<div class='col-xs-12 col-md-6'>
										<p><a href="http://garlandtools.org/">Garland Tools</a></p>
										<hr>
									</div>
									<div class='col-xs-12 col-md-6'>
										<p><a href="http://ffxivclock.com/">FFXIV Clock</a></p>
										<hr>
									</div>
								</div>
								<p><a href="/credits">Source Credits &amp; Resources</a></p>
							</div>
						</div>
					</div>
				</div>
				<div id="copyright-info">
					<div class="container">
						<div class="row">
							<div class="col-xs-12 col-sm-9">
								{!! date('Y') !!} FFXIV Crafting - Crafting as a Service. FINAL FANTASY is a registered trademark of Square Enix Holdings Co., Ltd.
							</div>
							<div class="col-xs-12 col-sm-3 text-right">
								<a href="#">Back To Top<span class="glyphicon glyphicon-chevron-up"></span></a>
							</div>
						</div>
					</div>
				</div>
			</section>
		</main>

		@yield('modals')

		<div id='notifications'></div>

		<!-- jQuery -->
		<script src='{!! cdn('/js/jquery-2.0.3.min.js') !!}'></script>
		<script src='//code.jquery.com/ui/1.10.3/jquery-ui.js'></script>

		<script src='{!! cdn('/js/bootstrap.min.js') !!}' type='text/javascript'></script>

		<script src='{!! cdn('/js/noty.js') !!}' type='text/javascript'></script>
		<script src='{!! cdn('/js/noty-bottomCenter.js') !!}' type='text/javascript'></script>
		<script src='{!! cdn('/js/noty-theme.js') !!}' type='text/javascript'></script>

		<script src='{!! cdn('/js/viewport.js') !!}' type='text/javascript'></script>

		<script src='{!! cdn('/js/global.js') !!}' type='text/javascript'></script>

		@yield('javascript')

		<script type='text/javascript'>
			if (typeof xivdb_tooltips === 'undefined')
				var xivdb_tooltips = {
					language: "{!! strtoupper($lang == 'ja' ? 'jp' : $lang) !!}",
					jqueryEmbed: false,
					seturlname: false,
					seturlicon: false
				}
		</script>
		<script src="http://xivdb.com/tooltips.min.js"></script>

		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-43830923-1', 'ffxivcrafting.com');
			ga('require', 'displayfeatures');
			ga('send', 'pageview');
		</script>
	</body>
</html>
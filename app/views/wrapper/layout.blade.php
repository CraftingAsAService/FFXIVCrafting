<!DOCTYPE html>
<html lang='en-us'>
	<head>
		<meta http-equiv='X-UA-Compatible' content='IE=Edge'>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<title>Crafting as a Service | Final Fantasy XIV ARR Crafting Information</title>
		<meta name='description' content='Final Fantasy XIV ARR Crafting Information and Planning'>
		<meta name='keywords' content=''

		<meta charset='utf-8'>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>

		<link href='{{ cdn('/css/bootstrap.css') }}' rel='stylesheet' />
		<link href='{{ cdn('/css/bootstrap-theme.min.css') }}' rel='stylesheet' />
		{{-- DO NOT HOST ON CDN --}}<link href='/css/local.css' rel='stylesheet' />{{-- /DO NOT HOST ON CDN --}}

		@section('vendor-css')
		@show

		<link href='{{ cdn('/css/global.css') }}' rel='stylesheet' />

		<!-- New Theme, woot woot! -->
		<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,700' rel='stylesheet' type='text/css'>
		<link href='{{ cdn('/css/theme.css') }}' rel='stylesheet' />

		@section('css')
		@show
	</head>
	<body>

		<div id="account">
			<div class="container">
				<p>This is the new account section</p>
			</div>
		</div>
		<div id="header">
			<div class='navbar'>
				<div class='container'>
					<div class='navbar-header'>
						<button type='button' class='navbar-toggle' data-toggle='collapse' data-target='.navbar-collapse'>
							<span class='icon-bar'></span>
							<span class='icon-bar'></span>
							<span class='icon-bar'></span>
						</button>
						<a class='navbar-brand' href='/'>FFXIV CAAS</a>
					</div>
					<div class='collapse navbar-collapse'>
						<ul class='nav navbar-nav navbar-right'>
							<li{{ isset($active) && $active == 'list' ? ' class="active"' : '' }}><a href='/list'><i class='glyphicon glyphicon-shopping-cart'></i> Crafting List</a></li>
						</ul>
						<ul class='nav navbar-nav hidden-sm'>
							<li{{ isset($active) && $active == 'equipment' ? ' class="active"' : '' }}><a href='/equipment'>Equipment</a></li>
							<li{{ isset($active) && $active == 'crafting' ? ' class="active"' : '' }}><a href='/crafting'>Crafting</a></li>
							<li{{ isset($active) && $active == 'career' ? ' class="active"' : '' }}><a href='/career'>Career</a></li>
							<li{{ isset($active) && $active == 'recipes' ? ' class="active"' : '' }}><a href='/recipes'>Recipe Book</a></li>
							<li{{ isset($active) && $active == 'quests' ? ' class="active"' : '' }}><a href='/quests'>Quests</a></li>
							<li{{ isset($active) && $active == 'leves' ? ' class="active"' : '' }}><a href='/leve'>Leves</a></li>
						</ul>
						<ul class='nav navbar-nav visible-sm'>
							<li class='dropdown{{ isset($active) && in_array($active, array('stats', 'materia', 'food')) ? ' active' : '' }}'>
								<a href='#' class='dropdown-toggle' data-toggle="dropdown">Tools <b class='caret'></b></a>
								<ul class='dropdown-menu'>
									<li{{ isset($active) && $active == 'equipment' ? ' class="active"' : '' }}><a href='/equipment'>Equipment</a></li>
									<li{{ isset($active) && $active == 'crafting' ? ' class="active"' : '' }}><a href='/crafting'>Crafting</a></li>
									<li{{ isset($active) && $active == 'career' ? ' class="active"' : '' }}><a href='/career'>Career</a></li>
								</ul>
							</li>
							<li class='dropdown{{ isset($active) && in_array($active, array('stats', 'materia', 'food')) ? ' active' : '' }}'>
								<a href='#' class='dropdown-toggle' data-toggle="dropdown">Info <b class='caret'></b></a>
								<ul class='dropdown-menu'>
									<li{{ isset($active) && $active == 'recipes' ? ' class="active"' : '' }}><a href='/recipes'>Recipe Book</a></li>
									<li{{ isset($active) && $active == 'quests' ? ' class="active"' : '' }}><a href='/quests'>Quests</a></li>
									<li{{ isset($active) && $active == 'leves' ? ' class="active"' : '' }}><a href='/leve'>Leves</a></li>
								</ul>
							</li>
						</ul>
						<ul class='nav navbar-nav hidden-xs'>
							<li class='dropdown{{ isset($active) && in_array($active, array('stats', 'materia', 'food')) ? ' active' : '' }}'>
								<a href='#' class='dropdown-toggle' data-toggle="dropdown">Resources <b class='caret'></b></a>
								<ul class='dropdown-menu'>
									<li{{ isset($active) && $active == 'stats' ? ' class="active"' : '' }}><a href='/stats'>Stats</a></li>
									<li{{ isset($active) && $active == 'materia' ? ' class="active"' : '' }}><a href='/materia'>Materia</a></li>
									<li{{ isset($active) && $active == 'food' ? ' class="active"' : '' }}><a href='/food'>Food</a></li>
								</ul>
							</li>
						</ul>
						<ul class='nav navbar-nav visible-xs'>
							<li class='{{ isset($active) && $active == 'stats' ? ' active' : '' }}'><a href='/stats'>Stats</a></li>
							<li class='{{ isset($active) && $active == 'materia' ? ' active' : '' }}'><a href='/materia'>Materia</a></li>
							<li class='{{ isset($active) && $active == 'food' ? ' active' : '' }}'><a href='/food'>Food</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div id="banner">
			<div class="container">
				<h1>I AM THE NEW BANNER SIZES</h1>
				<h2>I'm a supporting headline copy that doesn't really support anything.</h2>
			</div>
		</div>
		<div id='wrap'>
			@yield('precontent')

			<div class='container'>

				@yield('content')

			</div>
		</div>

		<div id='footer'>
			<div class='container'>

				<div class="row">
					<div class="col-sm-3">
						<p class="headline">Recent News</p>

						<?php $wardrobe = new \Wardrobe\Core\Repositories\DbPostRepository(); ?>
						@foreach($wardrobe->active(3) as $post)
							<div class="post">
								<div class="title">
									<a href="/blog/post/{{{ $post->slug }}}">{{ $post->title }}</a>
								</div>
								<div class="date">
									{{ date("M d, Y", strtotime($post->publish_date)) }}
									<?php /*by <span class='who'>{{ $post->user->first_name }} {{ $post->user->last_name }}</span> */ ?>
								</div>
								<hr>
							</div>
						@endforeach

						<p class="view-all"><a href="/blog/">View All Recent News</a></p>
					</div>
					<div class="col-sm-3">
						<p class="headline">Current Patch</p>
						<img src="/img/current_patch.png" class="img-responsive">
						<p>This site has been optimzed for Patch 2.3 Defenders of Eorzea</p>
					</div>
					<div class="col-sm-3">
						<p class="headline">Donations</p>
						<p>I've spent more time building this site than actually playing. Help me relax and show my wife this isn't just a hobby!</p>
						<p class="view-all"><a href="#buymeabeer">Donate Today!</a></p>
					</div>
					<div class="col-sm-3">
						<p class="headline">Other Links</p>

						<p><a href="mailto:tickthokk@gmail.com">Contact Me</a></p>
						<hr>
						<p><a href="/report">Report a bug</a></p>
						<hr>
						<p><a href="http://na.finalfantasyxiv.com/lodestone/character/2859264/">My Character</a></p>
						<hr>
						<p><a href="http://ffxivclock.com/">FFXIV Clock</a></p>
						<hr>
						<p><a href="http://www.reddit.com/r/ffxivcrafting">Subreddit</a></p>
						<hr>
						<p><a href="/credits">Source Credits &amp; Resources</a></p>
					</div>
				</div>
			</div>
		</div>
		<div id="copyright-info">
			<div class="container">
				<div class="row">
					<div class="col-sm-8">
						2014 FFXIV - Crafting as a Service. FINAL FANTASY is a registered trademark of Square Enix Holdings Co., Ltd.
					</div>
					<div class="col-sm-4 text-right">
						<a href="#">Back To Top</a>
					</div>
				</div>
			</div>
		</div>

		<div id='notifications'></div>

		<!-- jQuery -->
		<script src='{{ cdn('/js/jquery-2.0.3.min.js') }}'></script>
		<script src='//code.jquery.com/ui/1.10.3/jquery-ui.js'></script>

		<script src='{{ cdn('/js/bootstrap.min.js') }}' type='text/javascript'></script>

		<script src='{{ cdn('/js/noty.js') }}' type='text/javascript'></script>
		<script src='{{ cdn('/js/noty-bottomCenter.js') }}' type='text/javascript'></script>
		<script src='{{ cdn('/js/noty-theme.js') }}' type='text/javascript'></script>

		<script src='{{ cdn('/js/viewport.js') }}' type='text/javascript'></script>

		<script src='{{ cdn('/js/global.js') }}' type='text/javascript'></script>

		@section('javascript')
		@show

		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-43830923-1', 'craftingasaservice.com');
			ga('send', 'pageview');
		</script>
	</body>
</html>
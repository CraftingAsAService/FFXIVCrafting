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

		@section('css')
		@show
	</head>
	<body>
		<div id='wrap'>
			<div class='navbar navbar-inverse'>
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

			@yield('precontent')
		
			<div class='container'>

				@yield('content')
					
			</div>
		</div>

		<div id='footer'>
			<div class='container'>
				<div class='row top text-center'>
					<div class='col-xs-12 col-sm-6'>
						<p class='text-muted credit'>
							{{ random_donation_slogan() }}
						</p>
					</div>
					<div class='col-xs-12 col-sm-6'>
						<p class='text-muted credit'>
							Praise be unto {{ random_guardian_name() }}
						</p>
					</div>
					<form action='https://www.paypal.com/cgi-bin/webscr' method='post' target='_top' class='hidden'>
						<input type='hidden' name='cmd' value='_s-xclick'>
						<input type='hidden' name='hosted_button_id' value='NWDCLNE6FY76U'>
						<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif' border='0' name='submit' id='buymeabeer_button'>
						<img alt='' border='0' src='https://www.paypalobjects.com/en_US/i/scr/pixel.gif' width='1' height='1'>
					</form>
				</div>
				<div class='row bottom'>
					<div class='col-sm-4'>
						<h4>Site</h4>
						<p class='text-muted credit'>
							Patch 2.3 Ready
						</p>
					</div>
					<div class='col-sm-4'>
						<h4>Contact</h4>
						<p class='text-muted credit'>
							<a href='mailto:tickthokk@gmail.com'>Email Me</a>
						</p>
						<p class='text-muted credit'>
							<a href='http://na.finalfantasyxiv.com/lodestone/character/2859264/' target='_blank'>My Character</a> 
						</p>
					</div>
					<div class='col-sm-4'>
						<h4>Other Cool Sites</h4>
						<p class='text-muted credit'>
							<a href='http://ffxivclock.com/' target='_blank' rel='tooltip' title='Opens in new window'>
								FFXIV Clock<span class='glyphicon glyphicon-new-window' style='margin-left: 5px;'></span>
							</a> 
						</p>
						<p class='text-muted credit'>
							<a href='/credits'>Source Credits &amp; Resources</a> 
						</p>
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
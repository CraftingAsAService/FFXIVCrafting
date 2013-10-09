<!DOCTYPE html>
<html lang='en-us'>
	<head>
		<meta http-equiv='X-UA-Compatible' content='IE=Edge'>
		<title>Crafting as a Service</title>

		<meta charset='utf-8'>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>

		<link href='/css/bootstrap.css' rel='stylesheet' />
		<link href='/css/bootstrap-theme.min.css' rel='stylesheet' />

		@section('vendor-css')
		@show

		<link href='/css/global.css' rel='stylesheet' />
		<link href='/css/theme.css' rel='stylesheet' />

		@section('css')
		@show
	</head>
	<body>
		<div id='wrap'>
			<div id="header" class="container">
				<div class="row">
					<div class="col-md-4">
						<a href="/"><img src="/img/theme/logo.png" alt="Crafting as a Service" title="Crafting as a Service" /></a>
					</div>
<<<<<<< HEAD
					<div class="col-md-8 menu">
						<ul>
=======
					<div class='collapse navbar-collapse'>
						<ul class='nav navbar-nav navbar-right'>
							<li{{ isset($active) && $active == 'list' ? ' class="active"' : '' }}><a href='/list'><i class='glyphicon glyphicon-shopping-cart'></i> Crafting List</a></li>
						</ul>
						<ul class='nav navbar-nav hidden-sm'>
>>>>>>> master
							<li{{ isset($active) && $active == 'equipment' ? ' class="active"' : '' }}><a href='/equipment'>Equipment</a></li>
							<li{{ isset($active) && $active == 'crafting' ? ' class="active"' : '' }}><a href='/crafting'>Crafting</a></li>
							<li{{ isset($active) && $active == 'gathering' ? ' class="active"' : '' }}><a href='/gathering'>Gathering</a></li>
							<li{{ isset($active) && $active == 'recipes' ? ' class="active"' : '' }}><a href='/recipes'>Recipe Book</a></li>
							<li{{ isset($active) && $active == 'quests' ? ' class="active"' : '' }}><a href='/quests'>Quests</a></li>
<<<<<<< HEAD
							<li{{ isset($active) && $active == 'stats' ? ' class="active"' : '' }}><a href='/stats'>Stats</a></li>
							<li{{ isset($active) && $active == 'materia' ? ' class="active"' : '' }}><a href='/materia'>Materia</a></li>
							<li{{ isset($active) && $active == 'food' ? ' class="active"' : '' }}><a href='/food'>Food</a></li>
=======
							<li{{ isset($active) && $active == 'leves' ? ' class="active"' : '' }}><a href='/leve'>Leves</a></li>
						</ul>
						<ul class='nav navbar-nav visible-sm'>
							<li class='dropdown{{ isset($active) && in_array($active, array('stats', 'materia', 'food')) ? ' active' : '' }}'>
								<a href='#' class='dropdown-toggle' data-toggle="dropdown">Tools <b class='caret'></b></a>
								<ul class='dropdown-menu'>
									<li{{ isset($active) && $active == 'equipment' ? ' class="active"' : '' }}><a href='/equipment'>Equipment</a></li>
									<li{{ isset($active) && $active == 'crafting' ? ' class="active"' : '' }}><a href='/crafting'>Crafting</a></li>
									<li{{ isset($active) && $active == 'gathering' ? ' class="active"' : '' }}><a href='/gathering'>Gathering</a></li>
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
>>>>>>> master
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

			<div class="{{ isset($is_homepage) ? '' : 'interior ' }}container">

				@yield('content')

			</div>
			<div id="footer">
				<div class="container">
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<div class="row">
								<div class="col-md-3">
									<div class="bucket">
										<h4>Quick Links</h4>
										<ul>
											<li><a href="#">Equipment</a></li>
											<li><a href="#">Crafting</a></li>
											<li><a href="#">Gathering</a></li>
											<li><a href="#">Quests</a></li>
											<li><a href="#">Stats</a></li>
											<li><a href="#">Materia</a></li>
											<li><a href="#">Food</a></li>
										</ul>
									</div>
								</div>
								<div class="col-md-3">
									<div class="bucket">
										<h4>About</h4>
										<ul>
											<li><a href="#">Buy Me a Beer!</a></li>
											<li><a href="#">My Character</a></li>
											<li><a href="#">Issue Tracker</a></li>
											<li><a href="#">Credits</a></li>
										</ul>
									</div>
								</div>
								<div class="col-md-6">
									<div class="row">
										<h4>Contact</h4>
										<form role="form">
											<div class="col-md-6">
												<div class="form-group">
													<label for="exampleInputEmail1">Email address</label>
													<input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
												</div>
												<div class="form-group">
													<label for="exampleInputPassword1">Password</label>
													<input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="exampleInputEmail1">Email address</label>
													<input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
												</div>
												<div class="form-group">
													<label for="exampleInputPassword1">Password</label>
													<input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>

<<<<<<< HEAD
=======
		<div id='footer'>
			<div class='container'>
				<div class='row top text-center'>
					<?php
						$choices = array(
							"Support Alcoholism, <a href='#buymeabeer' id='buymeabeer'>Buy me a beer!</a>",
							"Keep the site ad free, <a href='#buymeabeer' id='buymeabeer'>The best AdBlock is Donating!</a>",
							"Show my wife it's not just a hobby, <a href='#buymeabeer' id='buymeabeer'>Donate!</a>",
							"Stable servers aren't free, <a href='#buymeabeer' id='buymeabeer'>Support the site!</a>",
							"I've spent more time building this than playing, <a href='#buymeabeer' id='buymeabeer'>Help me relax!</a>",
							"At least you know I'm not a Nigerian Prince, <a href='#buymeabeer' id='buymeabeer'>Donate!</a>",
							#"Help the site out, <a href='#buymeabeer' id='buymeabeer'>Like it on Facebook!</a>",
						);
					?>
					<p class='text-muted credit'>
						{{ $choices[array_rand($choices)] }}
					</p>
					<form action='https://www.paypal.com/cgi-bin/webscr' method='post' target='_top' class='hidden'>
						<input type='hidden' name='cmd' value='_s-xclick'>
						<input type='hidden' name='hosted_button_id' value='NWDCLNE6FY76U'>
						<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif' border='0' name='submit' id='buymeabeer_button'>
						<img alt='' border='0' src='https://www.paypalobjects.com/en_US/i/scr/pixel.gif' width='1' height='1'>
					</form>
				</div>
				<div class='row bottom'>
					<div class='col-sm-4'>
						<h4>Contact</h4>
						<p class='text-muted credit'>
							<a href='mailto:tickthokk@gmail.com'>Email</a>
						</p>
						<p class='text-muted credit'>
							<a href='https://github.com/Tickthokk/ffxiv-caas/issues' target='_blank'>Issue Tracker</a>
						</p>
					</div>
					<div class='col-sm-4'>
						<h4>More Info</h4>
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
>>>>>>> master
					</div>
				</div>
			</div>
		</div>

		<div id='notifications'></div>

		<!-- jQuery -->
		<script src='/js/jquery-2.0.3.min.js'></script>
		<script src='//code.jquery.com/ui/1.10.3/jquery-ui.js'></script>

		<script src='/js/bootstrap.min.js' type='text/javascript'></script>

		<script src='/js/noty.js' type='text/javascript'></script>
		<script src='/js/noty-bottomCenter.js' type='text/javascript'></script>
		<script src='/js/noty-theme.js' type='text/javascript'></script>

		<script src='/js/viewport.js' type='text/javascript'></script>

		<script src='/js/global.js' type='text/javascript'></script>

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
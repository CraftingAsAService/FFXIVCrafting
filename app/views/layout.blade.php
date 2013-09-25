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
					<div class="col-md-8 menu">
						<ul>
							<li{{ isset($active) && $active == 'equipment' ? ' class="active"' : '' }}><a href='/equipment'>Equipment</a></li>
							<li{{ isset($active) && $active == 'crafting' ? ' class="active"' : '' }}><a href='/crafting'>Crafting</a></li>
							<li{{ isset($active) && $active == 'gathering' ? ' class="active"' : '' }}><a href='/gathering'>Gathering</a></li>
							<li{{ isset($active) && $active == 'recipes' ? ' class="active"' : '' }}><a href='/recipes'>Recipe Book</a></li>
							<li{{ isset($active) && $active == 'quests' ? ' class="active"' : '' }}><a href='/quests'>Quests</a></li>
							<li{{ isset($active) && $active == 'stats' ? ' class="active"' : '' }}><a href='/stats'>Stats</a></li>
							<li{{ isset($active) && $active == 'materia' ? ' class="active"' : '' }}><a href='/materia'>Materia</a></li>
							<li{{ isset($active) && $active == 'food' ? ' class="active"' : '' }}><a href='/food'>Food</a></li>
						</ul>
						<ul class='nav navbar-nav navbar-right'>
							<li{{ isset($active) && $active == 'list' ? ' class="active"' : '' }}><a href='/list'><i class='glyphicon glyphicon-shopping-cart'></i> Crafting List</a></li>
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
		<script src='/js/noty-bottomRight.js' type='text/javascript'></script>
		<script src='/js/noty-theme.js' type='text/javascript'></script>

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
<!DOCTYPE html>
<html lang='en-us'>
	<head>
		<meta http-equiv='X-UA-Compatible' content='IE=Edge'>
		<title>FFXIV CAAS</title>
		
		<meta charset='utf-8'>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>

		<link href='/css/bootstrap.min.css' rel='stylesheet' />
		<link href='/css/bootstrap-theme.min.css' rel='stylesheet' />
		<link href='/css/global.css' rel='stylesheet' />

		@section('css')
		@show
	</head>
	<body>
		<div id='wrap'>
			<div class='navbar navbar-inverse navbar-fixed-top'>
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
						<ul class='nav navbar-nav'>
							<li><a href='/'>Calculate</a></li>
							<li{{ isset($active) && $active == 'stats' ? " class='active'" : '' }}><a href='/stats'>Stats</a></li>
							<li{{ isset($active) && $active == 'materia' ? " class='active'" : '' }}><a href='/materia'>Materia</a></li>
							<li{{ isset($active) && $active == 'food' ? " class='active'" : '' }}><a href='/food'>Food</a></li>
						</ul>
					</div>
				</div>
			</div>
		
			<div class='container'>

				@yield('content')
					
			</div>
		</div>

		<div id='footer'>
			<div class='container text-center'>
				<p class='text-muted credit'>
					<a href='http://na.finalfantasyxiv.com/lodestone/character/2859264/'>My Character</a> | <a href='mailto:tickthokk@gmail.com'>Email Me</a>
				</p>
			</div>
		</div>

		<!-- jQuery -->
		<script src='//code.jquery.com/jquery.js'></script>
		
		<script src='/js/bootstrap.min.js' type='text/javascript'></script>

		<script src='/js/global.js' type='text/javascript'></script>

		@section('javascript')
		@show
	</body>
</html>
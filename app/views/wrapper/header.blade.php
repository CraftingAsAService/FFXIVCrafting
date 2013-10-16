<!DOCTYPE html>
<html lang='en-us'>
	<head>
		<meta http-equiv='X-UA-Compatible' content='IE=Edge'>
		<title>Crafting as a Service</title>

		<meta charset='utf-8'>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>

		<link href='/css/bootstrap.css' rel='stylesheet' />


		@section('vendor-css')
		@show

		<link href='/css/global.css' rel='stylesheet' />
		<link href='/css/theme.css' rel='stylesheet' />
		<link href='/css/caas-icons.css' rel='stylesheet' />

		@section('css')
		@show
	</head>
	<body>
		<div id='wrap'>
			<div id="header" class="container">
				<div class="row logo">
					<div class="col-md-4">
						<a href="/"><img src="/img/theme/logo.png" alt="Crafting as a Service" title="Crafting as a Service" /></a>
					</div>
				</div>
				<div class="row">
					<div class='collapse navbar-collapse'>
						<ul class='nav navbar-nav hidden-sm'>

							<li{{ isset($active) && $active == 'equipment' ? ' class="active"' : '' }}><a href='/equipment'>Equipment</a></li>
							<li{{ isset($active) && $active == 'crafting' ? ' class="active"' : '' }}><a href='/crafting'>Crafting</a></li>
							<li{{ isset($active) && $active == 'gathering' ? ' class="active"' : '' }}><a href='/gathering'>Gathering</a></li>
							<li{{ isset($active) && $active == 'recipes' ? ' class="active"' : '' }}><a href='/recipes'>Recipe Book</a></li>
							<li{{ isset($active) && $active == 'quests' ? ' class="active"' : '' }}><a href='/quests'>Quests</a></li>
							<li{{ isset($active) && $active == 'leves' ? ' class="active"' : '' }}><a href='/leve'>Leves</a></li>
							<li{{ isset($active) && $active == 'stats' ? ' class="active"' : '' }}><a href='/stats'>Stats</a></li>
							<li{{ isset($active) && $active == 'materia' ? ' class="active"' : '' }}><a href='/materia'>Materia</a></li>
							<li{{ isset($active) && $active == 'food' ? ' class="active"' : '' }}><a href='/food'>Food</a></li>
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


						<ul class='nav navbar-nav visible-sm'>
							<li class='dropdown{{ isset($active) && in_array($active, array('stats', 'materia', 'food')) ? ' active' : '' }}'>
								<a href='#' class='dropdown-toggle' data-toggle="dropdown">Resources <b class='caret'></b></a>
								<ul class='dropdown-menu'>
									<li{{ isset($active) && $active == 'stats' ? ' class="active"' : '' }}><a href='/stats'>Stats</a></li>
									<li{{ isset($active) && $active == 'materia' ? ' class="active"' : '' }}><a href='/materia'>Materia</a></li>
									<li{{ isset($active) && $active == 'food' ? ' class="active"' : '' }}><a href='/food'>Food</a></li>
								</ul>
							</li>
						</ul>

						<!--<ul class='nav navbar-nav visible-xs'>
							<li class='{{ isset($active) && $active == 'stats' ? ' active' : '' }}'><a href='/stats'>Stats</a></li>
							<li class='{{ isset($active) && $active == 'materia' ? ' active' : '' }}'><a href='/materia'>Materia</a></li>
							<li class='{{ isset($active) && $active == 'food' ? ' active' : '' }}'><a href='/food'>Food</a></li>
						</ul>
					-->
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
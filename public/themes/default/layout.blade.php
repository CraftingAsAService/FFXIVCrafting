<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>@yield('title')</title>
		<link href='//fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT' rel='stylesheet' type='text/css'/>
		<link href="{{ asset(theme_path('css/style.css')) }}" rel="stylesheet" media="screen">
	</head>
	<body>
		<div class="container">
			<header>
				<h1><a href="{{ Wardrobe::route('/') }}">{{ site_title() }}</a></h1>
				<nav>
					<ul>
						<li><a href="{{ Wardrobe::route('posts.archive') }}">Posts</a></li>
						<li><a href="{{ Wardrobe::route('posts.rss') }}">RSS</a></li>
					</ul>
				</nav>
			</header>
			<div class="content">
				@yield('content')
			</div>
			<footer>
				<p>Powered by <a href="http://wardrobecms.com">Wardrobe</a></p>
			</footer>
		</div>
	</body>
</html>

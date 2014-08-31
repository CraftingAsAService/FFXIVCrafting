@extends('wrapper.layout')

@section('title')
	About Me
@stop

@section('css')
	@include(theme_view('inc.css'))
@stop

@section('banner')
	@include(theme_view('inc.links'))
	
	<h1 class="title">About Me</h1>
@stop

@section('content')

	<section class="about">

		<img src='/img/about/objection.jpg' style='width: 200px; float: left;'>

		<div style='margin-left: 220px;'>
			<p>
				Here I am.  Objecting.  ...it's an old picture.
			</p>

			<h4>Facts</h4>

			<ul>
				<li>
					I've been a Web Developer since 2001.
				</li>
				<li>
					I'm gainfully employed.
					<ul>
						<li>This site fulfils two hobby's at once: Web Development and Gaming.</li>
					</ul>
				</li>
				<li>
					I'm married.
				</li>
				<li>
					I love video games.  
					<ul>
						<li>Sometimes I make spreadsheets and print checklists.</li>
						<li>This time I made a website.</li>
					</ul>
				</li>
				<li>
					Final Fantasy has always been one of my favorite series.
					<ul>
						<li>The music is always great.  Check out FF Theatrhythm if you have a 3DS.</li>
						<li>I've done a Solo Warrior run in FF 1... using save states.</li>
						<li>I'd say FF 6 was my favorite, but SNES was my generation's console.</li>
						<li>I played FF 11, and loved it, but never got very far.</li>
					</ul>
				</li>
				<li>
					You can get ahold of me using one of the links in the footer.  I don't bite.
				</li>
				<li>
					Despite what my beard looks like, I'm not Amish.
				</li>
			</ul>
		</div>
	</section>
@stop

@extends('layout')

@section('content')

	<h1>Credits</h1>

	<p>I use a lot of third party resources to make this site happen, and I want to give credit where credit is due.</p>

	<div class='media'>
		<a href='http://finalfantasyxiv.com/' class='pull-left'>
			<img src='/img/credits/xivlogo.png' class='media-object' width='64'>
		</a>
		<div class='media-body'>
			<h4 class='media-heading'>
				<a href='http://finalfantasyxiv.com/'>Final Fantasy XIV</a>
				<small><em>&copy; SQUARE ENIX CO., LTD.</em></small>
			</h4>
			<p>
				The game and assets obviously originate from the game, so thanks to Square Enix for making it!
			</p>
		</div>
	</div>

	<div class='media'>
		<a href='http://xivdb.com/' class='pull-left'>
			<img src='/img/credits/kokoroon_cut.png' class='media-object' width='64'>
		</a>
		<div class='media-body'>
			<h4 class='media-heading'>
				<a href='http://xivdb.com/'>XIVDB</a>
				<small><em>&copy; ZAM</em></small>
			</h4>
			<p>
				The data I use was obtained from XIVDB.  I'm also using their tooltip API everywhere.  My site could have never happend without these guys.
			</p>
		</div>
	</div>

	<div class='media'>
		<a href='http://game-icons.net/' class='pull-left'>
			<img src='/img/credits/gameicons.png' class='media-object' width='64' height='64' style='border-radius: 5px;'>
		</a>
		<div class='media-body'>
			<h4 class='media-heading'>
				<a href='http://game-icons.net/'>Game-icons.net</a>
			</h4>
			<p>
				These things are amazing.  Kudos to the artists.
			</p>
		</div>
	</div>

	<div class='media'>
		<a href='http://reddit.com/r/ffxiv' class='pull-left'>
			<img src='/img/credits/reddit.jpg' class='media-object' width='64' height='64'>
		</a>
		<div class='media-body'>
			<h4 class='media-heading'>
				<a href='http://reddit.com/r/ffxiv'>The FFXIV subreddit</a>
				<small><em>&copy; reddit inc</em></small>
			</h4>
			<p>
				It's a great resource and community.  A lot of good ideas and additions to my site have come through here.
			</p>
		</div>
	</div>

@stop
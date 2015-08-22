@extends('app')

@section('banner')
	<h1>Osmose</h1>
	<h2>Libra Parser &amp; Other Tools</h2>
@stop

@section('content')
	<h3>Tools</h3>

	<div class='row'>
		<div class='col-sm-4'>
			<h4><a href='http://garlandtools.org' target='_blank'>Garland Tools</a> Data Start</h4>

			<p>
				<a href='/osmose/garland'>Grab Garland Core Data</a><br>
			</p>
			<p>
				<a href='/osmose/garland/view'>View Garland Core Data</a><br>
			</p>
		</div>
		<div class='col-sm-4'>
			<h4><a href='http://ffxiv.gamerescape.com/' target='_blank'>GamerEscape</a> Leve Crawler</h4>

			<p>
				<a href='/osmose/leves/crawl'>Crawl Leves</a>
			</p>
			<p>
				<a href='/osmose/leves/compile'>Compile Leves</a><br>
				<small>Chance of 502 error: refresh a few times until completion.</small>
			</p>
		</div>
		<div class='col-sm-4'>
			<h4><a href='http://na.finalfantasyxiv.com/lodestone/playguide/db/item/' target='_blank'>Eorzea DB</a> I18N Name Crawler</h4>

			<p>
				<a href='/osmose/eorzea/crawl-names'>Download Lists for I18N Names</a>
			</p>
			<p>
				<a href='/osmose/eorzea/parse-names'>Parse Lists for I18N Names</a>
			</p>
			<p>
				<a href='/osmose/eorzea/parse-names'>View I18N Names</a>
			</p>
		</div>
	</div>

	<hr>

	<h3>
		Artisan Commands<br>
		<small>If you don't have hhvm installed, use <code>php artisan</code> instead of <code>hhvm artisan</code>.</small>
	</h3>

	<div class='row'>
		<div class='col-sm-4'>
			<h4>Prepare Parsed Data <small>- Locally</small></h4>

			<p>
				<code>hhvm artisan migrate:refresh --seed</code>
			</p>
			<p>
				<code>hhvm artisan build</code>
			</p>
		</div>
		<div class='col-sm-4'>
			<h4>Publish Assets to CDN <small>- Anywhere</small></h4>
			
			<p>
				<code>hhvm artisan osmose:cdn:assets</code>
			</p>
			<p>
				<code>hhvm artisan osmose:cdn:images</code>
			</p>

		</div>
	</div>

@stop
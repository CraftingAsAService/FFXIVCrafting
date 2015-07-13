@extends('app')

@section('banner')
	<h1>Osmose</h1>
	<h2>Libra Parser &amp; Other Tools</h2>
@stop

@section('content')
	
	<div class='row'>
		<div class='col-sm-3'>
			<h3>Libra Parser</h3>
			<p>
				<a href='/osmose/libra/all'>Run All</a>
			</p>
			<p>
				<a data-toggle="collapse" href="#parseTable" aria-expanded="true" aria-controls="parseTable">Expand Individual Tables</a>
			</p>
		</div>
		<div class='col-sm-3'>
			<h3>Libra Schema Version</h3>
			<p>{{ date('m/d/y', strtotime($app_data->schema_version)) }}</p>
		</div>
		<div class='col-sm-3'>
			<h3>Libra Data Version</h3>
			<p>{{ date('m/d/y', strtotime($app_data->data_version)) }}</p>
		</div>
		<div class='col-sm-3'>
			<h3>Updating Libra DB</h3>
			<p>
				<a data-toggle="collapse" href="#updateLibra" aria-expanded="true" aria-controls="parseTable">Show Instructions</a>
			</p>
			<ol class='collapse' id='updateLibra'>
				<li>Update ipod touch libra application</li>
				<li>Run i-FunBox on Windows</li>
				<li>User Applications > FFXIV Libra > (Library) > Caches > app_data.sqlite</li>
				<li>Copy into <code>/database</code></li>
			</ol>
		</div>
	</div>

	<div class='collapse' id='parseTable'>
		<table class='table'>
			<thead>
				<th>Table</th>
				<th>Built</th>
				<th>Data</th>
				<th>Schema</th>
				<th>Filesize</th>
				<th>View</th>
			</thead>
			<tbody>
				@foreach($tables as $table => $version)
				<tr>
					<td><a href='/osmose/libra/{{ strtolower($table) }}'>{{ $table }}</a></td>
					@if(empty($version))
					<td colspan='5'>First Run</td>
					@else
					<td>{{ date('m/d/y', strtotime($version->built)) }}</td>
					<td>{{ date('m/d/y', strtotime($version->data)) }}</td>
					<td>{{ date('m/d/y', strtotime($version->schema)) }}</td>
					<td style='text-align: right;'>{{ $version->filesize }}</td>
					<td style='text-align: right;'><a href='/view/json/{{ strtolower($table) }}'>View</a></td>
					@endif
				</tr>
				@endforeach
				<tr>
					<td><a href='/osmose/libra/careers'>Careers</a></td>
					<td colspan='5'>Custom Script</td>
				</tr>
				<tr>
					<td><a href='/osmose/libra/nodes'>Clusters</a></td>
					<td colspan='5'>Custom Script</td>
				</tr>
			</tbody>
		</table>
	</div>

	<hr>

	<h3>Other Tools</h3>

	<div class='row'>
		<div class='col-sm-4'>
			<h4>Garland Import (<a href='http://garlandtools.org' target='_blank'>Garland Tools</a>)</h4>

			<p>
				<a href='/osmose/garland'>Impot Garland Data</a><br>
			</p>
		</div>
		<div class='col-sm-4'>
			<h4>Map Crawler (<a href='http://xivdb.com' target='_blank'>XIVDB</a>)</h4>

			<p>
				<a href='/osmose/maps/build'>Build Maps JSON &amp; Fetch Image Assets</a><br>
				<small>Chance of 502 error: refresh a few times until completion.</small>
			</p>
			<p>
				<a href='/osmose/maps/compile'>Compile Map Images</a>
			</p>
		</div>
		<div class='col-sm-4'>
			<h4>Icon Crawler (<a href='http://na.finalfantasyxiv.com/lodestone/playguide/db/item/' target='_blank'>Eorzea Database</a>)</h4>

			<p>
				<a href='/osmose/icons/crawl'>Crawl Icons</a><br>
				<small>Chance of 502 error: refresh a few times until completion.</small>
			</p>
		</div>
		<div class='col-sm-4'>
			<h4>Leve Crawler (<a href='http://ffxiv.gamerescape.com/' target='_blank'>GamerEscape Wiki</a>)</h4>

			<p>
				<a href='/osmose/leves/crawl'>Crawl Leves</a>
			</p>
			<p>
				<a href='/osmose/leves/compile'>Compile Leves</a><br>
				<small>Chance of 502 error: refresh a few times until completion.</small>
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
				<code>hhvm artisan db:seed --class=GarlandSeeder</code>
			</p>
			<p>
				<code>hhvm artisan osmose:db:export</code>
			</p>
		</div>
		<div class='col-sm-4'>
			<h4>Unpack &amp; Build Database <small>- Server</small></h4>

			<p>
				<code>hhvm artisan osmose:db:extract</code>
			</p>
			<p>
				<code>hhvm artisan osmose:db:import</code>
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
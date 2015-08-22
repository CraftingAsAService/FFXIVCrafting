
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
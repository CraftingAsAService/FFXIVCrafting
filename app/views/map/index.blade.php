@extends('wrapper.layout')

@section('javascript')
<script type='text/javascript' src='{{ cdn('/js/jquery.overscroll.js') }}'></script>
<script type='text/javascript' src='{{ cdn('/js/map.js') }}'></script>
@stop

@section('content')

<?php
	$map_size = 512;
	$icon_size = 20;
	// Map Quotient was difficult to pin down.
	// I was provided "coordinates" like -65.64 and 5.8
	// (0,0) is in the bottom left
	// Also, "X is Y and Y is X", as these were actually Longitute and Latitude-ish, and I stored them in reverse
	// I had to take Photoshop to get some pixel comparison equivalents
	// And did the math (a / b = c / X; solve for X)
	$cluster_quotient = 180;
	// Thus, while the map size is 512, coordinates reach out to 180% each direction.
	// Formulas in Action
	// 94px / -65.64° = 256 / X == -178.76
	// 42px / -29.79° = 256 / X == -181.57
	// 180 seemed to be "good enough"

	// Vendors coordinates are things like 14, and 8
	// (0,0) is in the top left
	// So the formula will be different
	// I'm guessing it's a 20x20 "grid", again, going for "good enough".
	// So, 14 to the right is really (14 / 20) * 512

	// TEST TODO REMOVE ME
	//var_dump($map_data[53]['vendors']);
?>

<div class='alert alert-warning pull-right margin-top'>
	<p><strong><em>Coordinates are not 100% accurate.</em></strong></p>
</div>

<h1>
	<i class='glyphicon glyphicon-globe'></i>
	Map
</h1>

<ul class='nav nav-tabs margin-top'>
	@foreach ($map as $area_slug => $section)
	<li class='{{ $section === reset($map) ? 'active' : '' }}'>
		<a href='#{{ $area_slug }}' data-toggle='tab'>{{ $section['area']['name'] }}</a>
	</li>
	@endforeach
</ul>

<div class='tab-content'>
@foreach ($map as $area_slug => $section)
	<div class='tab-pane {{ $section === reset($map) ? 'active' : '' }}' id='{{ $area_slug }}'>
		<div class='row'>
			<div class='col-sm-9'>
				<div class='globe {{ $area_slug }}'>
					<div class='area' data-id='{{ $section['area']['id'] }}'>
						<img src='{{ $section['area']['img'] }}.png' alt='{{{ $section['area']['name'] }}}'>

						@foreach ($section['regions'] as $region_slug => $data)
						<div class='region {{ $region_slug }}' style='top: {{ $data['top'] }}px; left: {{ $data['left'] }}px;' data-id='{{ $data['id'] }}'>
							<img src='{{ $data['img'] }}.png' alt='{{{ $data['name'] }}}' width='{{ $map_size }}' height='{{ $map_size }}'>
							<div class='name'>{{ $data['name'] }}</div>
						</div>
						@endforeach
					</div>

					<div class='region-nodes'>
					@foreach ($section['regions'] as $region_slug => $data)
						<?php 
							$ids = array($data['id']);
							if (isset($data['id_also']))
								$ids = array_merge($ids, explode(',', $data['id_also']));
						?>
						@foreach ($ids as $map_id)
						@if (isset($map_data[$map_id]))

							@if (isset($map_data[$map_id]['clusters']))
							<!-- Clusters -->
							@foreach ($map_data[$map_id]['clusters'] as $cid => $cluster)
							<?php 
								$top = $map_size - ($cluster['x'] ? ($map_size / 2) + round($cluster_quotient * ($cluster['x'] / 100)) - ($icon_size / 2) : -$icon_size + 256) + $data['top'];
								$left = ($cluster['y'] ? ($map_size / 2) + round($cluster_quotient * ($cluster['y'] / 100)) - ($icon_size / 2) : -$icon_size + 256) + $data['left'];
								$opaque = ! $cluster['x'] || ! $cluster['y'] ? ' opaque' : '';
							?>
							<img src='/img/maps/node_icons/{{ $cluster['icon'] ?: '../../reward.png' }}' class='cluster{{ $opaque }}' rel='tooltip' title='{{ $cluster['classjob_abbr'] }} lvl {{ 
							$cluster['level'] }}' data-id='{{ $cid }}' width='{{ $icon_size }}' height='{{ $icon_size }}' style='top: {{ $top }}px; left: {{ $left }}px;'>
							@endforeach
							<!-- /Clusters -->
							@endif

							@if (isset($map_data[$map_id]['vendors']))
							<!-- Vendors -->
							@foreach ($map_data[$map_id]['vendors'] as $vid => $vendor)
							<?php 
								$left = ($vendor['x'] ? ($vendor['x'] / 20) * $map_size - ($icon_size / 2) : -$icon_size + 256) + $data['left'];
								$top = ($vendor['y'] ? ($vendor['y'] / 20) * $map_size - ($icon_size / 2) : -$icon_size + 256) + $data['top'];
							?>
							<img src='/img/vendor.png' class='vendor' rel='tooltip' title='{{{ $vendor['name'] }}}' data-id='{{ $vid }}' width='{{ $icon_size }}' height='{{ $icon_size }}' style='top: {{ $top }}px; left: {{ $left }}px;'>
							@endforeach
							<!-- /Vendors -->
							@endif

							@if (isset($map_data[$map_id]['beasts']))
							<!-- Beasts -->
							@foreach ($map_data[$map_id]['beasts'] as $bid => $beast)


							@endforeach
							<!-- /Beasts -->
							@endif

						@endif
						@endforeach
					@endforeach
					</div>
				</div>
			</div>
			<div class='col-sm-3'>
				<ul>
					<li>List</li>
					<li>List</li>
					<li>List</li>
					<li>List</li>
				</ul>
			</div>
		</div>
	</div>
@endforeach
</div>

@stop
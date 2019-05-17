@extends('app')

@section('banner')
	<h1>Gathering Map</h1>
	@if(isset($map_title))
	<h2>{{ $map_title }}</h2>
	@endif
@stop

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
	#var_dump($map_data[53]['vendors']);
?>

<ul class='nav nav-tabs margin-top' style='clear: right;'>
	@foreach ($map as $area_slug => $section)
	<li class='{{ $section === reset($map) ? 'active' : '' }}'>
		<a href='#{{ $area_slug }}' data-toggle='tab'>
			<span class='visible-xs visible-sm'>{{ $section['area']['short_name'] }}</span>
			<span class='hidden-xs hidden-sm'>{{ $section['area']['name'] }}</span>
		</a>
	</li>
	@endforeach
</ul>

<div class='tab-content'>
@foreach ($map as $area_slug => $section)
	<div class='tab-pane {{ $section === reset($map) ? 'active' : '' }}' id='{{ $area_slug }}'>
		<div class='row'>
			<div class='col-sm-8'>
				<div class='globe {{ $area_slug }}'>
					<div class='area' data-id='{{ $section['area']['id'] }}'>
						<img src='{{ $section['area']['img'] }}.png' alt='{{ $section['area']['name'] }}'>

						@foreach ($section['regions'] as $region_slug => $data)
						<div class='region {{ $region_slug }}' style='top: {{ $data['top'] }}px; left: {{ $data['left'] }}px;' data-id='{{ $data['id'] }}'>
							<img src='{{ $data['img'] }}.png' alt='{{ $data['name'] }}' width='{{ $map_size }}' height='{{ $map_size }}'>
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
							<?php $i = -2; ?>
							@foreach ($map_data[$map_id]['clusters'] as $cid => $cluster)
							<?php
								$top = $map_size - ($cluster['x'] != 0 ? ($map_size / 2) + round($cluster_quotient * ($cluster['x'] / 100)) - ($icon_size / 2) : 256 - $icon_size) + $data['top'];
								$left = ($cluster['y'] != 0 ? ($map_size / 2) + round($cluster_quotient * ($cluster['y'] / 100)) - ($icon_size / 2) : 256 + ($icon_size * $i++)) + $data['left'];
								// $opaque = $cluster['x'] == 0 || $cluster['y'] == 0 ? ' opaque' : '';
							?>
							<img src='/img/maps/node_icons/{{ $cluster['icon'] ?: 'unknown.png' }}' class='map-item cluster' rel='tooltip' title='{{ $cluster['classjob_abbr'] }} lvl {{ $cluster['level'] }}' data-id='{{ $cid }}' data-type='cluster' data-items='{{ count($cluster['items']) }}' width='{{ $icon_size }}' height='{{ $icon_size }}' style='top: {{ $top }}px; left: {{ $left }}px;'>
							@endforeach
							<!-- /Clusters -->
							@endif

							@if (isset($map_data[$map_id]['vendors']))
							<!-- Vendors -->
							@foreach ($map_data[$map_id]['vendors'] as $vid => $vendor)
							<?php
								$left = ($vendor['x'] ? ($vendor['x'] / 20) * $map_size - $icon_size : 256 - $icon_size) + $data['left'];
								$top = ($vendor['y'] ? ($vendor['y'] / 20) * $map_size - $icon_size : 256 - $icon_size) + $data['top'];
							?>
							<img src='/img/vendor.png' class='map-item vendor' rel='tooltip' title='{{ $vendor['name'] }}' data-id='{{ $vid }}' data-type='vendor' data-items='{{ count($vendor['items']) }}' width='{{ $icon_size }}' height='{{ $icon_size }}' style='top: {{ $top }}px; left: {{ $left }}px;'>
							@endforeach
							<!-- /Vendors -->
							@endif

							@if (isset($map_data[$map_id]['beasts']))
							<!-- Beasts -->
							<?php $i = -3; ?>
							@foreach ($map_data[$map_id]['beasts'] as $bid => $beast)
							<img src='/img/fight.png' class='map-item beast' rel='tooltip' title='{{ $beast['name'] }}' data-id='{{ $area_slug }}-{{ $region_slug }}-{{ $bid }}' data-type='beast' data-items='{{ count($beast['items']) }}' width='{{ $icon_size }}' height='{{ $icon_size }}' style='top: {{ (256 - $icon_size) + $data['top'] }}px; left: {{ (256 + ($icon_size * $i++)) + $data['left'] }}px;'>
							@endforeach
							<!-- /Beasts -->
							@endif

						@endif
						@endforeach
					@endforeach
					</div>
				</div>
			</div>
			<div class='col-sm-4 globe_list'>
				<ul class='list-group legend'>
					<li class='list-group-item'>
						<a href='#' class='clear-selected small hidden pull-right text-danger'>Clear Selection</a>
						<a href='#legend' data-toggle='modal'>
							<i class='glyphicon glyphicon-picture margin-right'></i>Legend
						</a>
					</li>
				</ul>
				<ul class='list-group'>
				@foreach ($items as $item)
					<li class='item_level list-group-item'>
						<div>
							{{-- <span class='pull-right'>{!! Form::checkbox('', '', true) !!}</span> --}}
							<span class='pull-right opaque'>x {{ $item_list[$item->id] }}</span>
							<img src='{{ icon($item->icon) }}' class='item-icon' width='18' height='18' style='margin-right: 5px;'>{{ $item->name->term }}
						</div>
						<ul class='list-group'>
						@foreach ($section['regions'] as $region_slug => $data)
							<?php
								$ids = array($data['id']);
								if (isset($data['id_also']))
									$ids = array_merge($ids, explode(',', $data['id_also']));
							?>
							<li class='region_level list-group-item'>
								<div>
									{{-- <span class='pull-right'>{!! Form::checkbox('', '', true) !!}</span> --}}
									<i class='glyphicon glyphicon-globe'></i>
									{{ $data['name'] }}
								</div>
								<ul class='list-group'>
								@foreach ($ids as $map_id)
								@if (isset($map_data[$map_id]))
									@if (isset($map_data[$map_id]['clusters']))
									<li class='cluster_level list-group-item'>
										<div>
											{{-- <span class='pull-right'>{!! Form::checkbox('', '', true) !!}</span> --}}
											<i class='glyphicon glyphicon-tree-conifer'></i>
											Gathering
										</div>
										<ul class='list-group'>
										@foreach ($map_data[$map_id]['clusters'] as $cid => $cluster)
											@if(in_array($item->id, array_keys($cluster['items'])))
											<li class='node_level list-group-item'>
												<span class='pull-right'>{!! Form::checkbox('', '', true) !!}</span>
												<img src='/img/maps/node_icons/{{ $cluster['icon'] ?: 'unknown.png' }}' width='18' height='18' class='cluster node-item' data-id='{{ $cid }}' data-type='cluster'>
												{{ $cluster['classjob_abbr'] }}, Level {{ $cluster['level'] }}
											</li>
											@endif
										@endforeach
										</ul>
									</li>
									@endif
									@if (isset($map_data[$map_id]['vendors']))
									<li class='cluster_level list-group-item'>
										<div>
											{{-- <span class='pull-right'>{!! Form::checkbox('', '', true) !!}</span> --}}
											<i class='glyphicon glyphicon-usd'></i>
											Vendors
										</div>
										<ul class='list-group'>
										@foreach ($map_data[$map_id]['vendors'] as $vid => $vendor)
											@if(in_array($item->id, array_keys($vendor['items'])))
											<li class='node_level list-group-item'>
												<span class='pull-right'>{!! Form::checkbox('', '', true) !!}</span>
												<img src='/img/vendor.png' width='18' height='18' class='vendor node-item' data-id='{{ $vid }}' data-type='vendor'>
												{{ $vendor['name'] }}
											</li>
											@endif
										@endforeach
										</ul>
									</li>
									@endif
									@if (isset($map_data[$map_id]['beasts']))
									<li class='cluster_level list-group-item'>
										<div>
											{{-- <span class='pull-right'>{!! Form::checkbox('', '', true) !!}</span> --}}
											<i class='glyphicon glyphicon-heart-empty'></i>
											Beasts
										</div>
										<ul class='list-group'>
										@foreach ($map_data[$map_id]['beasts'] as $bid => $beast)
											@if(in_array($item->id, array_keys($beast['items'])))
											<li class='node_level list-group-item'>
												<span class='pull-right'>{!! Form::checkbox('', '', true) !!}</span>
												<img src='/img/fight.png' width='18' height='18' class='beast node-item' data-id='{{ $area_slug }}-{{ $region_slug }}-{{ $bid }}' data-type='beast'>
												{{ $beast['name'] }}
											</li>
											@endif
										@endforeach
										</ul>
									</li>
									@endif

								@endif
								@endforeach
								</ul>
							</li>
						@endforeach
						</ul>
					</li>
				@endforeach


			</div>
		</div>
	</div>
@endforeach
</div>
@stop

@section('modals')

<div class="modal fade" id='legend'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Map Legend</h4>
			</div>
			<div class="modal-body">

				<div class='row'>
					<div class='col-sm-2 text-center'>
						<div>
							<img src='/img/maps/node_icons/8750.png' width='20' height='20'>
							<img src='/img/maps/node_icons/8755.png' width='20' height='20'>
						</div>
						<div>
							<img src='/img/maps/node_icons/060432.png' width='20' height='20'>
							<img src='/img/maps/node_icons/060437.png' width='20' height='20'>
						</div>
					</div>
					<div class='col-sm-10'>
						Gathering icons.  Locations on map should be pretty close to their actual area.  Represents the center point to a cluster of nodes, not the individual nodes themselves.
					</div>
				</div>

				<hr>

				<div class='row'>
					<div class='col-sm-2 text-center'>
						<img src='/img/maps/node_icons/unknown.png' width='20' height='20'>
					</div>
					<div class='col-sm-10'>
						Gathering icon representing an unknown location.  Will (almost) always display below the name of the region.
					</div>
				</div>

				<hr>

				<div class='row'>
					<div class='col-sm-2 text-center'>
						<img src='/img/vendor.png' width='20' height='20'>
					</div>
					<div class='col-sm-10'>
						Vendor locations.  These should be pretty close to their actual spot as well.
					</div>
				</div>

				<hr>

				<div class='row'>
					<div class='col-sm-2 text-center'>
						<img src='/img/fight.png' width='20' height='20'>
					</div>
					<div class='col-sm-10'>
						Enemy icons.  Locations of enemys are unknown, so these will (almost) always display above the name of the region.
					</div>
				</div>

				<hr>

				<div class='row'>
					<div class='col-sm-10 col-sm-offset-2'>
						<p class='text-warning'>
							Coordinates are not 100% accurate.
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@stop
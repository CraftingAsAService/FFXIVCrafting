@extends('layout')

@section('javascript')
<script type='text/javascript' src='http://xivdb.com/tooltips.js'></script>
<script type='text/javascript' src='/js/food.js'></script>
@stop

@section('content')

<h1>Food</h1>

<p>
	Food provides a certain percentage of your stat up to the maximum.  The Threshold is what it takes to reach that percentage and maximum.
	For example, if you had 65 Craftsmanship, it would be a waste to use <em>Mashed Popotoes</em> when <em>Mint Lassi</em> is available (and assumedly cheaper).
</p>

<p>All food lasts 30 minutes and provides a 3% XP Bonus.</p>

<?php
	$sections = array(
		'Crafting' => array(
			'CP', 'Control', 'Craftsmanship'
		),
		'Gathering' => array(
			'GP|Gathering', 'GP|Perception', 'Gathering|Perception', 'Perception'
		),
		'Battle' => array(

		)
	);
?>

@foreach($sections as $heading => $section)
<h2>{{ $heading }}</h2>
@foreach($food_groups as $stat_names => $group)
<?php 
	if (in_array($heading, array('Crafting', 'Gathering')) && ! in_array($stat_names, $section))
		continue;

	unset($food_groups[$stat_names]);
?>
<div class='table-responsive' data-stat-names='{{{ $stat_names }}}'>
	<table class='table table-bordered table-striped'>
		<thead>
			<tr>
				<th width='27%'>
					<button class='btn btn-default pull-right glyphicon glyphicon-chevron-up collapse'></button>
				</th>
				@foreach(explode('|', $stat_names) as $stat_name)
				<th colspan='3' class='text-center' width='24%'>
					<img src='/img/stats/{{ $stat_name }}.png' class='stat-icon'>
					{{ $stat_name }}
				</th>
				@endforeach
				@if(3 > count(explode('|', $stat_names)))
				<th colspan='3' width='24%' class='invisible'>&nbsp;</th>
				@endif
				@if(2 > count(explode('|', $stat_names)))
				<th colspan='3' width='24%' class='invisible'>&nbsp;</th>
				@endif
			</tr>
		</thead>
		<tbody class='hidden'>
			<tr>
				<th>Item</th>
				@foreach(explode('|', $stat_names) as $stat_name)
				<th class='text-center' width='8%'>
					Amount
				</th>
				<th class='text-center' width='8%'>
					Maximum
				</th>
				<th class='text-center' width='8%'>
					Threshold
				</th>
				@endforeach
				@if(3 > count(explode('|', $stat_names)))
				<th colspan='3' width='24%' class='invisible'>&nbsp;</th>
				@endif
				@if(2 > count(explode('|', $stat_names)))
				<th colspan='3' width='24%' class='invisible'>&nbsp;</th>
				@endif
			</tr>
			@foreach($group as $item)
			<tr>
				<td>
					<a href='http://xivdb.com/?item/{{ $item['id'] }}' target='_blank'>
						<img src='/img/items/{{ $item['icon'] }}.png'>
						{{ $item['name'] }}
					</a>

					@if($item['buy'])
					<img src='/img/coin.png' class='stat-vendors pull-right' width='24' height='24' data-toggle='popover' data-placement='bottom' data-content-id='#vendors_for_{{ $item['id'] }}' title='Available for {{ $item['buy'] }} gil'>
					
					<div class='hidden' id='vendors_for_{{ $item['id'] }}'>
						@foreach($item['vendors'] as $location_name => $vendors)
						<p>{{ $location_name }}</p>
						<ul>
							@foreach($vendors as $vendor)
							<li>
								<em>{{ $vendor->name }}</em>@if($vendor->title), {{ $vendor->title }}@endif
								@if($vendor->x && $vendor->y)
								<span class='label label-default' rel='tooltip' title='Coordinates' data-container='body'>{{ $vendor->x }}x{{ $vendor->y }}</span>
								@endif
							</li>
							@endforeach
						</ul>
						@endforeach
					</div>
					@endif
				</td>
				@foreach(explode('|', $stat_names) as $stat_name)
				<td class='text-center'>
					{{ number_format($item['stats'][$stat_name]['amount']) }}%
				</td>
				<td class='text-center'>
					{{ number_format($item['stats'][$stat_name]['max']) }}
				</td>
				<td class='text-center'>
					{{ number_format($item['stats'][$stat_name]['threshold']) }}
				</td>
				@endforeach
			</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endforeach
@endforeach

@stop
@extends('wrapper.layout')

@section('javascript')
<script type='text/javascript' src='http://xivdb.com/tooltips.js'></script>
	<script type='text/javascript'>
		var xivdb_tooltips = 
		{ 
			"language"      : "EN",
			"frameShadow"   : true,
			"compact"       : false,
			"statsOnly"     : false,
			"replaceName"   : false,
			"colorName"     : true,
			"showIcon"      : false,
		} 
	</script>
<script type='text/javascript' src='/js/food.js{{ $asset_cache_string }}'></script>
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
					<img src='/img/stats/nq/{{ $stat_name }}.png' class='stat-icon'>
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
			<tr class='show-nq'>
				<td>
					@if($item['vendor_count'])
					<a href='#' class='btn btn-default vendors pull-right' data-item-id='{{ $item['id'] }}' rel='tooltip' title='Available for {{ $item['min_price'] }} gil, Click to load Vendors'>
						<img src='/img/coin.png' width='24' height='24'>
						{{ number_format($item['min_price']) }}
					</a>
					@endif

					<a href='http://xivdb.com/?item/{{ $item['id'] }}' target='_blank'>
						<img src='/img/items/nq/{{ $item['id'] }}.png' width='36' height='36'>
						{{ $item['name'] }}
					</a>
				</td>
				@foreach(explode('|', $stat_names) as $stat_name)
				<td class='text-center nq-only'>
					{{ number_format($item['stats'][$stat_name]['nq']['amount']) }}%
				</td>
				<td class='text-center nq-only'>
					{{ number_format($item['stats'][$stat_name]['nq']['limit']) }}
				</td>
				<td class='text-center nq-only'>
					{{ number_format($item['stats'][$stat_name]['nq']['threshold']) }}
				</td>
				@endforeach
				@foreach(explode('|', $stat_names) as $stat_name)
				<td class='text-center hq-only hidden'>
					{{ number_format($item['stats'][$stat_name]['hq']['amount']) }}%
				</td>
				<td class='text-center hq-only hidden'>
					{{ number_format($item['stats'][$stat_name]['hq']['limit']) }}
				</td>
				<td class='text-center hq-only hidden'>
					{{ number_format($item['stats'][$stat_name]['hq']['threshold']) }}
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
@extends('layout')

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
<script type='text/javascript' src='/js/food.js'></script>
@stop

@section('content')

<h1>Food</h1>

<p>
	Food provides a certain percentage of your stat up to the maximum.  The Threshold is what it takes to reach that percentage and maximum.
	For example, if you had 65 Craftsmanship, it would be a waste to use <em>Mashed Popotoes</em> when <em>Mint Lassi</em> is available (and assumedly cheaper).
</p>

<p>All food lasts 30 minutes and provides a 3% XP Bonus.</p>

@foreach($food_groups as $stat_names => $group)
<div class='table-responsive'>
	<table class='table table-bordered table-striped'>
		<thead>
			<tr>
				<th width='19%'>
					<button class='btn btn-default pull-right glyphicon glyphicon-chevron-up collapse'></button>
				</th>
				@foreach(explode('|', $stat_names) as $stat_name)
				<th colspan='3' class='text-center' width='27%'>
					<img src='/img/stats/{{ $stat_name }}.png' class='stat-icon'>
					{{ $stat_name }}
				</th>
				@endforeach
				@if(3 > count(explode('|', $stat_names)))
				<th colspan='3' width='27%' class='invisible'>&nbsp;</th>
				@endif
				@if(2 > count(explode('|', $stat_names)))
				<th colspan='3' width='27%' class='invisible'>&nbsp;</th>
				@endif
			</tr>
		</thead>
		<tbody class='hidden'>
			<tr>
				<th>&nbsp;</th>
				@foreach(explode('|', $stat_names) as $stat_name)
				<th class='text-center' width='9%'>
					Amount
				</th>
				<th class='text-center' width='9%'>
					Maximum
				</th>
				<th class='text-center' width='9%'>
					Threshold
				</th>
				@endforeach
				@if(3 > count(explode('|', $stat_names)))
				<th colspan='3' width='27%' class='invisible'>&nbsp;</th>
				@endif
				@if(2 > count(explode('|', $stat_names)))
				<th colspan='3' width='27%' class='invisible'>&nbsp;</th>
				@endif
			</tr>
			@foreach($group as $item)
			<tr>
				<td>
					<a href='http://xivdb.com/?item/{{ $item['id'] }}' target='_blank'>
						<img src='/img/items/{{ $item['icon'] }}.png'>
						{{ $item['name'] }}
					</a>
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

@stop
@extends('layout')

@section('javascript')
<script type='text/javascript' src='http://xivdb.com/tooltips.js'></script>
@stop

@section('content')

<h1>Food</h1>

<p>
	Food provides a certain percentage of your stat up to the maximum.  The Threshold is what it takes to reach that percentage and maximum.
	For example, if you had 65 Craftsmanship, it would be a waist to use <em>Mashed Popotoes</em> when <em>Mint Lassi</em> is available (and assumedly cheaper).
</p>

<?php
	$display_groups = array(
		'Disciples of the Hand' => array('Control', 'CP', 'Craftsmanship'), 
		'Disciples of the Land' => array('GP,Perception', 'GP,Gathering', 'Gathering,Perception', 'Perception')
	);
?>

@foreach($display_groups as $job => $group_list)
<h3>{{ $job }}</h3>
<table class='table table-bordered table-striped'>
	<tbody>
		<?php $last = null; ?>
		@foreach($group_list as $group_name)
		@foreach($food_groups[$group_name] as $food_name)
			<?php $food = $food_list[$food_name]; ?>
			@if ($group_name != $last)
				<?php $last = $group_name; ?>
				<tr>
					<th class='invisible'>&nbsp;</th>
					@foreach(range(0, count($food['stats']) - 1) as $place)
					<th colspan='3' class='text-center title'>
						@if (isset($food['stats'][$place]))

							<img src='/img/stats/{{ $food['stats'][$place]['stat'] }}.png' class='stat-icon'>
							{{ $food['stats'][$place]['stat'] }} Bonus

						@endif
					</th>
					@endforeach
				</tr>
				<tr>
					<th class='invisible'>&nbsp;</th>
					@foreach(range(1, count($food['stats'])) as $ignore)
					<th class='text-center'>
						Percent
					</th>
					<th class='text-center'>
						Maximum
					</th>
					<th class='text-center'>
						Threshold
					</th>
					@endforeach
				</tr>
			@endif
			<tr>
				<th class='text-right'>
					<a href='http://xivdb.com/{{ $food['href'] }}' target='_blank'>{{ $food_name }}</a>
				</th>
				@foreach(range(0, count($food['stats']) - 1) as $place)
				@if( ! isset($food['stats'][$place]))
				<td class='text-center no-value'>-</td>
				<td class='text-center no-value'>-</td>
				<td class='text-center no-value'>-</td>
				@else
				<td class='text-center'>
					{{ $food['stats'][$place]['amount'] }}%
				</td>
				<td class='text-center'>
					{{ $food['stats'][$place]['max'] }}
				</td>
				<td class='text-center'>
					{{ round($food['stats'][$place]['max'] / ($food['stats'][$place]['amount'] / 100)) }}
				</td>
				@endif
				@endforeach
			</tr>
		@endforeach
		@endforeach
	</tbody>
</table>
@endforeach

@stop
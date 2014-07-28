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
<script type='text/javascript' src='{{ cdn('/js/materia.js') }}'></script>
@stop

@section('banner')
	<h1>Materia</h1>
@stop

@section('content')

<div class='table-responsive'>
	<table class='table table-bordered table-striped'>
		<thead>
			<tr>
				<th class='text-right' width='25%'>Materia Name</th>
				<th>Stat</th>
				<th colspan='4' class='text-center bold'>
					Materia Power
				</th>
			</tr>
		</thead>
		<tbody>
			@foreach(array('Crafting' => 'Craftsman', 'Gathering' => 'Gatherer', 'Battle' => '') as $section_name => $section)
			<tr>
				<th colspan='2' class='text-center bold'>
					{{ $section_name }} Materia
				</th>
				@foreach(array('I', 'II', 'III', 'IV') as $power)
				<th class='text-center' width='10%'>{{ $power }}</th>
				@endforeach
			</tr>
			@foreach($materia_list as $name => $materia)
			<?php 
				if ( ! preg_match('/' . $section . '/', $name))
					continue;
				unset($materia_list[$name]);
			?>
			<tr>
				<td class='text-right valign'>{{ $name }} Materia</td>
				<td class='valign'>
					<img src='/img/stats/nq/{{ $materia['stat'] }}.png' class='stat-icon'>
					{{ $materia['stat'] }}
				</td>
				@foreach(array('I', 'II', 'III', 'IV') as $power)
				<?php if ( ! isset($materia['power'][$power])) continue; ?>
				<td class='valign text-center materia-value'>
					<a href='http://xivdb.com/?item/{{ $materia['power'][$power]['id'] }}' target='_blank'>
						{{ number_format($materia['power'][$power]['amount']) }}
						<img src='/img/items/nq/{{ $materia['power'][$power]['id'] }}.png'>
					</a>
				</td>
				@endforeach
			</tr>
			@endforeach
			@endforeach
		</tbody>
	</table>
</div>

@stop
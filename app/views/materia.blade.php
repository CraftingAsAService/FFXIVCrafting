@extends('layout')

@section('javascript')
<script type='text/javascript' src='http://xivdb.com/tooltips.js'></script>
@stop

@section('content')

<h1>Materia</h1>

<?php
	$display_groups = array(
		'Disciples of the Hand' => array('Control', 'CP', 'Craftsmanship'), 
		'Disciples of the Land' => array('Gathering', 'GP', 'Perception')
	);
?>

<div class='row'>
	@foreach($display_groups as $job => $stats)
	<div class='col-sm-6 text-center'>
		<h3>{{ $job }}</h3>

		@foreach($stats as $stat)
		<div class='table-responsive'>
			<table class='table table-bordered table-striped'>
				<tbody>
					<tr>
						<th class='invisible'>&nbsp;</th>
						<th class='text-center fixed-width-50 title'>
							<img src='/img/stats/{{ $stat }}.png' class='stat-icon'>
							{{ $stat }} Bonus
						</th>
					</tr>
					@foreach($materia_list[$stat] as $materia)
					<tr>
						<th class='text-right'><a href='http://xivdb.com/{{ $materia['href'] }}' target='_blank'>{{ $materia['name'] }}</a></th>
						<td class='text-center'>+{{ $materia['amount'] }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		@endforeach
	</div>
	@endforeach
</div>

@stop
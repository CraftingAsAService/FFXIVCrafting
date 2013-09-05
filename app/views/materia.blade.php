@extends('layout')

@section('content')

<h1>Materia</h1>

<div class='row'>
	@foreach(array('DOH' => array('Craftsmanship', 'Control', 'CP'), 'DOL' => array('Gathering', 'Perception', 'GP')) as $job => $stats)
	<?php $job = Job::where('abbreviation', $job)->first(); ?>
	<div class='col-sm-6 text-center'>
		<h3>{{ $job->name }}</h3>

		<table class='table table-bordered'>
			<tbody>
				@foreach($stats as $stat)
				<?php $stat = Stat::where('name', $stat)->first(); ?>
				<tr>
					<th> </th>
					<th class='text-center'>
						<img src='/img/stats/{{ $stat->name }}.png' class='stat-icon'>
						{{ $stat->name }} Bonus
					</th>
				</tr>
				@foreach(Materia::where('job_id', $job->id)->where('stat_id', $stat->id)->get() as $materia)
				<tr>
					<td class='text-right'>{{ $materia->name }}</td>
					<td class='text-center'>+{{ $materia->amount }}</td>
				</tr>
				@endforeach
				@endforeach
			</tbody>
		</table>
	</div>
	@endforeach
</div>
	
@stop
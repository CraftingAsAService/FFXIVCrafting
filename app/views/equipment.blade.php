@extends('layout')

@section('content')

<h1>Gear for a level {{ $level }} {{ $job->name }}</h1>
<button class='btn btn-info toggle-origin pull-right' style='margin: 0 10px 10px 0;'>Toggle Origin</button>
<!--
<button class='btn btn-info toggle-changes pull-right' style='margin: 0 10px 10px 0;'>Toggle Changes Only</button>
<button class='btn btn-info toggle-range pull-right' style='margin: 0 10px 10px 0;'>Toggle Range</button>
-->
@if($job->name != $disciple->name)
<h2>{{ $disciple->name }}</h2>
@endif

<table class='table table-bordered'>
	<thead>
		<tr>
			<th>
				
			</th>
			@foreach(array_keys($equipment) as $th_level)
			<th class='text-center alert alert-{{ $th_level == $level ? 'success' : ($th_level < $level ? 'info' : 'warning') }}'>
				Level
				{{ $th_level }}
			</th>
			@endforeach
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>
				
			</th>
			@foreach(array_keys($equipment) as $tf_level)
			<th>
				@foreach($stats[$tf_level] as $stat => $value)
				<div class='row'>
					<div class='col-sm-2 text-center'>
						<img src='/img/stats/{{ $stat }}.png' class='stat-icon' rel='tooltip' title='{{ $stat }}'>
					</div>
					<div class='col-sm-10'>
						{{ $stat }}: {{ $value }} 
						@if(isset($stats_diff[$tf_level][$stat]))
						({{ $stats_diff[$tf_level][$stat] > 0 ? '+' : '' }}{{ $stats_diff[$tf_level][$stat] }})
						@endif
					</div>
				</div>
				@endforeach
			</th>
			@endforeach
		</tr>
	</tfoot>
	<tbody>
		@foreach(EquipmentType::orderBy('rank')->get() as $type)
		<tr>
			<td class='text-center'>
				<img src='/img/equipment/{{ $type->name }}.png' class='equipment-icon' rel='tooltip' title='{{ $type->name }}'>
				<div>
					<strong>{{ $type->name }}</strong>
				</div>
			</td>
			@foreach(array_keys($equipment) as $td_level)
			<?php $new = isset($changes[$td_level][$type->name]); ?>
			<td class='{{ $new ? ('alert alert-' . ($td_level == $level ? 'success' : ($td_level < $level ? 'info' : 'warning'))) : '' }}'>
			@foreach($equipment[$td_level][$type->name] as $item)
				<div class='clearfix'>
					@if(strlen($item->origin) == 3)
					<img src='/img/classes/{{ $item->origin == 'n/a' ? 'NA' : $item->origin }}.png' class='stat-origin pull-right {{ $new ? '' : 'hidden not-new' }}' rel='tooltip' title='{{ $item->origin == 'n/a' ? 'Guildmaster / Quartermaster' : $job_list[$item->origin] }}'>
					@endif
					<div>
						<strong>{{ $item->level }}</strong>
						{{ $item->name }} 
					</div>
					@if($new)
						@if(strlen($item->origin) != 3)
						<div class='origin panel'>
							<strong>Origin</strong> 
							{{ $item->origin }}
						</div>
						@endif
						<div class='stats'>
							@foreach($changes[$td_level][$type->name] as $stat => $change)
							<?php if ($change == 0) continue; ?>
							<div class='text-center panel pull-left stat-box'>
								<img src='/img/stats/{{ $stat }}.png' class='stat-icon' rel='tooltip' title='{{ $stat }}'><br>
								{{ $change > 0 ? '+' : '' }}{{ $change }}
							</div>
							@endforeach
						</div>
					@endif
				</div>
			@endforeach
			</td>
			@endforeach
		</tr>
		@endforeach
	</tbody>
</table>

<div class='text-center'>
	<ul class='pagination pagination'>
		@foreach($job_list as $abbr => $rjob)
		<li class='{{ $rjob == $job->name ? 'active' : '' }} '>
			<a href='/equipment/{{ strtolower($abbr) }}/{{ $level }}/{{ $range }}' rel='tooltip' title='{{ $rjob }}'>
				@if( ! in_array($abbr, array('DOH', 'DOL')))
				<img src='/img/classes/{{ $abbr }}.png'>
				@else
				<img src='/img/classes/NA.png'> {{ $abbr }}
				@endif
			</a>
		</li>
		@endforeach
	</ul>
</div>

<div class='text-center'>
	<ul class='pagination pagination'>
		@foreach(range($level - 5, $level + 5) as $rlevel)
		<?php if ($rlevel < 1 || $rlevel > 50) continue; ?>
		<li class='{{ $rlevel == $level ? 'active' : '' }} '>
			<a href='/equipment/{{ strtolower($job->abbreviation) }}/{{ $rlevel }}/{{ $range }}'>{{ $rlevel }}</a>
		</li>
		@endforeach
	</ul>
</div>

@stop
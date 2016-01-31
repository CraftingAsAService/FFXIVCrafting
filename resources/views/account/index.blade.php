@extends('app')

@section('banner')
	<h1>
		<img src='{{ $account['avatar'] }}' width='64' height='64' class='border-radius'>
		{{ $character_name }} <small>of</small> {{ $server }}
	</h1>
@stop

@section('content')

	@foreach(array('crafting' => 'primary', 'gathering' => 'info', 'melee' => 'danger', 'magic' => 'warning') as $section => $color)
	<h2>{{ ucfirst($section) }} Levels</h2>

	<div class='well'>
		<div class='row'>
			@foreach(${$section . '_job_list'} as $job)
			<div class='col-sm-4 col-md-3'>
				<span class='pull-right label label-{{ $color }}'>Level {{ $account['levels'][strtolower($job->name)] }}</span>
				<img src='/img/jobs/{{ strtoupper($job->abbr) }}.png' width='24' height='24' rel='tooltip' title='{{ $job->name }}'>
				{{ $job->name }}
				<div class='progress margin-top'>
					<div class='progress-bar progress-bar-{{ $color }} progress-bar-striped' style='width: {{ (int) (($account['levels'][strtolower($job->name)] / config('site.max_level')) * 100) }}%;'></div>
				</div>
			</div>
			@endforeach
		</div>
	</div>
	@endforeach

	<div class='text-right'>
		<a href='/account/refresh' class='btn btn-primary margin-right'>Refresh Data</a>
		<a href='/account/logout' class='btn btn-danger'>Detach Character</a>
	</div>

@stop
@extends('app')

@section('meta')
	<meta name="robots" content="noindex,nofollow">
@stop

@section('vendor-css')
	<link href='{{ cdn('/css/bootstrap-switch.css') }}' rel='stylesheet'>
	<link href='{{ cdn('/css/bootstrap-tour.css') }}' rel='stylesheet'>
@stop

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/bootstrap-switch.js') }}'></script>
	<script type='text/javascript' src='{{ cdn('/js/bootstrap-tour.min.js') }}'></script>
	<script type='text/javascript'>
		var level = {{ $level }};
		var job = '{{ $job->abbr }}';
		var craftable_only = Boolean({{ $craftable_only }});
		var rewardable_too = Boolean({{ $rewardable_too }});
		var max_level = {{ config('site.max_level') }};
	</script>
	<script type='text/javascript' src='{{ cdn('/js/equipment.js') }}'></script>
@stop

@section('banner')

	<a href='#' id='start_tour' class='start btn btn-primary pull-right' style='margin-top: 12px;'>
		<i class='glyphicon glyphicon-play'></i>
		Start Tour
	</a>

	<h1>
		<img src='/img/jobs/{{ strtoupper($job->abbr) }}.png' width='32' height='32' style='position: relative; top: -4px;'>
		{{ $job->name }}
	</h1>
	<h2>Equipment Guide</h2>
@stop

@section('content')

<table class='table' id='gear-main'>
	<tbody>
		<tr>
			<th class='previous-gear'>
				&laquo;
			</th>
			<td class='table-holder'>
				<table class='table table-bordered{{ $slim_mode ? ' slim' : '' }}' id='gear'>
					<thead>
						<tr>
							@foreach($equipment['head'] as $th)
							{!! $th !!}
							@endforeach
						</tr>
					</thead>
					<tfoot>
						<tr>
							@foreach($equipment['foot'] as $th)
							{!! $th !!}
							@endforeach
						</tr>
					</tfoot>
					<tbody>
						@foreach($equipment['gear'] as $role => $levels)
						<tr class='role-row' data-role='{{ $role }}'>
							@foreach($levels as $level => $td)
							{!! $td !!}
							@endforeach
						</tr>
						@endforeach
					</tbody>
				</table>
			</td>
			<th class='next-gear'>
				&raquo;
			</th>
		</tr>
	</tbody>
</table>


<a name='table-options' id='table-options'></a>

<div class='panel panel-primary'>
	<div class='panel-heading'>
		<h3 class='panel-title'>Table Options</h3>
	</div>
	<div class='panel-body'>
		<div class='row'>
			<div class='col-sm-6'>
				<label>Slim Mode</label>
				<input type='checkbox' id='toggle-slim' {{ $slim_mode ? ' checked="checked"' : '' }} class='bootswitch' data-on-text='YES' data-off-text='NO'>
			</div>
			<div class='col-sm-6'>
				<label>Boring Stats</label>
				<input type='checkbox' id='toggle-all-stats' class='bootswitch' data-on-text='YES' data-off-text='NO'>
			</div>
		</div>
	</div>
</div>

<div class='panel panel-primary'>
	<div class='panel-heading'>
		<h3 class='panel-title'>Page-Reloading Options</h3>
	</div>
	<div class='panel-body'>
		<div class='row'>
			<div class='col-sm-4'>
				<div class='btn-group dropup'>
					<button type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown'>
					Change Class <span class='caret'></span>
					</button>
					<ul class='dropdown-menu class-dropdown-menu' role='menu'>
						@foreach(array('GLA', 'PGL', 'MRD', 'LNC', 'ARC', 'ROG') as $switch_job)
						<li><a href='/equipment/list?{{ $switch_job }}:{{ $original_level }}:{{ $craftable_only ? 1 : 0 }}:{{ $slim_mode ? 1 : 0 }}:{{ $rewardable_too ? 1 : 0 }}' class='btn btn-danger'>
							<img src='/img/jobs/{{ $switch_job }}-inactive.png' width='24' height='24'>
						</a></li>
						@endforeach
						@foreach(array('CNJ', 'THM', 'ACN') as $switch_job)
						<li><a href='/equipment/list?{{ $switch_job }}:{{ $original_level }}:{{ $craftable_only ? 1 : 0 }}:{{ $slim_mode ? 1 : 0 }}:{{ $rewardable_too ? 1 : 0 }}' class='btn btn-warning'>
							<img src='/img/jobs/{{ $switch_job }}-inactive.png' width='24' height='24'>
						</a></li>
						@endforeach
						@foreach(array('CRP','BSM','ARM','GSM','LTW','WVR','ALC','CUL') as $switch_job)
						<li><a href='/equipment/list?{{ $switch_job }}:{{ $original_level }}:{{ $craftable_only ? 1 : 0 }}:{{ $slim_mode ? 1 : 0 }}:{{ $rewardable_too ? 1 : 0 }}' class='btn btn-primary'>
							<img src='/img/jobs/{{ $switch_job }}-inactive.png' width='24' height='24'>
						</a></li>
						@endforeach
						@foreach(array('MIN','BTN','FSH') as $switch_job)
						<li><a href='/equipment/list?{{ $switch_job }}:{{ $original_level }}:{{ $craftable_only ? 1 : 0 }}:{{ $slim_mode ? 1 : 0 }}:{{ $rewardable_too ? 1 : 0 }}' class='btn btn-info'>
							<img src='/img/jobs/{{ $switch_job }}-inactive.png' width='24' height='24'>
						</a></li>
						@endforeach
					</ul>
				</div>
			</div>
			<div class='col-sm-4'>
				<div class='btn-group dropup'>
					<button type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown'>
					Change Level <span class='caret'></span>
					</button>
					<ul class='dropdown-menu level-dropdown-menu' role='menu'>
						<li><a href='/equipment/list?{{ $job->abbr }}:1:{{ $craftable_only ? 1 : 0 }}:{{ $slim_mode ? 1 : 0 }}:{{ $rewardable_too ? 1 : 0 }}' class='btn btn-success'>
							1
						</a></li>
						@foreach(range(5,config('site.max_level'),5) as $switch_level)
						<li><a href='/equipment/list?{{ $job->abbr }}:{{ $switch_level }}:{{ $craftable_only ? 1 : 0 }}:{{ $slim_mode ? 1 : 0 }}:{{ $rewardable_too ? 1 : 0 }}' class='btn btn-success'>
							{{ $switch_level }}
						</a></li>
						@endforeach
					</ul>
				</div>
			</div>
			<div class='col-sm-4'>
				{!! Form::open(['url' => '/equipment', 'class' => 'form-horizontal']) !!}
					<input type='hidden' name='class' value='{{ $job->abbr }}'>
					<input type='hidden' name='level' value='{{ $original_level }}'>
					<input type='hidden' name='slim_mode' value='{{ $slim_mode ? 1 : 0 }}'>
					<input type='hidden' name='rewardable_too' value='{{ $rewardable_too }}'>

					Only Craftable
					<input type='checkbox' id='craftable_only_switch' name='craftable_only' value='1' {{ $craftable_only ? ' checked="checked"' : '' }} class='bootswitch' data-on-text='YES' data-off-text='NO'>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

<div class='well'>
	Remember, this tool is geared towards finding craftable equipment!  If you're looking for information on how to gear your level {{ config('site.max_level') }}, please visit the <a href='http://ffxiv.ariyala.com/' target='_blank'>FFXIV Gear Calculator</a>!
</div>

@if($maxLevelWarning)
<div class='alert alert-warning'>
	There are too many results for level {{ config('site.max_level') }} DOW/DOM classes.  It was excluded on initial load.  Feel free to hit that right arrow though.
</div>
@endif

@stop

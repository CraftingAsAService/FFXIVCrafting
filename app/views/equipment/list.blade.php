@extends('layout')

@section('vendor-css')
	<link href='/css/bootstrap-switch.css' rel='stylesheet'>
	<link href='/css/bootstrap-tour.min.css' rel='stylesheet'>
@stop

@section('javascript')
	<script type='text/javascript' src='http://xivdb.com/tooltips.js'></script>
	<script type='text/javascript' src='/js/bootstrap-switch.js'></script>
	<script type='text/javascript' src='/js/bootstrap-tour.min.js'></script>
	<script type='text/javascript'>
		var level = {{ $level }};
		var job = '{{ $job->abbreviation }}';
		var craftable_only = Boolean({{ $craftable_only }});
	</script>
	<script type='text/javascript' src='/js/equipment.js'></script>
@stop

@section('content')

<a href='#' id='start_tour' class='start btn btn-primary pull-right' style='margin-top: 12px;'>
	<i class='glyphicon glyphicon-play'></i>
	Start Tour
</a>

<h1>
	<span class='job-border {{ $job->disciple }}'>
		<img src='/img/classes/{{ $job->abbreviation }}.png' class='stat-crafted_by' width='24' height='24'>
	</span>
	{{ $job->name }}
</h1>

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
							@foreach($starting_equipment as $equipment)
							{{ $equipment['head'] }}
							@endforeach
						</tr>
					</thead>
					<tfoot>
						<tr>
							@foreach($starting_equipment as $equipment)
							{{ $equipment['foot'] }}
							@endforeach
						</tr>
					</tfoot>
					<tbody>
						@foreach($roles as $role)
						<tr class='role-row' data-role='{{ $role }}'>
							@foreach($starting_equipment as $equipment)
							{{ $equipment['roles'][$role] }}
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
				<div class='make-switch' id='toggle-slim' data-on='success' data-off='warning'>
					<input type='checkbox'{{ $slim_mode ? ' checked="checked"' : '' }}>
				</div>
			</div>
			<div class='col-sm-6'>
				<label>Boring Stats</label>
				<div class='make-switch' id='toggle-all-stats' data-on='success' data-off='warning'>
					<input type='checkbox'>
				</div>
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
						@foreach(array('GLA', 'PGL', 'MRD', 'LNC', 'ARC') as $switch_job)
						<li><a href='/equipment/list?{{ $switch_job }}:{{ $level }}:{{ $craftable_only ? 1 : 0 }}:{{ $slim_mode ? 1 : 0 }}' class='btn btn-danger'>
							<img src='/img/classes/{{ $switch_job }}.png' rel='tooltip' title='{{ $job_list[$switch_job] }}'>
						</a></li>
						@endforeach
						@foreach(array('CNJ', 'THM', 'ACN') as $switch_job)
						<li><a href='/equipment/list?{{ $switch_job }}:{{ $level }}:{{ $craftable_only ? 1 : 0 }}:{{ $slim_mode ? 1 : 0 }}' class='btn btn-warning'>
							<img src='/img/classes/{{ $switch_job }}.png' rel='tooltip' title='{{ $job_list[$switch_job] }}'>
						</a></li>
						@endforeach
						@foreach(array('CRP','BSM','ARM','GSM','LTW','WVR','ALC','CUL') as $switch_job)
						<li><a href='/equipment/list?{{ $switch_job }}:{{ $level }}:{{ $craftable_only ? 1 : 0 }}:{{ $slim_mode ? 1 : 0 }}' class='btn btn-primary'>
							<img src='/img/classes/{{ $switch_job }}.png' rel='tooltip' title='{{ $job_list[$switch_job] }}'>
						</a></li>
						@endforeach
						@foreach(array('MIN','BTN','FSH') as $switch_job)
						<li><a href='/equipment/list?{{ $switch_job }}:{{ $level }}:{{ $craftable_only ? 1 : 0 }}:{{ $slim_mode ? 1 : 0 }}' class='btn btn-info'>
							<img src='/img/classes/{{ $switch_job }}.png' rel='tooltip' title='{{ $job_list[$switch_job] }}'>
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
						<li><a href='/equipment/list?{{ $job->abbreviation }}:1:{{ $craftable_only ? 1 : 0 }}:{{ $slim_mode ? 1 : 0 }}' class='btn btn-success'>
							1
						</a></li>
						@foreach(range(5,50,5) as $switch_level)
						<li><a href='/equipment/list?{{ $job->abbreviation }}:{{ $switch_level }}:{{ $craftable_only ? 1 : 0 }}:{{ $slim_mode ? 1 : 0 }}' class='btn btn-success'>
							{{ $switch_level }}
						</a></li>
						@endforeach
					</ul>
				</div>
			</div>
			<div class='col-sm-4'>
				<form action='/equipment' method='post' role='form' class='form-horizontal'>
					<input type='hidden' name='class' value='{{ $job->abbreviation }}'>
					<input type='hidden' name='level' value='{{ $level }}'>
					<input type='hidden' name='slim' value='{{ $slim_mode }}'>
					<label>
						Only Craftable
					</label>
					<div class='make-switch' data-on='success' data-off='warning'>
						<input type='checkbox' id='craftable_only_switch' name='craftable_only' value='1' {{ $craftable_only ? ' checked="checked"' : '' }}>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@stop
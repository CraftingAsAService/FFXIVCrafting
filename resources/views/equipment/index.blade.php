@extends('app')

@section('meta')
	<meta name="robots" content="nofollow">
@stop

@section('vendor-css')
	<link href='//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' rel='stylesheet'>
	<link href='{{ cdn('/css/bootstrap-switch.css') }}' rel='stylesheet'>
@stop

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/home.js') }}'></script>
	<script type='text/javascript' src='{{ cdn('/js/bootstrap-switch.js') }}'></script>
	<script type='text/javascript'>
		$(function() {
			$('.bootswitch').bootstrapSwitch();

			// On clicking a class icon, fill in the level
			$('.class-selector').click(function() {
				$('input[name=level]').val($(this).data('level'));
				return;
			});

			$('.class-selector.active').trigger('click');
		});
	</script>
@stop

@section('banner')
	<h1>Equipment Calculator</h1>
	<h2>Display the gear available for a class at a certain level.</h2>
@stop

@section('content')

	@if(isset($error) && $error)
	<div class='alert alert-danger'>
		The job you selected is unrecognized.  Try again.
	</div>
	@endif

	{!! Form::open(['url' => '/equipment', 'class' => 'form-horizontal']) !!}
		<fieldset>
			<legend>Select your Class</legend>
			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Disciples of the Hand &amp; Land</label>
				<div class='col-sm-8 col-md-9'>
					<div class='btn-group' data-toggle='buttons'>
						@foreach($crafting_job_list as $job)
						<label class='btn btn-primary class-selector{{ $job->id == reset($job_ids['crafting']) ? ' active' : '' }}' data-level='{{ $account ? $account['levels'][strtolower($job->name)] : 1 }}'>
							<input type='radio' name='class' value='{{ $job->abbr }}' {{ $job->id == reset($job_ids['crafting']) ? ' checked="checked"' : '' }}> 
							<img src='/img/jobs/{{ strtoupper($job->abbr) }}-inactive.png' width='24' height='24' rel='tooltip' title='{{ $job->abbr }}'>
						</label>
						@endforeach
					</div>
					<div class='btn-group' data-toggle='buttons'>
						@foreach($gathering_job_list as $job)
						<label class='btn btn-info class-selector' data-level='{{ $account ? $account['levels'][strtolower($job->name)] : 1 }}'>
							<input type='radio' name='class' value='{{ $job->abbr }}'> 
							<img src='/img/jobs/{{ strtoupper($job->abbr) }}-inactive.png' width='24' height='24' rel='tooltip' title='{{ $job->abbr }}'>
						</label>
						@endforeach
					</div>
				</div>
			</div>

			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Disciples of War &amp; Magic</label>
				<div class='col-sm-8 col-md-9'>
					<div class='btn-group' data-toggle='buttons'>
						@foreach($basic_melee_job_list as $job)
						<label class='btn btn-danger class-selector' data-level='{{ $account ? $account['levels'][strtolower($job->name)] : 1 }}'>
							<input type='radio' name='class' value='{{ $job->abbr }}'> 
							<img src='/img/jobs/{{ strtoupper($job->abbr) }}-inactive.png' width='24' height='24' rel='tooltip' title='{{ $job->abbr }}'>
						</label>
						@endforeach
					</div>
					<div class='btn-group' data-toggle='buttons'>
						@foreach($basic_magic_job_list as $job)
						<label class='btn btn-warning class-selector' data-level='{{ $account ? $account['levels'][strtolower($job->name)] : 1 }}'>
							<input type='radio' name='class' value='{{ $job->abbr }}'> 
							<img src='/img/jobs/{{ strtoupper($job->abbr) }}-inactive.png' width='24' height='24' rel='tooltip' title='{{ $job->abbr }}'>
						</label>
						@endforeach
					</div>
				</div>
			</div>
		</fieldset>
		<fieldset>
			<legend>Options</legend>
			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Level</label>
				<div class='col-sm-4 col-md-3'>
					<input type='number' name='level' value='5' placeholder='Level (e.g. 5)' class='form-control' required='required'>
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Craftable Only</label>
				<div class='col-sm-8 col-md-9'>
					<input type='checkbox' name='craftable_only' value='1' checked='checked' class='bootswitch' data-on-text='YES' data-off-text='NO'>
					<small><em> - Only show craftable items
					<input type='checkbox' name='rewardable_too' value='1'>
					and <u rel='tooltip' title='Items that are potential rewards from leves, achievements, quests, etc'>rewardable items</u>
					</em></small>
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Slim Mode</label>
				<div class='col-sm-8 col-md-9'>
					<input type='checkbox' name='slim_mode' value='1' checked='checked' class='bootswitch' data-on-text='YES' data-off-text='NO'>
					<small><em> - Show a condensed version</em></small>
				</div>
			</div>
		</fieldset>
		<fieldset>
			<div class='form-group'>
				<div class='col-sm-3 col-sm-offset-4 col-md-offset-3'>
					<button type='submit' class='btn btn-success btn-block btn-lg'>Get my Gear!</button>
				</div>
				@if($previous)
				<div class='col-sm-1 text-center' style='line-height: 3;'>
					or
				</div>
				<div class='col-sm-4 col-md-3'>
					<a href='{{ $previous }}' class='btn btn-warning btn-block btn-lg'>Load Last Setup</a>
				</div>
				@endif
			</div>
		</fieldset>
	{!! Form::close() !!}
		
	<hr>
	<div class='alert alert-info'>
		You can still use the <a href='/gear'>Gear Calculator</a>!  Have an opinion on the matter?  <a href='http://goo.gl/forms/ZttFqMd9CD' target='_blank'>Fill out the survey!</a>
		<div>
			<strong>Vote Again</strong> now that the tool's fixed!   I've cleared all of the old votes.  The commonly reported bugs have been fixed, promise!
		</div>
	</div>

@stop
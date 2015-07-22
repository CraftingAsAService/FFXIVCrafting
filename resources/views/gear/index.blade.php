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
			$('.bootswitch').bootstrapSwitch({
				onSwitchChange:function() {
					return $('#gear-form').trigger('build_url');
				}
			});

			// On clicking a class icon, fill in the level
			$('.class-selector').click(function() {
				var el = $(this),
					img = el.find('img');

				$('input[name=level]').val(el.data('level'));
				
				var active_img = $('.class-selector img.active');
				if (active_img.length) {
					active_img.removeClass('active')
					active_img.attr('src', active_img.data('originalSrc'));
				}

				img.addClass('active');
				img.data('originalSrc', img.attr('src'));
				img.attr('src', img.data('activeSrc'));

				$('#gear-form').trigger('build_url');

				return;
			});

			$('#gear-form').on('build_url', function() {
				var el = $(this),
					url = el.data('sourceUrl'),
					// cannot rely on .class-selector.active, it's delayed.
					job = $('.class-selector img.active').closest('label').data('job'),
					level = $('#level').val()
					options = '';

				$('#gear-form input:checkbox:checked').each(function() {
					// console.log($(this));
					options += $(this).attr('name') + ',';
					return;
				});

				// Take the last comma off
				if (options.length > 0)
					options = options.substring(0, options.length - 1);

				el.attr('action', url.replace(/JOB/, job).replace(/LEVEL/, level) + options);

				return;
			});

			$('#gear-form').submit(function(event) {
				event.preventDefault();
				$('#gear-form').trigger('build_url');
				window.location = $(this).attr('action');
				return false;
			});

			$('#level').change(function() {
				$('#gear-form').trigger('build_url');
			});

			if ($('.class-selector.select-me').length == 1)
				$('.class-selector.select-me').first().trigger('click');
			else
				$('.class-selector').first().trigger('click');

			return;
		});
	</script>
@stop

@section('banner')
	<h1>Gear Calculator</h1>
	<h2>Display the gear available for a class at a certain level.</h2>
@stop

@section('content')

	<div class='alert alert-info'>
		The commonly reported bugs have been fixed, promise!
	</div>

	{!! Form::open(['url' => '#', 'method' => 'get', 'id' => 'gear-form', 'class' => 'form-horizontal', 'data-source-url' => '/gear/profile/JOB/LEVEL?options=']) !!}
		<fieldset>
			<legend>Select your Class</legend>
			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Disciples of the Hand &amp; Land</label>
				<div class='col-sm-8 col-md-9'>
					<div class='btn-group' data-toggle='buttons'>
						@foreach($crafting_job_list as $job)
						<?php $this_job = isset($defaults['job']) && $defaults['job'] == $job->abbr; ?>
						<?php $default_level = $this_job && isset($defaults['level']) ? $defaults['level'] : ($account ? $account['levels'][strtolower($job->name)] : 1); ?>
						<label class='btn btn-primary class-selector{{ $this_job ? ' select-me' : '' }}' data-job='{{ $job->abbr }}' data-level='{{ $default_level }}'>
							<img src='/img/jobs/{{ $job->abbr }}-inactive.png' data-active-src='/img/jobs/{{ $job->abbr }}-active.png' width='24' height='24' rel='tooltip' title='{{ $job->name }}'>
						</label>
						@endforeach
					</div>
					<div class='btn-group' data-toggle='buttons'>
						@foreach($gathering_job_list as $job)
						<?php $this_job = isset($defaults['job']) && $defaults['job'] == $job->abbr; ?>
						<?php $default_level = $this_job && isset($defaults['level']) ? $defaults['level'] : ($account ? $account['levels'][strtolower($job->name)] : 1); ?>
						<label class='btn btn-info class-selector{{ $this_job ? ' select-me' : '' }}' data-job='{{ $job->abbr }}' data-level='{{ $default_level }}'>
							<img src='/img/jobs/{{ $job->abbr }}-inactive.png' data-active-src='/img/jobs/{{ $job->abbr }}-active.png' width='24' height='24' rel='tooltip' title='{{ $job->name }}'>
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
						<?php $this_job = isset($defaults['job']) && $defaults['job'] == $job->abbr; ?>
						<?php $default_level = $this_job && isset($defaults['level']) ? $defaults['level'] : ($account ? $account['levels'][strtolower($job->name)] : 1); ?>
						<label class='btn btn-danger class-selector{{ $this_job ? ' select-me' : '' }}' data-job='{{ $job->abbr }}' data-level='{{ $default_level }}'>
							<img src='/img/jobs/{{ $job->abbr }}-inactive.png' data-active-src='/img/jobs/{{ $job->abbr }}-active.png' width='24' height='24' rel='tooltip' title='{{ $job->name }}'>
						</label>
						@endforeach
					</div>
					<div class='btn-group' data-toggle='buttons'>
						@foreach($basic_magic_job_list as $job)
						<?php $this_job = isset($defaults['job']) && $defaults['job'] == $job->abbr; ?>
						<?php $default_level = $this_job && isset($defaults['level']) ? $defaults['level'] : ($account ? $account['levels'][strtolower($job->name)] : 1); ?>
						<label class='btn btn-warning class-selector{{ $this_job ? ' select-me' : '' }}' data-job='{{ $job->abbr }}' data-level='{{ $default_level }}'>
							<img src='/img/jobs/{{ $job->abbr }}-inactive.png' data-active-src='/img/jobs/{{ $job->abbr }}-active.png' width='24' height='24' rel='tooltip' title='{{ $job->name }}'>
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
					<input type='number' id='level' name='level' value='{{ isset($defaults['level']) ? $defaults['level'] : 5 }}' placeholder='Level (e.g. 5)' class='form-control'>
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Include High Quality</label>
				<div class='col-sm-8 col-md-9'>
					<input type='checkbox' name='hq' value='1' @if((isset($defaults['options']) && in_array('hq', $defaults['options'])) || ! isset($defaults['options']))checked='checked'@endif class='bootswitch' data-on-text='YES' data-off-text='NO'>
					<small><em> - Include High Quality craftables</em></small>
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Craftable Only</label>
				<div class='col-sm-8 col-md-9'>
					<input type='checkbox' name='craftable' value='1' @if((isset($defaults['options']) && in_array('craftable', $defaults['options'])) || ! isset($defaults['options']))checked='checked'@endif class='bootswitch' data-on-text='YES' data-off-text='NO'>
					<small><em> - Only show craftable items</em></small>
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Include Rewards</label>
				<div class='col-sm-8 col-md-9'>
					<input type='checkbox' name='rewardable' value='1' @if((isset($defaults['options']) && in_array('rewardable', $defaults['options'])) || ! isset($defaults['options']))checked='checked'@endif class='bootswitch' data-on-text='YES' data-off-text='NO'>
					<small><em> - Also include rewardable items</em></small>
				</div>
			</div>
		</fieldset>
		<fieldset>
			<div class='form-group'>
				<div class='col-sm-3 col-sm-offset-4 col-md-offset-3'>
					<button type='submit' class='btn btn-success btn-block btn-lg'>View Gear Profile</button>
				</div>
			</div>
		</fieldset>
	{!! Form::close() !!}
		
	<hr>
	<div class='alert alert-info'>
		You can still use the old <a href='/equipment'>Equipment Calculator</a>!  Have an opinion on the matter?  <a href='http://goo.gl/forms/ZttFqMd9CD' target='_blank'>Fill out the survey!</a>
	</div>

@stop
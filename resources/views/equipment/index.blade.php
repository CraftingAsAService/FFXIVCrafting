@extends('app')

@section('meta')
	<meta name="robots" content="nofollow">
@endsection

@section('vendor-css')
	<link href='//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' rel='stylesheet'>
	<link href='{{ cdn('/css/bootstrap-switch.css') }}' rel='stylesheet'>
	<style>
		.class-selector input {
			display: none;
		}
		/*.class-selector input:checked ~ .abbr {
			color: #5ab65a;
		}*/
		.class-selector {
			padding: 4px 8px;
			margin-bottom: 4px;
			border: 1px solid transparent;
			border-radius: 4px;
		}
		.class-selector.active {
			border: 1px solid #5ab65a;
		}
	</style>
@endsection

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/home.js') }}'></script>
	<script type='text/javascript' src='{{ cdn('/js/bootstrap-switch.js') }}'></script>
	<script type='text/javascript'>
		$(function() {
			$('.bootswitch').bootstrapSwitch();

			$('.class-selector').on('click', function() {
				$('.class-selector').removeClass('active');
				$(this).addClass('active');
			});
		});
	</script>
@endsection

@section('banner')
	<a href='/gear' class='btn btn-primary pull-right'>Gear Tool <i class='glyphicon glyphicon-arrow-right'></i></a>
	<h1>Equipment Calculator</h1>
	<h2>Display the gear available for a class at a certain level.</h2>
@endsection

@section('content')

	@if(isset($error) && $error)
	<div class='alert alert-danger'>
		The job you selected is unrecognized.  Try again.
	</div>
	@endif

	{!! Form::open(['url' => '/equipment', 'class' => 'form-horizontal']) !!}
		<fieldset>
			<legend>Select your Class</legend>
			<div class='row text-center'>
				<div class='col-sm-1' style='width: 25%;'>
					<div style='margin-bottom: 20px;'>
						<img src='/img/roles/squared/hand.png' width='32' height='32' rel='tooltip' title='Disciples of the Hand'>
					</div>
					<div class='row'>
						@foreach ($crafting_job_list as $job)
							<div class='col-sm-6'>
								<label class='class-selector{{ $job->id == reset($job_ids['crafting']) ? ' active' : '' }}' data-level='1'>
									<input type='radio' name='class' value='{{ $job->abbr }}' {{ $job->id == reset($job_ids['crafting']) ? ' checked="checked"' : '' }}>
									<img src='/img/jobs/{{ strtoupper($job->abbr) }}-inactive.png' width='24' height='24' rel='tooltip' title='{{ $job->abbr }}'> <span class='abbr'>{{ $job->abbr }}</span>
								</label>
							</div>
						@endforeach
					</div>
				</div>
				<div class='col-sm-1' style='width: 12.5%;'>
					<div style='margin-bottom: 20px;'>
						<img src='/img/roles/squared/land.png' width='32' height='32' rel='tooltip' title='Disciples of the Land'>
					</div>
					@foreach ($gathering_job_list as $job)
						<div>
							<label class='class-selector' data-level='1'>
								<input type='radio' name='class' value='{{ $job->abbr }}'>
								<img src='/img/jobs/{{ strtoupper($job->abbr) }}-inactive.png' width='24' height='24' rel='tooltip' title='{{ $job->abbr }}'> <span class='abbr'>{{ $job->abbr }}</span>
							</label>
						</div>
					@endforeach
				</div>
				<div class='col-sm-1' style='width: 12.5%;'>
					<div style='margin-bottom: 20px;'>
						<img src='/img/roles/squared/tank.png' width='32' height='32' rel='tooltip' title='Tanks'>
					</div>
					@foreach ($advanced_melee_job_list as $job)
						@php
							if ( ! in_array($job->abbr, config('site.roles.tank')))
								continue;
						@endphp
						<div>
							<label class='class-selector' data-level='1'>
								<input type='radio' name='class' value='{{ $job->abbr }}'>
								<img src='/img/jobs/{{ strtoupper($job->abbr) }}-inactive.png' width='24' height='24' rel='tooltip' title='{{ $job->abbr }}'> <span class='abbr'>{{ $job->abbr }}</span>
							</label>
						</div>
					@endforeach
				</div>
				<div class='col-sm-1' style='width: 12.5%;'>
					<div style='margin-bottom: 20px;'>
						<img src='/img/roles/squared/healer.png' width='32' height='32' rel='tooltip' title='Healers'>
					</div>
					@foreach ($advanced_magic_job_list as $job)
						@php
							if ( ! in_array($job->abbr, config('site.roles.healer')))
								continue;
						@endphp
						<div>
							<label class='class-selector' data-level='1'>
								<input type='radio' name='class' value='{{ $job->abbr }}'>
								<img src='/img/jobs/{{ strtoupper($job->abbr) }}-inactive.png' width='24' height='24' rel='tooltip' title='{{ $job->abbr }}'> <span class='abbr'>{{ $job->abbr }}</span>
							</label>
						</div>
					@endforeach
				</div>
				<div class='col-sm-1' style='width: 12.5%;'>
					<div style='margin-bottom: 20px;'>
						<img src='/img/roles/squared/melee.png' width='32' height='32' rel='tooltip' title='Melee DPS'>
					</div>
					@foreach ($advanced_melee_job_list as $job)
						@php
							if ( ! in_array($job->abbr, config('site.roles.melee')))
								continue;
						@endphp
						<div>
							<label class='class-selector' data-level='1'>
								<input type='radio' name='class' value='{{ $job->abbr }}'>
								<img src='/img/jobs/{{ strtoupper($job->abbr) }}-inactive.png' width='24' height='24' rel='tooltip' title='{{ $job->abbr }}'> <span class='abbr'>{{ $job->abbr }}</span>
							</label>
						</div>
					@endforeach
				</div>
				<div class='col-sm-1' style='width: 12.5%;'>
					<div style='margin-bottom: 20px;'>
						<img src='/img/roles/squared/ranged.png' width='32' height='32' rel='tooltip' title='Ranged DPS'>
					</div>
					@foreach ($advanced_melee_job_list as $job)
						@php
							if ( ! in_array($job->abbr, config('site.roles.ranged')))
								continue;
						@endphp
						<div>
							<label class='class-selector' data-level='1'>
								<input type='radio' name='class' value='{{ $job->abbr }}'>
								<img src='/img/jobs/{{ strtoupper($job->abbr) }}-inactive.png' width='24' height='24' rel='tooltip' title='{{ $job->abbr }}'> <span class='abbr'>{{ $job->abbr }}</span>
							</label>
						</div>
					@endforeach
				</div>
				<div class='col-sm-1' style='width: 12.5%;'>
					<div style='margin-bottom: 20px;'>
						<img src='/img/roles/squared/magic.png' width='32' height='32' rel='tooltip' title='Magic Ranged DPS'>
					</div>
					@foreach ($advanced_magic_job_list as $job)
						@php
							if ( ! in_array($job->abbr, config('site.roles.magic')))
								continue;
						@endphp
						<div>
							<label class='class-selector' data-level='1'>
								<input type='radio' name='class' value='{{ $job->abbr }}'>
								<img src='/img/jobs/{{ strtoupper($job->abbr) }}-inactive.png' width='24' height='24' rel='tooltip' title='{{ $job->abbr }}'> <span class='abbr'>{{ $job->abbr }}</span>
							</label>
						</div>
					@endforeach
				</div>
			</div>
		</fieldset>
		<fieldset style='margin-top: 20px;'>
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

@endsection
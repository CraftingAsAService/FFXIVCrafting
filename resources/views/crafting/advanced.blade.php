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
	<script type='text/javascript' src='{{ cdn('/js/crafting-index.js') }}'></script>
@stop

@section('banner')
	<h1>Crafting Calculator</h1>
	<h2>Display all the materials needed to craft one of each item between two levels.</h2>
	<p>In general this will not level you to your desired level.  Visit the <a href='/leve'>Leves</a> page when you're done crafting!</p>
@stop

@section('content')

	@if(isset($error) && $error)
	<div class='alert alert-danger'>
		The job you selected is unrecognized.  Try again.
	</div>
	@endif

	{!! Form::open(['url' => '/crafting', 'class' => 'form-horizontal advanced-crafting-form', 'autocomplete' => 'off']) !!}
		<fieldset>
			<legend>Select your Class</legend>
			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Disciples of the Hand</label>
				<div class='col-sm-8 col-md-9'>
					<div class='btn-group jobs-list' data-toggle='buttons'>
						@foreach($job_list as $job)
						<?php $this_job = $job->id == reset($crafting_job_ids); ?>
						<label class='btn btn-primary class-selector{{ $this_job ? ' select-me' : '' }}'>
							<input type='radio' name='class' value='{{ $job->en_abbr->term }}'> 
							<img src='/img/jobs/{{ $job->en_abbr->term }}-inactive.png' data-active-src='/img/jobs/{{ $job->en_abbr->term }}-active.png' width='24' height='24' rel='tooltip' title='{{ $job->name->term }}'>
						</label>
						@endforeach
					</div>
					<div class='btn-group hidden jobs-list' data-toggle='buttons'>
						@foreach($job_list as $job)
						<?php $this_job = 0; ?>
						<label class='btn btn-warning class-selector multi{{ $this_job ? ' select-me' : '' }}'>
							<input type='checkbox' name='classes[]' value='{{ $job->en_abbr->term }}'> 
							<img src='/img/jobs/{{ $job->en_abbr->term }}-inactive.png' data-active-src='/img/jobs/{{ $job->en_abbr->term }}-active.png' width='24' height='24' rel='tooltip' title='{{ $job->name->term }}'>
						</label>
						@endforeach
					</div>
					<div class='checkbox'>
						<label>
							<input name='multi' id='multi' type='checkbox'>
							I want to select multiple classes
						</label>
					</div>
				</div>
			</div>
		</fieldset>
		<fieldset>
			<legend>Options</legend>
			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Starting Item Level</label>
				<div class='col-sm-4 col-md-3'>
					<input type='number' name='start' value='1' placeholder='Starting Item Level' class='form-control' required='required' min='1' max='200'>
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Ending Item Level</label>
				<div class='col-sm-4 col-md-3'>
					<input type='number' name='end' value='5' placeholder='Ending Item Level' class='form-control' required='required' min='1' max='200'>
				</div>
				<div class='col-sm-4 col-md-6 control-label' style='text-align: left;'>
					<p>
						Maximum range of 10 accepted (1-10, 27-36, etc)
					</p>
				</div>
			</div>

			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Self Sufficient</label>
				<div class='col-sm-8 col-md-9'>
					<div>
						<input type='checkbox' name='self_sufficient' id='self_sufficient_switch' value='1' checked='checked' class='bootswitch' data-on-text='YES' data-off-text='NO'>
						<small><em> - "I want to gather things manually instead of buying them"</em></small>
					</div>
				</div>
			</div>

			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Misc Items</label>
				<div class='col-sm-8 col-md-9'>
					<div>
						<input type='checkbox' name='misc_items' id='misc_items_switch' value='1' class='bootswitch' data-on-text='YES' data-off-text='NO'>
						<small><em> - "Include things like housing and dye items"</em></small>
					</div>
				</div>
			</div>

			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Component Items</label>
				<div class='col-sm-8 col-md-9'>
					<div>
						<input type='checkbox' name='component_items' id='component_items_switch' value='1' class='bootswitch' data-on-text='YES' data-off-text='NO'>
						<small><em> - "Include component items from beastmen tribe questsTÏ"</em></small>
					</div>
				</div>
			</div>
		</fieldset>
		<fieldset>
			<div class='form-group'>
				<div class='col-sm-3 col-sm-offset-4 col-md-offset-3'>
					<button type='submit' class='btn btn-success btn-block btn-lg'>Build my craft list!</button>
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
@stop
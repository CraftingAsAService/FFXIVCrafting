@extends('wrapper.layout')

@section('vendor-css')
	<link href='//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' rel='stylesheet'>
	<link href='/css/bootstrap-switch.css' rel='stylesheet'>
@stop

@section('javascript')
	<script src='/js/home.js'></script>
	<script type='text/javascript' src='/js/bootstrap-switch.js'></script>
@stop

@section('content')

	<h1>Crafting Calculator</h1>

	<p>Display all the materials needed to craft one of each item between two levels.</p>

	<p>In general this will not level you to your desired level.  Visit the <a href='/leve'>Leves</a> page when you're done crafting!</p>

	@if(isset($error) && $error)
	<div class='alert alert-danger'>
		The job you selected is unrecognized.  Try again.
	</div>
	@endif

	<form action='/crafting' method='post' role='form' class='well form-horizontal' autocomplete='off'>
		<fieldset>
			<legend>Select your Class</legend>
			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Disciples of the Hand</label>
				<div class='col-sm-8 col-md-9'>
					<div class='btn-group jobs-list' data-toggle='buttons'>
						@foreach(array('CRP','BSM','ARM','GSM','LTW','WVR','ALC','CUL') as $job)
						<label class='btn btn-primary class-selector{{ $job == 'CRP' ? ' active' : '' }}'>
							<input type='radio' name='class' value='{{ $job }}'{{ $job == 'CRP' ? ' checked="checked"' : '' }}> 
							<img src='/img/classes/{{ $job }}.png' rel='tooltip' title='{{ $job_list[$job] }}'>
						</label>
						@endforeach
					</div>
					<div class='btn-group hidden jobs-list' data-toggle='buttons'>
						@foreach(array('CRP','BSM','ARM','GSM','LTW','WVR','ALC','CUL') as $job)
						<label class='btn btn-warning class-selector multi{{ $job == 'CRP' ? ' active' : '' }}'>
							<input type='checkbox' name='classes[]' value='{{ $job }}'{{ $job == 'CRP' ? ' checked="checked"' : '' }}> 
							<img src='/img/classes/{{ $job }}.png' rel='tooltip' title='{{ $job_list[$job] }}'>
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
					<div class='make-switch' data-on='success' data-off='warning' data-on-label='Yes' data-off-label='No'>
						<input type='checkbox' name='self_sufficient' id='self_sufficient_switch' value='1' checked='checked'>
					</div>
					<span class='ss_yes'>
						"I want to gather things manually instead of buying them"
					</span>
					<span class='ss_no hidden'>
						"Just let me know where to buy stuff"
					</span>
				</div>
			</div>

			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Misc Items</label>
				<div class='col-sm-8 col-md-9'>
					<div class='make-switch' data-on='success' data-off='warning' data-on-label='Yes' data-off-label='No'>
						<input type='checkbox' name='misc_items' id='misc_items_switch' value='1'>
					</div>
					<span class='mi_yes hidden'>
						"Include housing and dye items"
					</span>
					<span class='mi_no'>
						"Do not include housing and dye items"
					</span>
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
	</form>
@stop
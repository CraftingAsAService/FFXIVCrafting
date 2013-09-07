@extends('layout')

@section('vendor-css')
	<link href='//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' rel='stylesheet'>
@stop

@section('javascript')
	<script src='/js/home.js'></script>
@stop

@section('content')

<div class='jumbotron'>
	<h1>Crafting as a Service</h1>
	<h3>Crafting information and planning for FFXIV: ARR</h3>
</div>

<form action='/equipment' method='post' role='form' class='well form-horizontal'>
	<fieldset>
		<legend>Select your Class</legend>
		<div class='form-group'>
			<label class='col-sm-4 col-md-3 control-label'>Disciples of the Hand &amp; Land</label>
			<div class='col-sm-8 col-md-9'>
				<div class='btn-group' data-toggle='buttons'>
					@foreach(array('CRP','BSM','ARM','GSM','LTW','WVR','ALC','CUL') as $job)
					<label class='btn btn-primary class-selector{{ $job == 'CRP' ? ' active' : '' }}'>
						<input type='radio' name='class' value='{{ $job }}'{{ $job == 'CRP' ? ' checked="checked"' : '' }}> 
						<img src='/img/classes/{{ $job }}.png' rel='tooltip' title='{{ $job_list[$job] }}'>
					</label>
					@endforeach
				</div>
				<div class='btn-group' data-toggle='buttons'>
					@foreach(array('MIN','BTN','FSH') as $job)
					<label class='btn btn-info class-selector'>
						<input type='radio' name='class' value='{{ $job }}'> 
						<img src='/img/classes/{{ $job }}.png' rel='tooltip' title='{{ $job_list[$job] }}'>
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
			<label class='col-sm-4 col-md-3 control-label'>Forecast</label>
			<div class='col-sm-4 col-md-3'>
				<p>
					<div id='slider-range-min'></div>
				</p>
			</div>
			<div class='col-sm-4 col-md-6'>
				<p style='margin-top: 4px;'>
					See <input type='text' name='forecast' id='forecast' value='3' style='border: 0; font-style: italic; width: 10px; background-color: inherit;' class='text-center'> set<span id='forecast_plural'>s</span> into the future
				</p>
			</div>
		</div>
		<div class='form-group'>
			<label class='col-sm-4 col-md-3 control-label'>Hindsight</label>
			<div class='col-sm-8 col-md-9'>
				<div class='checkbox'>
					<label>
						<input type='checkbox' name='hindsight' value='yes'>
						See back one level
					</label>
				</div>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<div class='form-group'>
			<div class='col-sm-4 col-md-3 col-sm-offset-4 col-md-offset-3'>
				<button type='submit' class='btn btn-success btn-block btn-lg'>Get my Gear!</button>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend><span rel='tooltip' title='Well... Eventually'>Coming Soon!</span></legend>

		<div class='form-group'>
			<label class='col-sm-4 col-md-3 control-label'>Disciples of War &amp; Magic</label>
			<div class='col-sm-8 col-md-9'>
				<div class='btn-group' data-toggle='buttons'>
					@foreach(array('GLD', 'PGL', 'MRD', 'LNC', 'ARC') as $job)
					<label class='btn btn-danger class-selector disabled'>
						<input type='radio' name='class' value='{{ $job }}' disabled='disabled'> 
						<img src='/img/classes/{{ $job }}.png' rel='tooltip' title='{{ $job }}'>
					</label>
					@endforeach
				</div>
				<div class='btn-group' data-toggle='buttons'>
					@foreach(array('CNJ', 'THM', 'ARN') as $job)
					<label class='btn btn-warning class-selector disabled'>
						<input type='radio' name='class' value='{{ $job }}' disabled='disabled'> 
						<img src='/img/classes/{{ $job }}.png' rel='tooltip' title='{{ $job }}'>
					</label>
					@endforeach
				</div>
			</div>
		</div>
	</fieldset>
</form>

@stop
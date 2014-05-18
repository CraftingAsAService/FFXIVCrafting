@extends('wrapper.layout')

@section('vendor-css')
	<link href='{{ cdn('/css/bootstrap-multiselect.css') }}' rel='stylesheet'>
@stop

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/bootstrap-multiselect.js') }}'></script>
	<script src='{{ cdn('/js/career.js') }}'></script>
@stop

@section('content')

	<h1>Career Calculator</h1>

	<form class='form-horizontal well' action='/career/producer' method='post'>
		<fieldset>
			
			<legend>
				@if($previous_ccp)
				<a href='{{ $previous_ccp }}' class='btn btn-warning btn-sm pull-right'>Load Last Setup</a>
				@endif
				Crafting Career - Producer POV
			</legend>

			<button type='submit' role='button' class='btn btn-success pull-right'>
				View Recipes &raquo;
			</button>

			I am a

			<select class='multiselect hidden' id='supporter-producer-class' name='supporter-producer-class'>
				@foreach(array('CRP','BSM','ARM','GSM','LTW','WVR','ALC','CUL') as $job)
				<option value='{{ $job }}'{{ $job == 'CRP' ? ' selected="selected"' : '' }}>{{ $job_list[$job] }}</option>
				@endforeach
			</select>

			, what can I make to support

			<select class='multiselect hidden' multiple='multiple' id='supporter-supported-classes' name='supporter-supported-classes[]'>
				@foreach(array('CRP','BSM','ARM','GSM','LTW','WVR','ALC','CUL') as $job)
				<option value='{{ $job }}' selected='selected'>{{ $job_list[$job] }}</option>
				@endforeach
			</select>

			between levels

			<input type='number' min='0' max='70' value='1' class='form-control text-center inline-input level-input' id='supporter-min-level' name='supporter-min-level'>

			and

			<input type='number' min='0' max='70' value='70' class='form-control text-center inline-input level-input' id='supporter-max-level' name='supporter-max-level'>

			?

		</fieldset>
	</form>

	<form class='form-horizontal well' action='/career/receiver' method='post'>
		<fieldset>
			<legend>
				@if($previous_ccr)
				<a href='{{ $previous_ccr }}' class='btn btn-warning btn-sm pull-right'>Load Last Setup</a>
				@endif
				Crafting Career - Receiver POV
			</legend>

			<button type='submit' role='button' class='btn btn-success pull-right'>
				View Recipes &raquo;
			</button>

			I am a

			<select class='multiselect hidden' id='receiver-recipient-class' name='receiver-recipient-class'>
				@foreach(array('CRP','BSM','ARM','GSM','LTW','WVR','ALC','CUL') as $job)
				<option value='{{ $job }}'{{ $job == 'CRP' ? ' selected="selected"' : '' }}>{{ $job_list[$job] }}</option>
				@endforeach
			</select>
			, what should

			<select class='multiselect hidden' multiple='multiple' id='receiver-producer-classes' name='receiver-producer-classes[]'>
				@foreach(array('CRP','BSM','ARM','GSM','LTW','WVR','ALC','CUL') as $job)
				<option value='{{ $job }}' selected='selected'>{{ $job_list[$job] }}</option>
				@endforeach
			</select>

			make for me between levels

			<input type='number' min='0' max='70' value='1' class='form-control text-center inline-input level-input' id='receiver-min-level' name='receiver-min-level'>

			and

			<input type='number' min='0' max='70' value='70' class='form-control text-center inline-input level-input' id='receiver-max-level' name='receiver-max-level'>

			?
			
		</fieldset>
	</form>

	<form class='form-horizontal well' action='/career/gathering' method='post'>
		<fieldset>
			<legend>
				@if($previous_gc)
				<a href='{{ $previous_gc }}' class='btn btn-warning btn-sm pull-right'>Load Last Setup</a>
				@endif
				Gathering Career
			</legend>

			<button type='submit' role='button' class='btn btn-success pull-right'>
				View Items &raquo;
			</button>

			I am a

			<select class='multiselect hidden' id='gatherer-class' name='gatherer-class'>
				@foreach(array('MIN','BTN','FSH') as $job)
				<option value='{{ $job }}'{{ $job == 'MIN' ? ' selected="selected"' : '' }}>{{ $job_list[$job] }}</option>
				@endforeach
			</select>
			
			, what should I obtain to support

			<select class='multiselect hidden' multiple='multiple' id='gathering-supported-classes' name='gathering-supported-classes[]'>
				@foreach(array('CRP','BSM','ARM','GSM','LTW','WVR','ALC','CUL', 'MIN', 'BTN', 'FSH') as $job)
				<option value='{{ $job }}' selected='selected'>{{ $job_list[$job] }}</option>
				@endforeach
			</select>

			between levels

			<input type='number' min='0' max='70' value='1' class='form-control text-center inline-input level-input' id='gathering-min-level' name='gathering-min-level'>

			and

			<input type='number' min='0' max='70' value='70' class='form-control text-center inline-input level-input' id='gathering-max-level' name='gathering-max-level'>
			
			?

		</fieldset>
	</form>

	<form class='form-horizontal well' action='/career/gathering' method='post'>
		<input type='hidden' name='gatherer-class' value='BTL'>
		<fieldset>
			<legend>
				@if($previous_bc)
				<a href='{{ $previous_bc }}' class='btn btn-warning btn-sm pull-right'>Load Last Setup</a>
				@endif
				Battling Career
			</legend>

			<button type='submit' role='button' class='btn btn-success pull-right'>
				View Items &raquo;
			</button>

			I am a lord of battle!

			What can I maim or pillage to support

			<select class='multiselect hidden' multiple='multiple' id='battling-supported-classes' name='gathering-supported-classes[]'>
				@foreach(array('CRP','BSM','ARM','GSM','LTW','WVR','ALC','CUL') as $job)
				<option value='{{ $job }}' selected='selected'>{{ $job_list[$job] }}</option>
				@endforeach
			</select>

			between levels

			<input type='number' min='0' max='70' value='1' class='form-control text-center inline-input level-input' id='gathering-min-level' name='gathering-min-level'>

			and

			<input type='number' min='0' max='70' value='70' class='form-control text-center inline-input level-input' id='gathering-max-level' name='gathering-max-level'>
			
			?
			
		</fieldset>
	</form>

	<div class='panel panel-info'>
		<div class='panel-heading'>
			<h3 class='panel-title'>What is this?</h3>
		</div>
		<div class='panel-body'>

			<p>I define your Career as making one of everything, and gathering those items yourself.</p>

			<p>Imagine that you are leveling Blacksmithing.  Just how many Bronze Ingots do you need?  Imagine yourself out Mining.  How many Iron Ore do you really need?</p>
			
			<p>There are items with multiple recipes (Bronze Ingots are made from BSM &amp; ARM for example).  Due to the complex nature of the calculations, items were simply split evenly between those crafts.</p>

		</div>
	</div>

@stop
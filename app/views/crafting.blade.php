@extends('layout')

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

	@if(isset($error) && $error)
	<div class='alert alert-danger'>
		The job you selected is unrecognized.  Try again.
	</div>
	@endif

	<form action='/crafting' method='post' role='form' class='well form-horizontal'>
		<fieldset>
			<legend>Select your Class</legend>
			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Disciples of the Hand</label>
				<div class='col-sm-8 col-md-9'>
					<div class='btn-group' data-toggle='buttons'>
						@foreach(array('CRP','BSM','ARM','GSM','LTW','WVR','ALC','CUL') as $job)
						<label class='btn btn-primary class-selector{{ $job == 'CRP' ? ' active' : '' }}'>
							<input type='radio' name='class' value='{{ $job }}'{{ $job == 'CRP' ? ' checked="checked"' : '' }}> 
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
				<label class='col-sm-4 col-md-3 control-label'>Staring Level</label>
				<div class='col-sm-4 col-md-3'>
					<input type='number' name='start' value='1' placeholder='Level (e.g. 1)' class='form-control' required='required' min='1' max='100'>
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Ending Level</label>
				<div class='col-sm-4 col-md-3'>
					<input type='number' name='end' value='5' placeholder='Level (e.g. 5)' class='form-control' required='required' min='1' max='100'>
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
		</fieldset>
		<fieldset>
			<div class='form-group'>
				<div class='col-sm-4 col-md-3 col-sm-offset-4 col-md-offset-3'>
					<button type='submit' class='btn btn-success btn-block btn-lg'>Build my craft list!</button>
				</div>
			</div>
		</fieldset>
	</form>

	<h3>Quest information for Disciples of the Land</h3>
	<div class='well form-horizontal'>
		<div class='row'>
			<div class='col-sm-6'>
				<fieldset>
					<legend>Select your Class</legend>
					<div class='form-group'>
						<label class='col-sm-6 control-label'>Disciples of the Land</label>
						<div class='col-sm-6'>
							<div class='btn-group'>
								@foreach(array('MIN','BTN','FSH') as $job)
								<span class='btn btn-info quest-selector' data-job='{{ $job }}'>
									<img src='/img/classes/{{ $job }}.png' rel='tooltip' title='{{ $job_list[$job] }}'>
								</span>
								@endforeach
							</div>
						</div>
					</div>
				</fieldset>
			</div>
			<div class='col-sm-6'>
				@foreach(array('MIN','BTN','FSH') as $job)
				<fieldset class='quests hidden' data-job='{{ $job }}'>
					<legend>{{ $job_list[$job] }}</legend>
					<ul>
						@foreach($quests[$job] as $quest)
						<li>
							Level {{ $quest->level }}: 
							@if ( ! $quest->item)
								No data! Please help complete the list.
							@else
								{{ preg_replace('/\\\\/', '', $quest->item->name) }} 
								<small>x</small>{{ $quest->amount }}
								@if($quest->quality)
								<strong>(HQ)</strong>
								@endif
							@endif
							@if($quest->notes)
							({{ $quest->notes }})
							@endif
						</li>
						@endforeach
					</ul>
				</fieldset>
				@endforeach
			</div>
		</div>
	</div>
@stop
@extends('app')

@section('meta')
	<meta name="robots" content="nofollow">
@stop

@section('banner')
	<h1>Load Character</h1>
	<h2>Your levels will help the system make smarter choices!</h2>
@stop

@section('css')
	<link href='{{ cdn('/css/jquery-ui.css') }}' rel='stylesheet' />
@stop

@section('javascript')
	<script type='text/javascript'>
		var servers = ["{!! implode('", "', Config::get('site.servers')) !!}"];
		$(function() {
			$('#server').autocomplete({
				source: servers
			});
		});
	</script>

@stop

@section('content')

	{!! Form::open(array('class' => 'form-horizontal')) !!}

		<div class='alert alert-danger'>
			The API I was using for the login functionality has been broken, so I've had to remove it.  Sorry! [<a href='https://github.com/viion/lodestone-nodejs'>Their GitHub</a>]
		</div>

		<fieldset>
			<div class='form-group'>
				<label for='name' class='col-sm-4 col-md-3 col-lg-2 control-label'>Character Name</label>
				<div class='col-sm-8 col-md-9 col-lg-10'>
					<input type='text' name='name' class='form-control' id='name' value='{{ $character_name }}' required='required'>
				</div>
			</div>
			<div class='form-group'>
				<label for='server' class='col-sm-4 col-md-3 col-lg-2 control-label'>Server</label>
				<div class='col-sm-8 col-md-9 col-lg-10'>
					<input type='text' name='server' class='form-control' id='server' value='{{ $server }}' required='required'>
				</div>
			</div>
			<div class='form-group text-right'>
				<button type='submit' class='btn btn-success'>Load Character</button>
			</div>
		</fieldset>

	{!! Form::close() !!}

	<div class='well'>
		Thanks to <a href='http://xivpads.com/'>XIVPads</a> for their character API!
	</div>

@stop
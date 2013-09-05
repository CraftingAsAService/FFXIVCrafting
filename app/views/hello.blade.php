@extends('layout')

@section('content')

<div class='jumbotron'>
	<h1>Crafting as a Service</h1>
	<h3>Crafting information and planning for FFXIV: ARR</h3>
</div>

<form action='/equipment' method='post' role='form' class='form-inline well'>
	<fieldset>
		<legend>Gear Calculator</legend>
		<div class='form-group'>
			<select name='class' class='form-control'>
				@foreach(Job::all() as $job)
				<option value='{{ $job->abbreviation }}'>{{ $job->name }}</option>
				@endforeach
			</select>
		</div>
		<div class='form-group'>
			<input name='level' placeholder='Level (e.g. 5)' class='form-control'>
		</div>
		<button type='submit' class='btn btn-primary'>Get my Gear!</button>
	</fieldset>
</form>

@stop
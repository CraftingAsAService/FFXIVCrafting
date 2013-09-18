@extends('layout')

@section('javascript')
	<script src='/js/home.js'></script>
@stop

@section('content')

	<h1>Gathering Calculator</h1>

	<p>Wondering what to focus on while gathering?</p>

	@if(isset($error) && $error)
	<div class='alert alert-danger'>
		The job you selected is unrecognized.  Try again.
	</div>
	@endif

	<div class='well'>
		<div class='row'>
			<div class='col-sm-4 text-center'>
				<a href='/gathering/list/MIN' class='btn btn-lg btn-primary'>
					<img src='/img/classes/MIN.png'>
					{{ $job_list['MIN'] }}
				</a>
			</div>
			<div class='col-sm-4 text-center'>
				<a href='/gathering/list/BTN' class='btn btn-lg btn-primary'>
					<img src='/img/classes/BTN.png'>
					{{ $job_list['BTN'] }}
				</a>
			</div>
			<div class='col-sm-4 text-center'>
				<a href='/gathering/list/FSH' class='btn btn-lg btn-primary disabled'>
					<img src='/img/classes/FSH.png'>
					{{ $job_list['FSH'] }}
				</a>
			</div>
		</div>
	</div>
@stop
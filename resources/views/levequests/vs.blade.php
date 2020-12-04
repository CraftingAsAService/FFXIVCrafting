@extends('app')

@section('banner')
	<h1>Leve Vs Leve</h1>
@endsection

@section('content')

<?php $vs = true; ?>

<div class='row'>
	<div class='col-sm-6'>
		@include('levequests._chart', $a)
	</div>
	<div class='col-sm-6'>
		@include('levequests._chart', $b)
	</div>
</div>

@endsection
@extends('wrapper.layout')

@section('banner')
	<h1>Leve Vs Leve</h1>
@stop

@section('content')

<?php $vs = true; ?>

<div class='row'>
	<div class='col-sm-6'>
		@include('leve._chart', $a)
	</div>
	<div class='col-sm-6'>
		@include('leve._chart', $b)
	</div>
</div>

@stop
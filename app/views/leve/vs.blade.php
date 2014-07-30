@extends('wrapper.layout')

@section('javascript')
	<script type='text/javascript' src='http://xivdb.com/tooltips.js'></script>
	<script type='text/javascript'>
		var xivdb_tooltips = 
		{ 
			"language"      : "{{ strtoupper(Config::get('language')) }}",
			"frameShadow"   : true,
			"compact"       : false,
			"statsOnly"     : false,
			"replaceName"   : false,
			"colorName"     : true,
			"showIcon"      : false,
		} 
	</script>
@stop

@section('content')

<?php $vs = true; ?>

<h1>Leve Vs Leve</h1>

<div class='row'>
	<div class='col-sm-6'>
		@include('leve._chart', $a)
	</div>
	<div class='col-sm-6'>
		@include('leve._chart', $b)
	</div>
</div>

@stop
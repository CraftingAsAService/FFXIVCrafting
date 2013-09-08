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

@include('snippets.calculate_form')

@stop
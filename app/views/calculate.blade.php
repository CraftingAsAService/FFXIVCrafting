@extends('layout')

@section('vendor-css')
	<link href='//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' rel='stylesheet'>
@stop

@section('javascript')
	<script src='/js/home.js'></script>
@stop

@section('content')

@if(isset($error) && $error)
<div class='alert alert-danger'>
	The job you selected is unrecognized.  Try again.
</div>
@endif

@include('snippets.calculate_form')

@stop
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

<div class='row'>
	<div class='col-sm-6'>
		<div class='jumbotron'>
			<h2>Equipment Calculator</h2>
			<p style='font-size: .7em;'>
				Want to know what equipment you can craft at a certain level for your class?  
				Use this tool to select your disciple and level range.
			</p>
			<a href='/equipment' class='btn btn-primary btn-lg btn-block'>Gear me out!</a>
		</div>
	</div>
	<div class='col-sm-6'>
		<div class='jumbotron'>
			<h2>Crafting Calculator</h2>
			<p style='font-size: .7em;'>
				Want to get everything you need before trying to level Weaver from levels 5 to 10?  
				Use this tool to select your craft and level range.
			</p>
			<a href='/crafting' class='btn btn-primary btn-lg btn-block'>Vocationalize!</a>
		</div>
	</div>
</div>

@stop
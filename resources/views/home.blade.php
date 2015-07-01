@extends('app')

@section('vendor-css')
	<link href='//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' rel='stylesheet'>
@stop

@section('javascript')
	<script type='text/javascript' src='{!! cdn('/js/home.js') !!}'></script>
@stop

@section('banner')
	<h1>Welcome to FFXIV Crafting</h1>
	<h2>Crafting information and planning for FFXIV: A Realm Reborn</h2>
@stop

@section('content')

<div class='row'>
	<div class='col-sm-4 homepage-step'>
		<div class='home-callout' data-href='/gear'>
			<img src='/img/homepage/icon-equipment.png' width='40' height='40'>
			<p class='title'>
				Gear<span class='hidden-sm'>&nbsp;Profiler</span>
			</p>
			<p class='description'>
				Your goal is to create HQ items every time, and the only way to do that is with the proper gear.  Start with your gear!
			</p>
			<p class='step'>
				<img src='/img/homepage/icon-number-1.png' class='pull-right' width='20' height='20'>
				Begin Step
			</p>
		</div>
	</div>
	<div class='col-sm-4 homepage-step'>
		<div class='home-callout' data-href='/crafting'>
			<img src='/img/homepage/icon-anvil.png' width='40' height='40'>
			<p class='title'>
				Crafting<span class='hidden-sm'>&nbsp;Calculator</span>
			</p>
			<p class='description'>
				Make your life easier, gather everything you'll need before you start crafting.  Be sure to pick up some extras!
			</p>
			<p class='step'>
				<img src='/img/homepage/icon-number-2.png' class='pull-right' width='20' height='20'>
				Begin Step
			</p>
		</div>
	</div>
	<div class='col-sm-4 homepage-step'>
		<div class='home-callout' data-href='/levequests'>
			<img src='/img/homepage/icon-levequest.png' width='40' height='40'>
			<p class='title'>
				Levequests
			</p>
			<p class='description'>
				Bonus XP from crafting your list won't completely level you.  Find the best Levequest to help you level up to the next crafting tier!
			</p>
			<p class='step'>
				<img src='/img/homepage/icon-number-3.png' class='pull-right' width='20' height='20'>
				Begin Step
			</p>
		</div>
	</div>
</div>

@stop
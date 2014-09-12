@extends('wrapper.layout')

@section('vendor-css')
	<link href='//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' rel='stylesheet'>
@stop

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/home.js') }}'></script>
@stop

@section('banner')
	<h1>Welcome to FFXIV Crafting</h1>
	<h2>Crafting information and planning for FFXIV: A Realm Reborn</h2>
@stop

@section('content')

<div class='row'>
	<div class='col-sm-4 homepage-step'>
		<div class='home-callout' data-href='/equipment'>
			<img src='/img/homepage/icon-equipment.png' width='40' height='40'>
			<p class='title'>
				Equipment<span class='hidden-sm'>&nbsp;Calculator</span>
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
		<div class='home-callout' data-href='/leve'>
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

<?php /*
<div class="row">
	<div class="col-sm-6">
		<a href="/equipment">
			<div class="home-callout">
				<div class="row">
					<div class="col-sm-4">
						<img src="/img/homepage/callout-equipment.png" class="img-responsive center-block equipment">
					</div>
					<div class="col-sm-8 copy">
						<p class="title">Equipment Calculator</p>
						<p class="description">
							Want to know what equipment you can craft at a certain level for your class?
							Use this tool to select your disciple and level range.
						</p>
					</div>
				</div>
			</div>
		</a>
	</div>

	<div class="col-sm-6">
		<div class="home-callout">
			<div class="row">
				<div class="col-sm-4">
				</div>
				<div class="col-sm-8">
					<h2>Crafting Calculator</h2>
					<p>
						Want to get everything you need before trying to level Weaver from levels 5 to 10?
						Use this tool to select your craft and level range.
					</p>
					<a href='/crafting' class='btn btn-primary'>Vocationalize &raquo;</a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-6">
		<div class="home-callout">
			<div class="row">
				<div class="col-sm-4">
				</div>
				<div class="col-sm-8">
					<h2>Career Calculator</h2>
					<p>
						Ever ask yourself when mining "What should I be digging up?", or when crafting "Just how many total Bronze Ingots do I really need to make?"
						Find the most efficient use of your pickaxe or needle with this tool!
					</p>
					<a href='/gathering' class='btn btn-primary'>See the totals &raquo;</a>
				</div>
			</div>
		</div>
	</div>

	<div class="col-sm-6">
		<div class="home-callout">
			<div class="row">
				<div class="col-sm-4">
				</div>
				<div class="col-sm-8">
					<h2>Recipe Book</h2>
					<p>
						Quickly discover what materials are needed to craft a specific item.
					</p>
					<a href='/crafting' class='btn btn-primary'>Vocationalize &raquo;</a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-6">
		<div class="home-callout">
			<div class="row">
				<div class="col-sm-4">
				</div>
				<div class="col-sm-8">
					<h2>Quests</h2>
					<p>
						Those things that you do while playing a game that usually feel like a grind, but are so darn fun.
					</p>
					<a href='/gathering' class='btn btn-primary'>Action &raquo;</a>
				</div>
			</div>
		</div>
	</div>

	<div class="col-sm-6">
		<div class="home-callout">
			<div class="row">
				<div class="col-sm-4">
				</div>
				<div class="col-sm-8">
					<h2>Leves</h2>
					<p>
						Find the best Tradecraft Leves for your time and gil.
					</p>
					<a href='/crafting' class='btn btn-primary'>Find out more &raquo;</a>
				</div>
			</div>
		</div>
	</div>
</div>
*/ ?>
@stop
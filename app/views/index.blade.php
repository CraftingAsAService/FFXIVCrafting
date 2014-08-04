@extends('wrapper.layout')

@section('vendor-css')
	<link href='//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' rel='stylesheet'>
@stop

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/home.js') }}'></script>
@stop

@section('banner')
	<h1>Crafting as a Service</h1>
	<h2>Crafting information and planning for FFXIV: A Realm Reborn</h2>
@stop

@section('content')


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
						Use this took to quickly discover what materials are needed to craft a specific item.
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
					<a href='/gathering' class='btn btn-primary'>Button &raquo;</a>
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
						Not going to lie, I have no idea what these are.
					</p>
					<a href='/crafting' class='btn btn-primary'>Find out more &raquo;</a>
				</div>
			</div>
		</div>
	</div>
</div>

@stop
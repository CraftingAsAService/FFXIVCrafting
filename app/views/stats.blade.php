@extends('layout')

@section('content')
	
	<h1>Stats Explained</h1>

	<h2>
		<img src='/img/stats/Craftsmanship.png' class='stat-icon'>
		Craftsmanship
	</h2>

	<p>
		Increases the amount of progress gained when using Basic/Standard Synthesis.
	</p>

	<h2>
		<img src='/img/stats/Control.png' class='stat-icon'>
		Control
	</h2>

	<p>
		Increases the amount of quality gained when using quality increasing actions. The higher quality is for a synthesis, the higher your odds of making a High-Quality version of the item is. (Using HQ materials while crafting will also boost quality prior to starting the craft. The more HQ mats used, the more the quality bar will be filled at the start.)
	</p>

	<h2>
		<img src='/img/stats/CP.png' class='stat-icon'>
		CP
	</h2>

	<p>
		Acts as MP for all crafting jobs. Gauge fills completely when you change to a crafting class and when you finish crafting an item. CP does not recover during synthesis unless a CP recovery skill was used. Allows use of special crafting skills. Skill effects include increasing durability of an item, increasing Control stat each time quality of an item is increased (to increase chance of HQ item), etc.
	</p>

	<h2>
		<img src='/img/stats/Gathering.png' class='stat-icon'>
		Gathering
	</h2>

	<p>
		Increases chance to successfully gather an item. Will also provide a bonus to certain gathering nodes with Tree/Stone Whisperer.
	</p>

	<h2>
		<img src='/img/stats/Perception.png' class='stat-icon'>
		Perception
	</h2>

	<p>
		Increases HQ gathering rate. Will also provide a bonus to certain gathering nodes with Tree/Stone Whisperer.
	</p>

	<h2>
		<img src='/img/stats/GP.png' class='stat-icon'>
		GP
	</h2>

	<p>
		Acts as MP for Miner/Botanist. Recharges by 5 GP every 3 seconds while not gathering. While gathering, each successful gather provides a static 5 GP.
	</p>

	<p class='well'>
		<small><em>Information pulled from <a href='http://www.ffxivpro.com/forum/topic/40286/ffxiv-arr-stats/'>here</a></em></small>
	</p>

@stop
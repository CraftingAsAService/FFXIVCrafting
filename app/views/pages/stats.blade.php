@extends('wrapper.layout')

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

	<h2>
		<img src='/img/stats/Strength.png' class='stat-icon'>
		Strength
	</h2>

	<p>
		Increases HQ chance for Blacksmith's Primary tool and Armorer's Secondary tool.  Increases Botanist's Gathering.
	</p>

	<h2>
		<img src='/img/stats/Dexterity.png' class='stat-icon'>
		Dexterity
	</h2>

	<p>
		Increases HQ chance for Goldsmith/Weaver's Primary tool and Carpenter's Secondary tool.  Increases Fisher's Gathering.
	</p>

	<h2>
		<img src='/img/stats/Vitality.png' class='stat-icon'>
		Vitality
	</h2>

	<p>
		Increases HQ chance for Carpenter/Armorer/Leatherworker's Primary tool.  Increases Miner's Gathering.
	</p>

	<h2>
		<img src='/img/stats/Intelligence.png' class='stat-icon'>
		Intelligence
	</h2>

	<p>
		Increases HQ chance for Alchemist's Primary tool and Goldsmith/Leatherworker's Secondary tool.  Increases Botanist's Gathering.
	</p>

	<h2>
		<img src='/img/stats/Mind.png' class='stat-icon'>
		Mind
	</h2>

	<p>
		Increases HQ chance for Culinarian's Primary tool and Blacksmith/Weaver's Secondary tool.  Increases Miner's Gathering.
	</p>

	<h2>
		<img src='/img/stats/Piety.png' class='stat-icon'>
		Piety
	</h2>

	<p>
		Increases HQ chance for Alchemist/Culinarian's Secondary tool.  Increases Fisher's Gathering.
	</p>

	<p class='well'>
		<small><em>Some information pulled from <a href='http://www.ffxivpro.com/forum/topic/40286/ffxiv-arr-stats/'>this thread</a>, other pieces pulled from <a href='http://ffxiv.gamerescape.com/wiki/Category:Attributes'>Gamer Escape's wiki</a>.</em></small>
	</p>

@stop
@extends('wrapper.layout')

@section('css')
	<style type='text/css'>
		.shroud .north 		{ 
			top: -120px; 
			left: -90px;
		}
		.shroud .gridania 	{ 
			top: 70px; 
			left: 220px;
		}
		.shroud .east 		{ 
			top: 150px; 
			left: 560px;
		}
		.shroud .central 	{ 
			top: 290px; 
			left: 110px;
		}
		.shroud .south 		{ 
			top: 520px; 
			left: 310px;
		}

		.thanalan .western 	{ 
			top: 470px;
			left: 20px;
		}
		.thanalan .uldah 	{ 
			top: 530px;
			left: 290px;
		}
		.thanalan .southern { 
			top: 470px;
			left: 520px;
		}
		.thanalan .northern { 
			top: -80px;
			left: 210px;
		}
		.thanalan .eastern 	{ 
			top: 90px;
			left: 530px;
		}
		.thanalan .central 	{ 
			top: 220px;
			left: 230px;
		}

		.noscea .western {
			top: 110px;
			left: -80px;
		}
		.noscea .upper 	{
			top: 40px;
			left: 220px;
		}
		.noscea .middle 	{
			top: 400px;
			left: 250px;
		}
		.noscea .lower 	{
			top: 400px;
			left: 400px;
		}
		.noscea .limsa 	{
			top: 550px;
			left: 20px;
		}
		.noscea .eastern {
			top: 0px;
			left: 520px;
		}
		.noscea .outer {
			top: 0px;
			left: 220px;
		}

		.coerthas .coerthas-central {
			top: 0px;
			left: 40px;
		}
		.coerthas .mor-dhona {
			top: 380px;
			left: 140px;
		}
	</style>
@stop

@section('javascript')
<script type='text/javascript' src='{{ cdn('/js/jquery.overscroll.js') }}'></script>
<script type='text/javascript' src='{{ cdn('/js/map.js') }}'></script>
@stop

@section('content')

<h1>
	<i class='glyphicon glyphicon-globe'></i>
	Map
</h1>

<h2>The Black Shroud (Gridania)</h2>

<div class='globe shroud'>
	<div class='area'>
		<img src='/img/maps/the-black-shroud-the-black-shroud-region-01.png'>
	</div>

	<div class='region north'>
		<img src='/img/maps/the-black-shroud-north-shroud-f1f4-00.png' title='Map Name'>
	</div>
	<div class='region central'>
		<img src='/img/maps/the-black-shroud-central-shroud-f1f1-00.png' title='Map Name'>
	</div>
	<div class='region gridania'>
		<img src='/img/maps/the-black-shroud-gridania.png' title='Map Name'>
	</div>
	<div class='region south'>
		<img src='/img/maps/the-black-shroud-south-shroud-f1f3-00.png' title='Map Name'>
	</div>
	<div class='region east'>
		<img src='/img/maps/the-black-shroud-east-shroud-f1f2-00.png' title='Map Name'>
	</div>
</div>

<h2>Thanalan (Ul'dah)</h2>

<div class='globe thanalan'>
	<div class='area'>
		<img src='/img/maps/thanalan-thanalan-region-02.png'>
	</div>

	<div class='region western'>
		<img src='/img/maps/thanalan-western-thanalan-w1f1-00.png' title='Map Name'>
	</div>
	<div class='region uldah'>
		<img src='/img/maps/thanalan-uldah---steps-of-thal.png' title='Map Name'>
	</div>
	<div class='region southern'>
		<img src='/img/maps/thanalan-southern-thanalan-w1f4-01.png' title='Map Name'>
	</div>
	<div class='region northern'>
		<img src='/img/maps/thanalan-northern-thanalan-w1f5-00.png' title='Map Name'>
	</div>
	<div class='region eastern'>
		<img src='/img/maps/thanalan-eastern-thanalan-w1f3-00.png' title='Map Name'>
	</div>
	<div class='region central'>
		<img src='/img/maps/thanalan-central-thanalan-w1f2-00.png' title='Map Name'>
	</div>
</div>

<h2>La Noscea (Limsa Lominsa)</h2>

<div class='globe noscea'>
	<div class='area'>
		<img src='/img/maps/la-noscea-la-noscea-region-00.png'>
	</div>

	<div class='region western'>
		<img src='/img/maps/la-noscea-western-la-noscea-s1f4-00.png' title='Map Name'>
	</div>
	<div class='region upper'>
		<img src='/img/maps/la-noscea-upper-la-noscea-s1f5-00.png' title='Map Name'>
	</div>
	<div class='region middle'>
		<img src='/img/maps/la-noscea-middle-la-noscea-s1f1-00.png' title='Map Name'>
	</div>
	<div class='region lower'>
		<img src='/img/maps/la-noscea-lower-la-noscea-s1f2-00.png' title='Map Name'>
	</div>
	<div class='region limsa'>
		<img src='/img/maps/la-noscea-limsa-lominsa.png' title='Map Name'>
	</div>
	<div class='region eastern'>
		<img src='/img/maps/la-noscea-eastern-la-noscea-s1f3-00.png' title='Map Name'>
	</div>
	<div class='region outer'>
		<img src='/img/maps/la-noscea-outer-la-noscea-s1f6-00.png' title='Map Name'>
	</div>	
</div>

<h2>Coerthas / Mor Dhona</h2>

<div class='globe coerthas'>
	<div class='area'>
		<img src='/img/maps/mor-dhona-mor-dhona-region-04.png'>
	</div>
	
	<div class='region coerthas-central'>
		<img src='/img/maps/coerthas-coerthas-central-highlands-r1f1-00.png' title='Map Name'>
	</div>
	<div class='region mor-dhona'>
		<img src='/img/maps/mor-dhona-mor-dhona-l1f1-01.png' title='Map Name'>
	</div>
</div>


@stop
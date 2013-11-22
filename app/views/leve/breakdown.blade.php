@extends('layout')

@section('javascript')
	<script type='text/javascript' src='http://xivdb.com/tooltips.js'></script>
	<script type='text/javascript'>
		var xivdb_tooltips = 
		{ 
			"language"      : "EN",
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

<h1>Leve Breakdown</h1>

@include('leve._chart')

@if($leve->triple)
<p>
	Because this is a Triple Turnin, using three leve allowances will net you three turnins, as opposed to three allowances allowing only one turning with other leves.
</p>
@endif

<p>Most likely you will stop doing this leve after 5 levels, but depending on market prices/etc it may be benefitial to keep turning it in well past that.</p>

<p>Use your best judgement when deciding which level to mass produce.</p>

<h3>Compare this Leve against...</h3>

<ul>
	@foreach($others as $other)
	<li>
		<a href='/leve/vs/{{ $leve->id }}/{{ $other->id }}'>{{ $other->name }}</a>
		@if($other->triple)
		<strong>Triple!</strong>
		@endif
	</li>
	@endforeach
</ul>

@stop
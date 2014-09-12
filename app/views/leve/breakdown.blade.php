@extends('wrapper.layout')

@section('banner')
	<h1>Levequest Breakdown</h1>
@stop

@section('content')

@include('leve._chart')

<h3>Notes</h3>

@if($leve->triple)
<p>
	Because this is a Triple Turnin, using three leve allowances will net you three turnins, as opposed to three allowances allowing only one turning with other leves.
</p>
@endif

<p>Most likely you will stop doing this leve after 5 levels, but depending on market prices/etc it may be benefitial to keep turning it in well past that.  Also remember to Diversify.  The same quest will not always be available to choose.</p>

<p>Use your best judgement when deciding which level to mass produce.</p>

<h3>Compare this Levequest against...</h3>

<ul>
	@foreach($others as $other)
	<li>
		<a href='/levequests/vs/{{ $leve->id }}/{{ $other->id }}'>{{ $other->name }}</a>
		@if($other->triple)
		<i class='glyphicon glyphicon-fire text-danger' rel='tooltip' title='Triple Turnin!'></i>
		@endif
	</li>
	@endforeach
</ul>

@stop
@extends('app')

@section('banner')
	<h1>Levequest Breakdown</h1>
@endsection

@section('content')

@include('levequests._chart')

<h3>Notes</h3>

@if ($leve->level >= 50)
<p>
	You will only gain the listed XP amount if you're within the Expansion's Level Range; A level 70+ character attempting to complete a level 60-something Leve will gain almost no experience.
</p>
@endif

@if($leve->repeats)
<p>
	Because this is a Repeatable Turnin, using one leve allowances will net you {{ $leve->repeats + 1 }} turnins, as opposed to {{ $leve->repeats }} allowances allowing only one turnins with other leves.
</p>
@endif

<p>Most likely you will stop doing this leve after 5 levels, but depending on market prices/etc it may be benefitial to keep turning it in well past that.  Also remember to Diversify.  The same quest will not always be available to choose.</p>

<p>Use your best judgement when deciding which level to mass produce.</p>

<h3>Compare this Levequest against...</h3>

<ul>
	@foreach($others as $other)
	<li>
		<a href='/levequests/vs/{{ $leve->id }}/{{ $other->id }}'>{{ $other->name }}</a>
		@if($other->repeats)
		<i class='glyphicon glyphicon-fire text-danger' rel='tooltip' title='Repeatable Turnin!'></i>
		@endif
	</li>
	@endforeach
</ul>

@endsection
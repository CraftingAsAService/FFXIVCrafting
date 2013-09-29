
<h2>{{ $leve->name }}</h2>
<p>
	<strong>Level:</strong> {{ $leve->level }}
	 &nbsp; 
	<strong>Rewards:</strong> {{ number_format($leve->xp) }} XP and {{ number_format($leve->gil) }} gil.
	@if($leve->triple)
	 &nbsp; 
	<strong>Triple Turnin</strong>
	@endif
</p>
<p>
	<strong>Requires:</strong> {{ $leve->amount }} x <a href='http://xivdb.com/{{ $leve->item->href }}' target='_blank'>{{ $leve->item->name }}</a>
</p>
<p>
	Calculations assume you're turning in HQ items for a total reward of <em>{{ number_format($leve->xp * 3) }} XP</em> per turnin.
</p>

<table class='table table-bordered table-striped'>
	<thead>
		<tr>
			<th>Level</th>
			<th>Requires</th>
			<th>Turnins</th>
			<th>XP Overkill</th>
		</tr>
	</thead>
	<tbody>
		@foreach($chart as $row)
		<tr>
			<td class='text-center'>{{ number_format($row['level']) }}</td>
			<td class='text-center' rel='tooltip' title='{{ number_format($row['requires']) }} - {{ number_format($row['previous_overkill']) }}' data-container='body'>{{ number_format($row['requires'] - $row['previous_overkill']) }}</td>
			<td class='text-center'>{{ number_format($row['turnins']) }}</td>
			<td class='text-center'>{{ number_format($row['overkill']) }}</td>
		</tr>
		@endforeach
	</tbody>
</table>

@if(isset($vs))
<a href='/leve/breakdown/{{ $leve->id }}'>View this Leve solo</a>
@endif
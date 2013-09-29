@foreach($leves as $leve)
<tr>
	<td width='24' class='valign'>
		<img src='/img/classes/{{ $leve->job->abbreviation }}.png' rel='tooltip' title='{{ $leve->job->name }}'>
	</td>
	<td class='item{{ $leve->triple ? ' triple\' rel="tooltip" title="Triple Leve" data-container=\'body' : '' }}'>
		<span class='close' rel='tooltip' title='Leve Level'>{{ $leve->level }}</span>
		<a href='http://xivdb.com/{{ $leve->item->href }}' class='item-name' target='_blank'><img src='/img/items/{{ $leve->item->icon ?: '../noitemicon.png' }}' style='margin-right: 10px;'>{{ $leve->item->name }}</a>
		@if ($leve->amount > 1)
		<span class='label label-primary' rel='tooltip' title='Amount Required' data-container='body'>
			x {{ $leve->amount }}
		</span>
		@endif
	</td>
	<td class='text-center name_type'>
		<span class='label label-success pull-left'>
			{{ $leve->type }}
		</span>
		{{ $leve->name }}
	</td>
	<td class='text-center reward valign'>
		{{ number_format($leve->xp) }} <a href='/leve/breakdown/{{ $leve->id }}'>XP</a>
	</td>
	<td class='text-center reward valign'>
		<img src='/img/coin.png' class='stat-vendors' width='24' height='24'>
		{{ number_format($leve->gil) }}
	</td>
	<td class='text-center location {{ preg_replace('/\W/', '', strtolower($leve->major->name)) }}'>
		<div>{{ ! empty($leve->location) ? $leve->location->name : '' }}</div>
		<div>{{ ! empty($leve->minor) ? $leve->minor->name : '' }}</div>
	</td>
	<td class='text-center valign'>
		<button class='btn btn-default add-to-list' data-item-id='{{ $leve->item->id }}' data-item-name='{{{ $leve->item->name }}}' data-item-quantity='{{{ $leve->amount }}}'>
			<i class='glyphicon glyphicon-shopping-cart'></i>
			<i class='glyphicon glyphicon-plus'></i>
		</button>
	</td>
</tr>
@endforeach
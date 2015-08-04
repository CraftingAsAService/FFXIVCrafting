@foreach($leves as $leve)
<tr>
	<td width='24' class='valign'>
		<i class='class-icon class-id-{{ $leve->job_category->jobs[0]->id }}'></i>
	</td>
	<td class='item'>
		<span class='close ilvl' rel='tooltip' title='Leve Level'>{{ $leve->level }}</span>
		@if($leve->repeats)
		<img src='/img/leve_icon_red.png' rel='tooltip' title='Repeatable Leve!' width='16' class='pull-right' style='clear: right;'>
		@endif
		<a href='http://xivdb.com/?item/{{ $leve->requirements[0]->id }}' class='item-name' target='_blank'>
			<img src='{{ assetcdn('item/' . $leve->requirements[0]->icon . '.png') }}' width='36' height='36' style='margin-right: 10px;'>{{ $leve->requirements[0]->name }}
		</a>
		@if ($leve->requirements[0]->pivot->amount > 1)
		<span class='label label-primary' rel='tooltip' title='Amount Required'>
			x {{ $leve->requirements[0]->pivot->amount }}
		</span>
		@endif
	</td>
	<td class='text-center name_type'>
		<span class='label label-success pull-left'>
			{{ $leve->simple_type }}
		</span>
		{{ $leve->name }}
	</td>
	<td class='text-center reward valign'>
		<a href='/levequests/breakdown/{{ $leve->id }}' class='btn btn-default'>
			<img src='/img/xp.png' width='24' height='24'>
			{{ number_format($leve->xp) }}
		</a>
	</td>
	<td class='text-center reward valign'>
		<img src='/img/coin.png' width='24' height='24'>
		{{ number_format($leve->gil) }}
	</td>
	<td class='text-center location {{ preg_replace('/\W/', '', strtolower($leve->location->name)) }}'>
		{{ $leve->location->name }}
	</td>
	<td class='text-center valign'>
		<button class='btn btn-default leve_rewards' data-toggle='popover' data-content-id='#rewards_for_{{ $leve->id }}'>
			<i class='glyphicon glyphicon-gift'></i>
		</button>
		<div class='hidden' id='rewards_for_{{ $leve->id }}'>
			@if(isset($leve_rewards[$leve->id]))
			@foreach($leve_rewards[$leve->id] as $reward)
			<div class='margin-bottom'>
				@if($reward->item_id)
				<img src='{{ assetcdn('item/' . $reward->item->icon . '.png') }}' width='36' height='36' style='margin-right: 10px;'>
				{{ $reward->item->name }}
				@else
				<img src='/img/noitemicon.png' style='margin-right: 10px;'>
				{{ $reward->item_name }}
				<span class='label label-danger' rel='tooltip' title='See news post to help fill this out!'>Help</span>
				@endif
				<span class='label label-success'>x {{ number_format($reward->amount) }}</span>
			</div>
			@endforeach
			@else
			None listed
			@endif
		</div>
	</td>
	<td class='text-center valign'>
		<button class='btn btn-default add-to-list' data-item-id='{{ $leve->requirements[0]->id }}' data-item-name='{{{ $leve->requirements[0]->name }}}' data-item-quantity='{{{ $leve->requirements[0]->pivot->amount }}}'>
			<i class='glyphicon glyphicon-shopping-cart'></i>
			<i class='glyphicon glyphicon-plus'></i>
		</button>
	</td>
</tr>
@endforeach
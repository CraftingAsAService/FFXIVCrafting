@foreach($leves as $leve)
<?php $recipe_id = count($leve->item->recipe) ? $leve->item->recipe[0]->id : 0; ?>
<tr>
	<td width='24' class='valign'>
		<i class='class-icon class-id-{{ $leve->classjob_id }}'></i>
	</td>
	<td class='item{{ $leve->repeats ? ' repeats\' rel="tooltip" title="Repeatable Leve" data-placement="right" data-container=\'body' : '' }}'>
		<span class='close' rel='tooltip' title='Leve Level'>{{ $leve->level }}</span>
		@if($recipe_id == 0)
		<a href='http://xivdb.com/?item/{{ $leve->item->id }}' class='item-name' target='_blank'>
		@else
		<a href='http://xivdb.com/?recipe/{{ $recipe_id }}' class='item-name' target='_blank'>
		@endif
			<img src='{{ assetcdn('item/' . $leve->item->icon . '.png') }}' width='36' height='36' style='margin-right: 10px;'>{{ $leve->item->name->term }}
		</a>
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
		<a href='/levequests/breakdown/{{ $leve->id }}' class='btn btn-default'>
			<img src='/img/xp.png' width='24' height='24'>
			{{ number_format($leve->xp) }}
		</a>
	</td>
	<td class='text-center reward valign'>
		<img src='/img/coin.png' width='24' height='24'>
		{{ number_format($leve->gil) }}
	</td>
	<td class='text-center location {{ preg_replace('/\W/', '', strtolower($leve->major_location)) }}'>
		<div>{{ ! empty($leve->location) ? $leve->location : '' }}</div>
		<div>{{ ! empty($leve->minor_location) ? $leve->minor_location : '' }}</div>
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
				{{ $reward->item->name->term }}
				@else
				<img src='/img/noitemicon.png' style='margin-right: 10px;'>
				{{ $reward->item_name }}
				<span class='label label-danger' rel='tooltip' title='See news post to help fill this out!' data-container='body'>Help</span>
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
		<button class='btn btn-default add-to-list' data-item-id='{{ $leve->item->id }}' data-item-name='{{{ $leve->item->name->term }}}' data-item-quantity='{{{ $leve->amount }}}'>
			<i class='glyphicon glyphicon-shopping-cart'></i>
			<i class='glyphicon glyphicon-plus'></i>
		</button>
	</td>
</tr>
@endforeach
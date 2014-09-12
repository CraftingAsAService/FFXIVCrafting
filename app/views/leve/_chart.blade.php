


<h2>
	{{ $leve->name }}
	@if($leve->triple)
	<i class='glyphicon glyphicon-fire text-danger' rel='tooltip' title='Triple Turnin!'></i>
	@endif
	<small>level {{ $leve->level }}</small>
</h2>

<h3>Rewards</h3>
<p class='xp-reward'>
	<img src='/img/xp.png' width='24' height='24'>
	{{ number_format($leve->xp) }}
	@if ($leve->xp_spread > 0)
	<span>&plusmn;<span>{{ number_format($leve->xp_spread) }}</span></span>
	@endif
</p>
<p class='gil-reward'>
	<img src='/img/coin.png' width='24' height='24'>
	{{ number_format($leve->gil) }}
	@if ($leve->gil_spread > 0)
	<span>&plusmn;<span>{{ number_format($leve->gil_spread) }}</span></span>
	@endif
</p>

<h3>Requires</h3>
<button class='btn btn-default pull-right add-to-list' data-item-id='{{ $leve->item->id }}' data-item-name='{{{ $leve->item->name->term }}}' data-item-quantity='{{{ $leve->amount }}}' rel='tooltip' title='Add to Crafting List'>
	<i class='glyphicon glyphicon-shopping-cart'></i>
	<i class='glyphicon glyphicon-plus'></i>
</button>
<p>
	<?php $recipe_id = count($leve->item->recipe) ? $leve->item->recipe[0]->id : 0; ?>
	<?php $url = $recipe_id == 0 ? 'item/' . $leve->item->id : 'recipe/' . $recipe_id; ?>
	<a href='http://xivdb.com/?{{ $url }}' class='item-name' target='_blank'><img src='{{ assetcdn('items/nq/' . $leve->item->id . '.png') }}' width='24' height='24' style='margin-right: 10px;'>{{ $leve->item->name->term }}</a>

	@if($leve->amount > 1)
	<span class='label label-primary' rel='tooltip' title='Amount Required' data-container='body'>
		x {{ $leve->amount }}
	</span>
	@endif
</p>

@if($leve->item->recipe)
<h3>Recipe</h3>

<div class='panel-group' id='accordion{{ $leve->id }}' style='margin-bottom: 0;'>
	<div class='panel panel-default'>
		<div class='panel-heading'>
			<h4 class='panel-title'>
				<a data-toggle='collapse' data-parent='#accordion{{ $leve->id }}' href='#collapse{{ $leve->id }}'>
					Show Recipe
				</a>
			</h4>
		</div>
	</div>
</div>
<div id='collapse{{ $leve->id }}' class='collapse'>
	<ul class='list-group'>
		@foreach($leve->item->recipe[0]->reagents as $reagent)
		<li class='list-group-item'>
			<a href='http://xivdb.com/?item/{{ $reagent->id }}' target='_blank'>
				<img src='{{ assetcdn('items/nq/' . $reagent->id . '.png') }}' width='36' height='36' class='margin-right'><span class='name'>{{ $reagent->name->term }}</span>
			</a>
			x {{ $reagent->pivot->amount * $leve->amount }}
			@if($leve->amount > 1)
			total
			@endif
		</li>
		@endforeach
		<li class='list-group-item'>
			<a href='/crafting/list?Item:::::{{ $leve->item->id }}'>View in crafting tool</a>
		</li>
	</ul>
</div>
				
@endif

<h3>Leveling Up</h3>

<p>
	Each Turnin of HQ items will grant a reward of <em>{{ number_format($leve->xp * 2) }} XP</em> and {{ number_format($leve->gil * 2) }} Gil.  
	You will make a gil profit if you can obtain the {{ $leve->amount }} items for less than {{ number_format(($leve->gil * 2) / $leve->amount) }} each.
</p>

<table class='table table-bordered table-striped'>
	<thead>
		<tr>
			<th class='text-center' rowspan='2'>Level</th>
			<th class='text-center' rowspan='2'>Requires</th>
			<th class='text-center' colspan='{{ 1 + ($leve->triple ? 1 : 0) + ($leve->amount > 1 ? 1 : 0) }}'>
				<img src='/img/NQ.png' width='24' height='24'>
				NQ
			</th>
			<th class='text-center' colspan='{{ 1 + ($leve->triple ? 1 : 0) + ($leve->amount > 1 ? 1 : 0) }}'>
				<img src='/img/HQ.png' width='24' height='24'>
				HQ
			</th>
		</tr>
		<tr>
			<th class='text-center'>Turnins</th>
			@if($leve->amount > 1)
			<th class='text-center'>Items</th>
			@endif
			@if($leve->triple)
			<th class='text-center'>Allotments</th>
			@endif
			<th class='text-center'>Turnins</th>
			@if($leve->amount > 1)
			<th class='text-center'>Items</th>
			@endif
			@if($leve->triple)
			<th class='text-center'>Allotments</th>
			@endif
		</tr>
	</thead>
	<tbody>
		@foreach($chart as $row)
		<tr class='{{ $row['level'] - 1 == $account['levels'][strtolower($leve->classjob->en_name->term)] ? 'success' : '' }}'>
			<td class='text-center nowrap'>{{ $row['level'] - 1 }} to {{ $row['level'] }}</td>
			<td class='text-center'>{{ number_format($row['requires']) }}</td>
			<td class='text-center'>{{ $row['turnins'] }}</td>
			@if($leve->amount > 1)
			<td class='text-center'>{{ $leve->amount * $row['turnins'] }}</td>
			@endif
			@if($leve->triple)
			<td class='text-center'>{{ ceil($row['turnins'] / 3) }}</td>
			@endif
			<td class='text-center'>{{ $row['hq_turnins'] }}</td>
			@if($leve->amount > 1)
			<td class='text-center'>{{ $leve->amount * $row['hq_turnins'] }}</td>
			@endif
			@if($leve->triple)
			<td class='text-center'>{{ ceil($row['hq_turnins'] / 3) }}</td>
			@endif
		</tr>
		@endforeach
	</tbody>
</table>

@if(isset($vs))
<a href='/levequests/breakdown/{{ $leve->id }}'>View this Leve solo</a>
@endif
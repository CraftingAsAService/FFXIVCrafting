
<h2>
	{{ $leve->name }}
	@if($leve->repeats)
	<i class='glyphicon glyphicon-fire text-danger' rel='tooltip' title='Repeatable Turnin!'></i>
	@endif
	<small>level {{ $leve->level }}</small>
</h2>

<h3>Rewards</h3>
<p class='xp-reward'>
	<img src='/img/xp.png' width='24' height='24'>
	{{ number_format($leve->xp) }}
</p>
<p class='gil-reward'>
	<img src='/img/coin.png' width='24' height='24'>
	{{ number_format($leve->gil) }}
</p>

<h3>Requires</h3>
<button class='btn btn-default pull-right add-to-list' data-item-id='{{ $leve->requirements[0]->id }}' data-item-name='{{{ $leve->requirements[0]->display_name }}}' data-item-quantity='{{{ $leve->requirements[0]->pivot->amount }}}' rel='tooltip' title='Add to Crafting List'>
	<i class='glyphicon glyphicon-shopping-cart'></i>
	<i class='glyphicon glyphicon-plus'></i>
</button>
<p>
	<a href='{{ xivdb_item_link() . $leve->requirements[0]->id }}' class='item-name xivdb-24-icon' target='_blank'><img src='{{ assetcdn('item/' . $leve->requirements[0]->icon . '.png') }}' width='24' height='24' style='margin-right: 10px;'>{{ $leve->requirements[0]->display_name }}</a>

	@if($leve->requirements[0]->pivot->amount > 1)
	<span class='label label-primary' rel='tooltip' title='Amount Required'>
		x {{ $leve->requirements[0]->pivot->amount }}
	</span>
	@endif
</p>

@if(count($leve->requirements[0]->recipes))
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
		@foreach($leve->requirements[0]->recipes[0]->reagents as $reagent)
		<li class='list-group-item'>
			<a href='{{ xivdb_item_link() . $reagent->id }}' target='_blank'>
				<img src='{{ assetcdn('item/' . $reagent->icon . '.png') }}' width='36' height='36' class='margin-right'><span class='name'>{{ $reagent->display_name }}</span>
			</a>
			x {{ $reagent->pivot->amount * $leve->requirements[0]->pivot->amount }}
			@if($leve->requirements[0]->pivot->amount > 1)
			total
			@endif
		</li>
		@endforeach
		<li class='list-group-item'>
			<a href='/crafting/item/{{ $leve->requirements[0]->id }}?self_sufficient=1' class='fix-self-sufficient'>View in crafting tool</a>
		</li>
	</ul>
</div>

@endif

@if ( ! empty($leve->location))
<h3>Location</h3>
<p>{{ $leve->location->name }}</p>
@endif

<h3>Leveling Up</h3>

<p>
	Each Turnin of HQ items will grant a reward of <em>{{ number_format($leve->xp * 2) }} XP</em> and {{ number_format($leve->gil * 2) }} Gil.
	You will make a gil profit if you can obtain the {{ $leve->requirements[0]->pivot->amount }} items for less than {{ number_format(($leve->gil * 2) / $leve->requirements[0]->pivot->amount) }} each.
</p>

<table class='table table-bordered table-striped'>
	<thead>
		<tr>
			<th class='text-center' rowspan='2'>Level</th>
			<th class='text-center' rowspan='2'>Requires</th>
			<th class='text-center' colspan='{{ 1 + ($leve->repeats ? 1 : 0) + ($leve->requirements[0]->pivot->amount > 1 ? 1 : 0) }}'>
				NQ
			</th>
			<th class='text-center' colspan='{{ 1 + ($leve->repeats ? 1 : 0) + ($leve->requirements[0]->pivot->amount > 1 ? 1 : 0) }}'>
				<img src='/img/hq-icon.png' width='20' height='20'>
				HQ
			</th>
		</tr>
		<tr>
			<th class='text-center'>Turnins</th>
			@if($leve->requirements[0]->pivot->amount > 1)
			<th class='text-center'>Items</th>
			@endif
			@if($leve->repeats)
			<th class='text-center'>Allotments</th>
			@endif
			<th class='text-center'>Turnins</th>
			@if($leve->requirements[0]->pivot->amount > 1)
			<th class='text-center'>Items</th>
			@endif
			@if($leve->repeats)
			<th class='text-center'>Allotments</th>
			@endif
		</tr>
	</thead>
	<tbody>
		@foreach($chart as $row)
		<tr class='{{ $row['level'] - 1 == $account['levels'][strtolower($leve->job_category->jobs[0]->name)] ? 'success' : '' }}'>
			<td class='text-center nowrap'>{{ $row['level'] }} to {{ $row['level'] + 1 }}</td>
			<td class='text-center'>{{ number_format($row['requires']) }}</td>
			<td class='text-center'>{{ $row['turnins'] }}</td>
			@if($leve->requirements[0]->pivot->amount > 1)
			<td class='text-center'>{{ $leve->requirements[0]->pivot->amount * $row['turnins'] }}</td>
			@endif
			@if($leve->repeats)
			<td class='text-center'>{{ ceil($row['turnins'] / ($leve->repeats + 1)) }}</td>
			@endif
			<td class='text-center'>{{ $row['hq_turnins'] }}</td>
			@if($leve->requirements[0]->pivot->amount > 1)
			<td class='text-center'>{{ $leve->requirements[0]->pivot->amount * $row['hq_turnins'] }}</td>
			@endif
			@if($leve->repeats)
			<td class='text-center'>{{ ceil($row['hq_turnins'] / ($leve->repeats + 1)) }}</td>
			@endif
		</tr>
		@endforeach
	</tbody>
</table>

@if(isset($vs))
<a href='/levequests/breakdown/{{ $leve->id }}'>View this Leve solo</a>
@endif
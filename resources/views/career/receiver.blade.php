@extends('app')

@section('meta')
	<meta name="robots" content="noindex,nofollow">
@stop

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/career.js') }}'></script>
@stop

@section('banner')
	<h1>{{ $job->name }}'s Receiver Career</h1>

	<p>The following class{{ count($jobs) > 1 ? 'es' : '' }} produce items for {{ $job->name }} between levels {{ $min_level }} and {{ $max_level }}:</p>
	<p>@foreach($jobs as $j) <span class='label label-default'>{{ $j }}</span> @endforeach</p>
@stop

@section('content')
<div class='table-responsive'>
	<table class='table table-bordered table-striped hide_quests' id='career-items-table'>
		<thead>
			<tr>
				<th class='invisible'>&nbsp;</th>
				<th class='text-left'>Recipe</th>
				<th class='text-center'>Amount Needed</th>
				{{-- @if($show_quests)
				<th class='text-center quest_amount' rel='tooltip' title='Gather this many extra for your quests!'>Quest</th>
				@endif --}}
				<th class='text-center'>Purchase</th>
				<th class='text-center valign' rel='tooltip' title='Add to Crafting List'>
					<i class='glyphicon glyphicon-shopping-cart'></i>
					<i class='glyphicon glyphicon-plus'></i>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php $total = 0; ?>
			@foreach($recipes as $recipe)
			<?php if($amounts[$recipe->id] == 1) continue; ?>
			<?php if(count($recipe->item->shops)) $total += round($amounts[$recipe->id] + .49) * $recipe->item->price; ?>
			<tr data-item-id='{{ $recipe->item_id }}'>
				<td width='24' class='valign'>
					<img src='/img/jobs/{{ strtoupper($recipe->job->abbr) }}.png' width='24' height='24'>
				</td>
				<td>
					@if(isset($recipe->recipe_level))
					<span class='close' rel='tooltip' title='Level'>{{ $recipe->recipe_level }}</span>
					@endif
					<a href='http://xivdb.com/?item/{{ $recipe->item_id }}' target='_blank'>
						<img src='{{ assetcdn('item/' . $recipe->item->icon . '.png') }}' width='36' height='36' style='margin-right: 5px;'>{{ $recipe->item->display_name }}
					</a>
				</td>
				<td class='valign text-center'>
					{{ number_format($amounts[$recipe->id] + .49) }}
				</td>
				{{-- @if($show_quests)
				<td class='valign text-center quest_amount'>
					@if(isset($recipe->quest_level) && $recipe->quest_level > 0)
						<img src='/img/{{ $recipe->quest_quality ? 'H' : 'N' }}Q.png' width='24' height='24' class='quest_marker' rel='tooltip' title='Level {{ $recipe->quest_level }} Quest{{ $recipe->quest_quality ? '<br>HQ Items required' : '' }}' data-html='true'>
						<span class='amount'>{{ number_format($recipe->quest_amount) }}</span>
					@endif
				</td>
				@endif --}}
				<td class='valign text-center'>
					@if(count($recipe->item->shops))
					<a href='#' class='btn btn-default click-to-view' data-type='shops' rel='tooltip' title='Available for {{ $recipe->item->price }} gil, Click to load Vendors'>
						<img src='/img/coin.png' width='24' height='24'>
						{{ number_format($recipe->item->price) }}
					</a>
					@endif
				</td>
				<td class='text-center valign'>
					<button class='btn btn-default add-to-list' data-item-id='{{ $recipe->item_id }}' data-item-name='{{{ $recipe->item->display_name }}}'>
						<i class='glyphicon glyphicon-shopping-cart'></i>
						<i class='glyphicon glyphicon-plus'></i>
					</button>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>

<p><small>Amounts are simply an estimate; the math should be correct but I'm not going to guarantee it.  In the least you will need more for Leves or failed syntheses.</small></p>

<p><small>Recipes requiring only one production are not shown.</small></p>

@if($total)
<p><small>If you were to purchase all of these items it would cost {{ number_format($total) }} gil.</small></p>
@endif

@stop
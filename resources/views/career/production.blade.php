@extends('app')

@section('meta')
	<meta name="robots" content="noindex,nofollow">
@stop

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/career.js') }}'></script>
@stop

@section('banner')
	<h1>{{ $job->name->term }}'s Producing Career</h1>

	<p>List supports the following class{{ count($jobs) > 1 ? 'es' : '' }} between levels {{ $min_level }} and {{ $max_level }}:</p>
	<p>@foreach($jobs as $j) <span class='label label-default'>{{ $j }}</span> @endforeach</p>
@stop

@section('content')
<div class='table-responsive'>
	<table class='table table-bordered table-striped hide_quests' id='career-items-table'>
		<thead>
			<tr>
				<th class='text-left'>Recipe</th>
				<th class='text-center'>Amount Needed</th>
				@if($show_quests)
				<th class='text-center quest_amount' rel='tooltip' title='Gather this many extra for your quests!' data-container='body'>Quest</th>
				@endif
				<th class='text-center'>Purchase</th>
				<th class='text-center valign' rel='tooltip' title='Add to Crafting List'>
					<i class='glyphicon glyphicon-shopping-cart'></i>
					<i class='glyphicon glyphicon-plus'></i>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php $total = 0; ?>
			@foreach($recipies as $recipe)
			<?php if($recipe->vendors) $total += round($recipe->amount + .49) * $recipe->min_price; ?>
			<tr>
				<td>
					@if(isset($recipe->job_level))
					<span class='close' rel='tooltip' title='Job Level'>{{ $recipe->job_level }}</span>
					@endif
					<a href='http://xivdb.com/?recipe/{{ $recipe->recipe_id }}' target='_blank'>
						<img src='{{ assetcdn('items/nq/' . $recipe->item_id . '.png') }}' width='36' height='36' style='margin-right: 5px;'>{{ $recipe->term }}
					</a>
				</td>
				<td class='valign text-center'>
					{{ number_format($recipe->amount + .49) }}
				</td>
				@if($show_quests)
				<td class='valign text-center quest_amount'>
					@if(isset($recipe->quest_level) && $recipe->quest_level > 0)
						<img src='/img/{{ $recipe->quest_quality ? 'H' : 'N' }}Q.png' width='24' height='24' class='quest_marker' rel='tooltip' title='Level {{ $recipe->quest_level }} Quest{{ $recipe->quest_quality ? '<br>HQ Items required' : '' }}' data-container='body' data-html='true'>
						<span class='amount'>{{ number_format($recipe->quest_amount) }}</span>
					@endif
				</td>
				@endif
				<td class='valign text-center'>
					@if($recipe->vendors)
					<a href='#' class='btn btn-default vendors' data-item-id='{{ $recipe->item_id }}' rel='tooltip' title='Available for {{ $recipe->min_price }} gil, Click to load Vendors'>
						<img src='/img/coin.png' width='24' height='24'>
						{{ number_format($recipe->min_price) }}
					</a>
					@endif
				</td>
				<td class='text-center valign'>
					<button class='btn btn-default add-to-list' data-item-id='{{ $recipe->item_id }}' data-item-name='{{{ $recipe->term }}}'>
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
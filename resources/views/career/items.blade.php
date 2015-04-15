@extends('app')

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/career.js') }}'></script>
@stop

@section('banner')
	<h1>
		@if ($job != 'BTL')
		{{ $job->name->term }}'s Gathering Career
		@else
		Battling Career
		@endif
	</h1>
	<p>List supports the following class{{ count($jobs) > 1 ? 'es' : '' }} between levels {{ $min_level }} and {{ $max_level }}:</p>
	<p>@foreach($jobs as $j) <span class='label label-default'>{{ $j }}</span> @endforeach</p>
@stop

@section('content')

<div class='table-responsive'>
	<table class='table table-bordered table-striped hide_quests' id='career-items-table'>
		<thead>
			<tr>
				<th class='text-left'>Item</th>
				<th class='text-center'>Amount Needed</th>
				@if($show_quests)
				<th class='text-center quest_amount' rel='tooltip' title='Gather this many extra for your quests!' data-container='body'>Quest</th>
				@endif
				@if($job == 'BTL')
				<th class='text-center'>Beasts</th>
				@elseif ($job->abbr->term != 'FSH')
				<th class='text-center'>Gathering</th>
				@endif
				<th class='text-center'>Purchase</th>
			</tr>
		</thead>
		<tbody>
			@foreach($items as $item)
			<?php if ($item->item_id < 30) continue; ?>
			<tr>
				<td>
					@if($item->level)
					<span class='close' rel='tooltip' title='Item Level'>{{ $item->level }}</span>
					@endif
					<a href='http://xivdb.com/?item/{{ $item->item_id }}' target='_blank'>
						<img src='{{ assetcdn('items/nq/' . $item->item_id . '.png') }}' width='36' height='36' style='margin-right: 5px;'>{{ $item->name }}
					</a>
				</td>
				<td class='valign text-center'>
					{{ number_format($item->amount + .49) }}
				</td>
				@if($show_quests)
				<td class='valign text-center quest_amount'>
					@if(isset($item->quest_level) && $item->quest_level > 0)
						<img src='/img/{{ $item->quest_quality ? 'H' : 'N' }}Q.png' width='24' height='24' class='quest_marker' rel='tooltip' title='Level {{ $item->quest_level }} Quest{{ $item->quest_quality ? '<br>HQ Items required' : '' }}' data-container='body' data-html='true'>
						<span class='amount'>{{ number_format($item->quest_amount) }}</span>
					@endif
				</td>
				@endif
				@if($job == 'BTL')
				<td class='text-center'>
					@if($item->beasts)
					<a href='#' class='btn btn-default beasts' data-item-id='{{ $item->item_id }}' rel='tooltip' title='Click to load Beasts'>
						<img src='/img/mob.png' width='24' height='24'>
						{{ number_format($item->beasts) }}
					</a>
					@endif
				</td>
				@elseif ($job->abbr->term != 'FSH')
				<td class='text-center'>
					@if ($item->nodes)
					<i class='class-icon class-id-{{ $job->id }} clusters' data-item-id='{{ $item->item_id }}'></i>
					@endif
				</td>
				@endif
				<td class='valign text-center'>
					@if($item->vendors)
					<a href='#' class='btn btn-default vendors' data-item-id='{{ $item->item_id }}' rel='tooltip' title='Available for {{ $item->min_price }} gil, Click to load Vendors'>
						<img src='/img/coin.png' width='24' height='24'>
						{{ number_format($item->min_price) }}
					</a>
					@endif
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>

@if($job != 'BTL' && $job->abbr->term != 'FSH')
<p><em><small>Shards not shown.</small></em></p>
@endif
<p><small>Amounts are simply an estimate; the math should be correct but I'm not going to guarantee it.  In the least you will need more for Leves or failed syntheses.</small></p>

@stop
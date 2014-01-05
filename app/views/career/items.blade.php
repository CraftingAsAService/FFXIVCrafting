@extends('layout')

@section('javascript')
<script type='text/javascript' src='http://xivdb.com/tooltips.js'></script>
	<script type='text/javascript'>
		var xivdb_tooltips = 
		{ 
			"language"      : "EN",
			"frameShadow"   : true,
			"compact"       : false,
			"statsOnly"     : false,
			"replaceName"   : false,
			"colorName"     : true,
			"showIcon"      : false,
		} 
	</script>
<!--<script type='text/javascript' src='/js/career.items.js'></script>-->
@stop

@section('content')

@if ($job != 'BTL')
<h1>{{ $job->name }}'s Gathering Career</h1>
@else
<h1>Battling Career</h1>
@endif

<p>List supports the following class{{ count($jobs) > 1 ? 'es' : '' }} between levels {{ $min_level }} and {{ $max_level }}:</p>
<p>@foreach($jobs as $j) <span class='label label-default'>{{ $j }}</span> @endforeach</p>

<div class='table-responsive'>
	<table class='table table-bordered table-striped hide_quests' id='career-items-table'>
		<thead>
			<tr>
				<th class='text-left'>Item</th>
				<th class='text-center'>Amount Needed</th>
				@if($show_quests)
				<th class='text-center quest_amount' rel='tooltip' title='Gather this many extra for your quests!' data-container='body'>Quest</th>
				@endif
				@if($job != 'BTL' && $job->abbreviation != 'FSH')
				<th class='text-center' width='400'>Locations</th>
				@endif
				<th class='text-center'>Buy</th>
				<th class='text-center'>Vendors</th>
			</tr>
		</thead>
		<tbody>
			@foreach($items as $item)
			<?php if ($item->id < 30) continue; ?>
			<tr data-role='{{ $item->role }}'>
				<td>
					@if(isset($item->ilvl))
					<span class='close' rel='tooltip' title='Item Level'>{{ $item->ilvl }}</span>
					@endif
					<a href='http://xivdb.com/?item/{{ $item->id }}' target='_blank'>
						<img src='/img/items/{{ $item->icon ?: '../noitemicon' }}.png' style='margin-right: 5px;'>{{ $item->name }}
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
				@if($job != 'BTL' && $job->abbreviation != 'FSH')
				<td>
					<div class='row'>
						@foreach($item->nodes as $location_name => $node)
						<div class='col-sm-6 text-right'>
							{{ $location_name }} 
						</div>
						<div class='col-sm-6'>
							@foreach($node as $action)
							<span class='label label-primary' rel='tooltip' title='{{ $action }}' data-container='body'>{{ $action }}</span>
							@endforeach
						</div>
						@endforeach
					</div>
				</td>
				@endif
				<td class='valign text-center'>
					@if($item->buy)
						<img src='/img/coin.png' class='stat-vendors pull-left' width='24' height='24'>
						{{ number_format($item->buy) }}
					@endif
				</td>
				<td class='valign text-center'>
					@if($item->buy)
					<button class='btn btn-default btn-sm' data-toggle='popover' data-container='body' data-html='true' data-placement='left' data-content-id='#vendors_for_{{ $item->id }}'>
						{{ $item->vendor_count }} Vendor{{ $item->vendor_count > 1 ? 's' : '' }} 
						@if($item->vendor_count > 1 && count($item->vendors) > 1)
						in {{ count($item->vendors) }} Area{{ count($item->vendors) > 1 ? 's' : '' }}
						@endif
					</button>
					<div class='hidden' id='vendors_for_{{ $item->id }}'>
						@foreach($item->vendors as $location_name => $vendors)
						<p>{{ $location_name }}</p>
						<ul>
							@foreach($vendors as $vendor)
							<li>
								<em>{{ $vendor->name }}</em>@if($vendor->title), {{ $vendor->title }}@endif
								@if($vendor->x && $vendor->y)
								<span class='label label-default' rel='tooltip' title='Coordinates' data-container='body'>{{ $vendor->x }}x{{ $vendor->y }}</span>
								@endif
							</li>
							@endforeach
						</ul>
						@endforeach
					</div>
					@endif
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>

@if($job != 'BTL' && $job->abbreviation != 'FSH')
<p><em><small>Shards not shown.</small></em></p>
@endif
<p><small>Amounts are simply an estimate; the math should be correct but I'm not going to guarantee it.  In the least you will need more for Leves or failed syntheses.</small></p>

@stop
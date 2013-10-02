@extends('layout')

@section('vendor-css')
	<link href='/css/bootstrap-tour.min.css' rel='stylesheet'>
@stop

@section('javascript')
<script type='text/javascript' src='http://xivdb.com/tooltips.js'></script>
<script type='text/javascript' src='/js/bootstrap-tour.min.js'></script>
<script type='text/javascript' src='/js/gathering.js'></script>
@stop

@section('content')

{{--
<a href='#' id='start_tour' class='start btn btn-primary pull-right' style='margin-top: 12px;'>
	<i class='glyphicon glyphicon-play'></i>
	Start Tour
</a>
--}}

<h1>{{ $job->name }} Gathering</h1>

<p>Want to know what to focus on when you're out there with your {{ $job->name }}? Assuming you want to make one of everything with the selected DOH classes and in the selected level ranges, this lists them all.  This is simply the minimum required.  Always remember to grab a few extra!</p>

@if(in_array($job->abbreviation, array('MIN','BTN')))
<p><em>Please Note:</em> The level 20 quest for 'Grade 1 Carbonized Matter x99' is not reflected in the list as it is not used in any crafts.</p>
@endif

<div class='row'>
	<div class='col-sm-3 selectors'>

		<h4>Options</h4>
		<div class='checkbox'>
			<label>
				<input type='checkbox' id='hide_shards' class='options' checked='checked'>
				Hide Shards
			</label>
		</div>
		<div class='checkbox'>
			<label>
				<input type='checkbox' id='hide_quests' class='options'>
				Hide Quests
			</label>
		</div>

		<h4>Classes</h4>
		@foreach($job_list as $abbreviation => $name)
		<div class='checkbox'>
			<label>
				<input type='checkbox' id='class_{{ $abbreviation }}' class='job_checkbox' checked='checked'>
				{{ $name }}
			</label>
		</div>
		@endforeach

		<h4>Disciple Levels</h4>
		@foreach($level_ranges as $start)
		<div class='checkbox'>
			<label>
				<input type='checkbox' id='level_{{ $start }}' class='level_checkbox' checked='checked' data-level='{{ $start }}'>
				{{ $start }} - {{ $start + 4 }}
			</label>
		</div>
		@endforeach

		<h4>Quest Details</h4>
		@foreach($quests as $quest)
		<div>
			Lv.{{ $quest->level }} 
			{{ $quest->item->name }}
			x{{ $quest->amount }}
			{{ $quest->quality ? '(HQ)' : '' }}
		</div>
		@endforeach
	</div>
	<div class='col-sm-9'>
		<div class='table-responsive'>
			<table class='table table-bordered' id='gathering-table'>
				<thead>
					<tr>
						<th class='text-left'>Item</th>
						<th class='text-center'>Amount Needed</th>
						<th class='text-center'>Locations</th>
						<th class='text-center'>Each</th>
						<th class='text-center'>Buy All</th>
						<th class='invisible'>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					{{-- @foreach(range(5,50,5) as $range_level) --}}
					@foreach(range(1,75) as $item_level)
					@foreach($items as $item_id => $item)
					<?php if ($item['data']->level != $item_level) continue; ?>
					{{-- <?php if($item['data']->locations[0]->pivot->level != $range_level) continue; ?> --}}
					<tr class='{{ 100 > $item_id ? 'hidden shard shard-hidden' : '' }}{{ isset($item['data']->quest[0]) ? ' quest' : '' }}'>
						<td class='text-left'>
							<span class='close' rel='tooltip' title='Item Level'>{{ $item_level }}</span>
							<a href='http://xivdb.com/{{ $item['data']->href }}' target='_blank'>
								<img src='/img/items/{{ $item['data']->icon ?: '../noitemicon.png' }}' style='margin-right: 5px;'>{{ $item['data']->name }}
							</a>
						</td>
						<td class='text-center amount_needed' data-level='{{ $item_level }}' data-additional='{{ isset($item['data']->quest[0]) ? $item['data']->quest[0]->amount : 0 }}'>
							@if(isset($item['data']->quest[0]))
							<img src='/img/{{ $item['data']->quest[0]->quality ? 'H' : 'N' }}Q.png' style='position: absolute; right: 5px' rel='tooltip' title='{{ $item['data']->quest[0]->amount }}{{ $item['data']->quest[0]->quality ? ' (HQ)' : '' }} are for the Guildmaster at level {{$item['data']->quest[0]->level}}{{ $item['data']->quest[0]->notes ? ', see left for note' : '' }}' width='24' height='24'>
							@endif
							<span>{{ number_format($item['tally'] + (isset($item['data']->quest[0]) ? $item['data']->quest[0]->amount : 0)) }}</span>
						</td>
						<td class='text-left'>
							<div class='location'>
								<span class='close' rel='tooltip' title='Node Level'>
									{{ $item['data']->locations[0]->pivot->level }}
								</span>
								{{ $item['data']->locations[0]->name }}
								@if(count($item['data']->locations) > 1)
								<span class='faux_link and_more' data-container='body' data-toggle='popover' data-placement='bottom' data-content='
									@foreach($item['data']->locations as $location)
									<div class="location">
										{{ $location->name }} (Lv.{{ $location->pivot->level }})
									</div>
									@endforeach
								' data-html='true'>
									and more
								</span>
								@endif
							</div>
						</td>
						<td class='text-right' style='white-space: nowrap;'>
							@if($item['data']->gil)
							<img src='/img/coin.png' class='stat-vendors pull-left' width='24' height='24'>
							{{ number_format($item['data']->gil) }}
							@endif
						</td>
						<td class='text-right total_cost' style='white-space: nowrap;' data-per='{{ $item['data']->gil }}'>
							@if($item['data']->gil)
							<img src='/img/coin.png' class='stat-vendors pull-left' width='24' height='24'>
							<span>{{ number_format($item['data']->gil * $item['tally']) }}</span>
							@endif
						</td>
						<td>
							<button class='btn btn-default pull-right glyphicon glyphicon-chevron-up collapse' style='color: #999;' rel='tooltip' title='View Breakdown'></button>
						</td>
					</tr>
					<tr class='hidden'>
						<td colspan='6'>
							<table class='table table-bordered breakdown'>
								<thead>
									<td class='invisible'>&nbsp;</td>
									@foreach($job_list as $abbreviation => $name)
									<td class='text-center class_{{ $abbreviation }}'>
										<img src='/img/classes/{{ $abbreviation }}.png' rel='tooltip' title='{{ $name }}'>
									</td>
									@endforeach
								</thead>
								<tfoot class='hidden'></tfoot>
								<tbody>
									@foreach($level_ranges as $start)
									<tr class='level_{{ $start }}' data-level='{{ $start }}'>
										<th class='text-right'>
											{{ $start }} - {{ $start + 4 }}
										</th>
										@foreach(array_keys($job_list) as $abbreviation)
										<td class='text-center class_{{ $abbreviation }}'>{{ isset($item['breakdown'][$start][$abbreviation]) ? $item['breakdown'][$start][$abbreviation] : '-' }}</td>
										@endforeach
									</tr>
									@endforeach
								</tbody>
							</table>
						</td>
					</tr>
					@endforeach
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>


@stop
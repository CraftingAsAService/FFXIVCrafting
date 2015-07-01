@extends('app')

@section('banner')
	<h1>{{ $classjob->name->term }} Gear Profile</h1>
	<h2>A look at level {{ $start_level }}</h2>
@stop

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/gear-profile.js') }}'></script>
@stop

@section('content')

	@foreach ($gear as $slot => $levels)
	<table class='gear table table-striped table-hover table-condensed table-curved'>
		<colgroup>
			<col width='20px'>
			<col>
			@foreach ($stat_focus as $stat)
			<col width='50px'>
			@endforeach
			<col width='50px'>
			<col width='60px'>
			<col width='80px'>
		</colgroup>
		<thead>
			<tr>
				<th class='slot-icon' colspan='2'>
					<img src='/img/equipment/{{ $slot }}.png'>
					{{ $slot }}
				</th>
				@foreach ($stat_focus as $stat)
				<th class='stat' rel='tooltip' title='{{ $stat }}'>
					<img src="/img/stats/nq/{{ $stat }}.png" class="stat-icon">
				</th>
				@endforeach
				<th class='stat materia' rel='tooltip' title='Materia'>
					<img src="/img/stats/nq/Materia.png" class="stat-icon">
				</th>
				<th class='obtained' rel='tooltip' title='Obtained From'><i class='glyphicon glyphicon-gift'></i></th>
				<th class='cart' rel='tooltip' title='Add to Crafting List'><i class='glyphicon glyphicon-shopping-cart'></i></th>
			</tr>
		</thead>
		@foreach ($levels as $level => $bucket)
		<tbody>
			<?php $i = 0; ?>
			@foreach (['nq', 'hq'] as $quality)
			<?php if ( ! isset($bucket[$quality])) continue; ?>
			@foreach ($bucket[$quality] as $item)
			{{-- Less than the LCD, opaque --}}
			{{-- Is LCD, and LCD != real starting level, warning color --}}
			{{-- Is LCD, and LCD == real starting level, success color --}}
			<tr class='{{ 
				$level < $bucket['lcd'] 
					? 'opaque' 
					: (
						$item->{$quality . '_worth'} == $bucket['bis_worth'] 
						? (
							$level == $start_level 
							? 'success' 
							: ($level == $bucket['lcd'] ? 'info' : '')
						)
						: ''
					) 
			}}' data-item-id='{{ $item->id }}'>
				@if($i++ == 0)
				<td class='level{{ 
					$level == $start_level
					? ' success' 
					: ($level == $bucket['lcd'] ? ' info' : '')
				}}' rowspan='{{ count($bucket['nq']) + (isset($bucket['hq']) ? count($bucket['hq']) : 0) }}'>
					{{ $level }}
				</td>
				@endif
				<td class='item'>
					<span class='ilvl'>{{ $item->level }}</span>
					<a href='http://xivdb.com/?item/{{ $item->id }}' target='_blank'>
						<img src='{{ assetcdn('items/' . $quality . '/' . $item->id . '.png') }}' width='20' height='20' class='item-icon {{ $quality }}'>{{ $item->name->term }}
					</a>
					{{--
					@if ($item->{$quality . '_worth'} == $bucket['bis_worth'])
					<i class='glyphicon glyphicon-fire' rel='tooltip' title='Best in Slot for Level'></i>
					@endif
					--}}
				</td>
				@foreach ($stat_focus_ids as $stat_id)
					<?php $use = null; ?>
					@foreach ($item->baseparam as $baseparam)
					@if ($baseparam->id == $stat_id)
					<?php $use = (int) $baseparam->pivot->{$quality . '_amount'}; ?>
					@endif
					@endforeach
				<td class='stat @if($item->rarity == 7 && ! $use)aetherial' rel='tooltip' title='Aetherial items have random stats.@endif'>@if($item->rarity == 7 && ! $use)<span></span>@else{{ $use }}@endif</td>
				@endforeach
				<td class='stat materia'>
					@if($item->materia > 0)
					<img src="/img/{{ $item->materia }}.png" class="stat-icon" rel='tooltip' title='{{ $item->materia }} materia {{ $item->materia == 1 ? 'slot' : 'slots' }} available'>
					@endif
				</td>
				<td class='obtained'>
					@if(($item->rewarded || $item->achievable) && $quality != 'hq')
					<span class='rewarded'>
						<img src='/img/reward.png' class='rewarded' width='20' height='20' rel='tooltip' title='Reward from {{ $item->achievable ? 'an Achievement' : 'a Quest' }}'>
					</span>
					@endif
					@if(count($item->vendors) && $quality != 'hq')
					<span class='gil'>
						<img src='/img/coin.png' class='vendors' width='24' height='24' rel='tooltip' title='Available for {{ $item->min_price }} gil, Click to load Vendors'>
					</span>
					@endif
					@if(count($item->dungeon_drop))
					<span class='dungeon'>
						<img src='/img/dungeon.png' class='dungeon_drop' width='24' height='24' rel='tooltip' title='Dungeon Reward'>
					</span>
					@endif
				</td>
				<td class='cart'>
					@if(count($item->recipe))
					<img src='/img/jobs/{{ $item->recipe[0]->classjob->en_abbr->term }}.png' width='24' height='24' rel='tooltip' title='Crafted By {{ $item->recipe[0]->classjob->name->term }}'></i>
					<button class='btn btn-default btn-xs add-to-list success-after-add' data-item-id='{{ $item->id }}' data-item-name='{{ $item->name->term }}'>
						<i class='glyphicon glyphicon-shopping-cart'></i>
						<i class='glyphicon glyphicon-plus'></i>
					</button>
					@endif
				</td>
			</tr>
			@endforeach
			@endforeach
		</tbody>
		@endforeach
	</table>
	@endforeach

	<a href='/gear?job={{ $classjob->en_abbr->term }}&amp;level={{ $start_level }}&amp;options={{ implode(',', $options) }}' class='btn btn-primary pull-right'>Select Another Profile</a>
	
@stop
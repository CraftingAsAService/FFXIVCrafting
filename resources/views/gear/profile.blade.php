@extends('app')

@section('banner')
	<h1>{{ $job->name }} Gear Profile</h1>
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
			<col width='94px'>
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
					<img src="/img/stats/{{ $stat }}.png" class="stat-icon">
				</th>
				@endforeach
				<th class='stat materia' rel='tooltip' title='Materia'>
					<img src="/img/stats/Materia.png" class="stat-icon">
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
							$level == $start_level || $level == $bucket['lcd']
							? 'success' : ''
						)
						: ''
					)
			}}' data-item-id='{{ $item->id }}'>
				@if($i++ == 0)
				<td class='level{{
					$level == $start_level || $level == $bucket['lcd']
					? ' success' : ''
				}}' rowspan='{{ count($bucket['nq']) + (isset($bucket['hq']) ? count($bucket['hq']) : 0) }}'>
					{{ $level }}
				</td>
				@endif
				<td class='item'>
					<span class='ilvl'>{{ $item->ilvl }}</span>
					<a href='{{ item_link() . $item->id }}' target='_blank'>
						<span class='overlay-container'>
						@if($quality == 'hq')
						<img src='/img/hq-overlay.png' width='20' height='20' class='hq-overlay'>
						@endif
						<img src='{{ icon($item->icon) }}' width='20' height='20' class='item-icon'>
						</span>{{ $item->display_name }}
					</a>
					{{--
					@if ($item->{$quality . '_worth'} == $bucket['bis_worth'])
					<i class='glyphicon glyphicon-fire' rel='tooltip' title='Best in Slot for Level'></i>
					@endif
					--}}
				</td>
				@foreach ($stat_focus_ids as $stat_id)
					<?php $use = null; ?>
					@foreach ($item->attributes as $attribute)
					@if ($attribute->attribute == $stat_id && $attribute->quality == $quality)
					<?php $use = (int) $attribute->amount; ?>
					@endif
					@endforeach
				<td class='stat {{ $stat_id }} @if($item->rarity == 7 && ! $use)aetherial' rel='tooltip' title='Aetherial items have random stats.@endif'>@if($item->rarity == 7 && ! $use)<span></span>@else{{ $use }}@endif</td>
				@endforeach
				<td class='stat materia'>
					@if($item->sockets > 0)
					<img src="/img/{{ $item->sockets }}.png" class="stat-icon" rel='tooltip' title='{{ $item->sockets }} materia {{ $item->sockets == 1 ? 'slot' : 'slots' }} available'>
					@endif
				</td>
				<td class='obtained'>
					@if($quality != 'hq')
						@if( ! $item->shops->isEmpty())
					<span class='gil'>
						<img src='/img/shop.png' class='click-to-view' data-type='shops' width='24' height='24' rel='tooltip' title='Purchase for {{ number_format($item->price, 0, '.', ',') }} gil, Click to View'>
					</span>
						@endif
						@if($item->quests)
					<span class='gil'>
						<img src='/img/quest.png' class='click-to-view' data-type='quests' width='24' height='24' rel='tooltip' title='Quest Reward, Click to View'>
					</span>
						@endif
						@if($item->leves)
					<span class='gil'>
						<img src='/img/leve_icon.png' class='click-to-view' data-type='leves' width='24' height='24' rel='tooltip' title='Leve Reward, Click to View'>
					</span>
						@endif
						@if($item->mobs)
					<span class='gil'>
						<img src='/img/mob.png' class='click-to-view' data-type='mobs' width='24' height='24' rel='tooltip' title='Drops from a Monster, Click to View'>
					</span>
						@endif
						@if( ! $item->instances->isEmpty())
					<span class='gil'>
						<img src='/img/dungeon.png' class='click-to-view' data-type='instances' width='24' height='24' rel='tooltip' title='Instance Coffer Loot, Click to View'>
					</span>
						@endif
						@if( ! $item->achievements->isEmpty())
					<span class='gil'>
						<img src='{{ icon($item->achievements[0]->icon) }}' width='24' height='24' rel='tooltip' title='Achievement Reward: {{ $item->achievements[0]->name }}'>
					</span>
						@endif
					@endif
				</td>
				<td class='cart'>
					@if( ! $item->recipes->isEmpty())
					<img src='/img/jobs/{{ $item->recipes[0]->job->abbr }}.png' width='24' height='24' rel='tooltip' title='Crafted By {{ $item->recipes[0]->job->name }}'></i>
					<button class='btn btn-default btn-xs add-to-list success-after-add' data-item-id='{{ $item->id }}' data-item-name='{{ $item->display_name }}'>
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

	<nav class='text-center'>
		<ul class="pagination pagination">
			@foreach(range($start_level - 5, $start_level + 5) as $level)
			<?php if ($level < 1 || $level > config('site.max_level')) continue; ?>
			<li class='{{ $level == $start_level ? 'active' : '' }}'>
				@if($level == $start_level)
				<span>{{ $level }}</span>
				@else
				<a href="{{ $level }}?{{ $_SERVER['QUERY_STRING'] }}">{{ $level }}</a>
				@endif
			</li>
			@endforeach
			{{-- <li><a href="#">{{ $start_level }}</a></li> --}}
		</ul>
	</nav>

	<a href='/gear?job={{ $job->abbr }}&amp;level={{ $start_level }}&amp;options={{ implode(',', $options) }}' class='btn btn-primary pull-right'>Select Another Profile</a>

@stop
@extends('layout')

@section('javascript')
<script type='text/javascript' src='http://xivdb.com/tooltips.js'></script>
<script type='text/javascript' src='/js/calculate.js'></script>
@stop

@section('content')

<h1>Gear for a Level {{ $level }} {{ $job->name }}</h1>

<table class='table table-bordered table-striped'>
	<thead>
		<tr>
			<th class='invisible'>&nbsp;</th>
			@foreach(array_keys($equipment) as $th_level)
			<?php if ($th_level > 50) continue; ?>
			<th class='text-center alert alert-{{ $th_level == $level ? 'success' : ($th_level < $level ? 'info' : 'warning') }}{{ $th_level == $kill_column ? ' hidden' : '' }}'>
				Level
				{{ $th_level }}
			</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		@foreach($slots as $slot)
		<tr>
			<td class='text-center'>
				<img src='/img/equipment/{{ $slot->name }}.png' class='equipment-icon' rel='tooltip' title='{{ $slot->name }}'>
				<div>
					<strong>{{ $slot->name }}</strong>
				</div>
			</td>
			@foreach(array_keys($equipment) as $td_level)
			<?php if ($td_level > 50) continue; ?>
			<?php $new = isset($changes[$td_level][$slot->name]); ?>
			<td class='{{ $new ? ('alert alert-' . ($td_level == $level ? 'success' : ($td_level < $level ? 'info' : 'warning'))) : '' }}{{ $td_level == $kill_column ? ' hidden' : '' }}'>
				<div class='items'>
					@foreach($equipment[$td_level][$slot->name] as $key => $item)
					<div class='item clearfix{{ $key > 0 ? ' hidden' : ' active' }}'>
						<div class='obtain-box pull-right'>
							@if($item->crafted_by)
							<div>
								<img src='/img/classes/{{ $item->crafted_by }}.png' class='stat-crafted_by pull-right' rel='tooltip' title='Crafted By {{ $job_list[$item->crafted_by] }}' width='24' height='24'>
							</div>
							@endif
							@if($item->vendors)
							<div>
								<img src='/img/coin.png' class='stat-vendors pull-right' rel='tooltip' title='Available from {{ $item->vendors }} vendor{{ $item->vendors != 1 ? 's' : '' }} for {{ number_format($item->gil) }} gil' width='24' height='24'>
							</div>
							@endif
						</div>
						
						<div class='name-box'>
							<a href='http://xivdb.com/{{ $item->href }}' target='_blank' class='text-primary'>{{ $item->name }}</a>
						</div>

						<div class='stats-box row{{ ! $new ? ' hidden' : '' }}'>
							@foreach($item->stats as $stat => $amount)
							<div class='col-sm-6 text-center stat{{ ! in_array($disciple_focus[$stat], array('ALL', $job->disciple)) ? ' hidden always_hidden' : '' }}' data-stat='{{ $stat }}' data-amount='{{ $amount }}'>
								<span>{{ $amount }}</span> &nbsp; 
								<img src='/img/stats/{{ $stat }}.png' class='stat-icon' rel='tooltip' title='{{ $stat }}'>
							</div>
							@endforeach
						</div>
					</div>
					@endforeach
				</div>
				@if(count($equipment[$td_level][$slot->name]) > 1)
				<div class='td-navigation-buffer'></div>
				<div class='btn-group btn-group-xs td-navigation' rel='tooltip' title='More than one option!<br>Navigate Options' data-html='true' data-placement='bottom'>
					<button type='button' class='btn btn-default previous'>&lt;&lt;</button>
					<button type='button' class='btn btn-default disabled'><span class='current'>1</span> / <span class='total'>{{ count($equipment[$td_level][$slot->name]) }}</span></button>
					<button type='button' class='btn btn-default next'>&gt;&gt;</button>
				</div>
				@endif
			</td>
			@endforeach
		</tr>
		@endforeach
	</tbody>
</table>

<div class='well'>
	<p>
		<strong>Crafting as a Service</strong> couldn't have done it without these resources.  Please support them!
	</p>
	<p>
		<small><em>
			<a href='http://xivdb.com/' target='_blank'>XIVDB</a>, for their tooltips and data.
			<a href='http://game-icons.net/' target='_blank'>Game-icons.net</a> for some of the icons.
			And of course <a href='http://www.finalfantasyxiv.com/' target='_blank'>Square Enix</a>.
		</em></small>
	</p>
</div>

@stop
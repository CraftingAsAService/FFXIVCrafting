@extends('app')

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/gear.js') }}'></script>
@stop

@section('banner')
	<h1>
		<i class='class-icon {{ $job->abbr->term }} large' style='position: relative; top: 5px;'></i>
		{{ $job->name->term }}
	</h1>
	<h2>Equipment Guide</h2>
@stop

@section('content')

	<p>
		Jump To: <a href='#'>TODO</a>, <a href='#'>TODO</a>
	</p>
	<p>
		TODO <a href='#'>Collapse All</a>, <a href='#'>Expand All</a>
	</p>

	@foreach($roles as $key => $role)
	<?php 
		$title = $img = $role; 
		if ($img == 'Main Hand') $img = 'Primary';
		if ($img == 'Off Hand') $img = 'Secondary';
		if ($img == 'Right Ring') $img = 'Ring';
		if ($key == 12 && $img == 'Ring') $title = 'Left Ring';
	?>
	<div class='panel-group' id='{{ strtolower(str_replace(' ', '', $role)) }}'>
		<div class='panel panel-default'>
			<a class='panel-heading block' data-toggle='collapse' data-parent='{{ strtolower(str_replace(' ', '', $role)) }}' href='#collapse{{ $key }}'>
				<img src='/img/equipment/{{ $img }}.png' width='24' height='24' class='margin-right equipment-icon'>{{ $title }}
			</a>
			<div id='collapse{{ $key }}' class='panel-collapse collapse in'>
				@if(isset($gear[$role]))
				<table class='table table-striped'>
					<thead>
						<tr>
							<th>Item</th>
							@foreach($gear_focus as $stat)
							<th class='text-center'>
								<img src='/img/stats/nq/{{ $stat }}.png' width='20' height='20' class='equipment-icon' rel='tooltip' title='{{{ $stat }}}'>
							</th>
							@endforeach
							<th class='text-center'>
								<img src='/img/stats/nq/Materia.png' width='20' height='20' class='equipment-icon' rel='tooltip' title='Materia Slots'>
							</th>
							<th class='text-center'>
								Buy
							</th>
							<th class='text-center'>
								Craft
							</th>
						</tr>
					</thead>
					<tbody>
						@foreach($gear[$role] as $item_id => $item)
						<?php $qualities = $item->has_hq ? array('hq', 'nq') : array('nq'); ?>
						@foreach($qualities as $quality)
						<tr data-item-id='{{ $item->id }}' data-item-ilvl='{{ $item->level }}' data-cannot-equip='{{{ $item->cannot_equip }}}'>
							<td>
								<small class='pull-right opaque'>lvl {{ $item->equip_level }}</small>
								<a href='http://xivdb.com/?item/{{ $item->id }}' target='_blank'><img src='{{ assetcdn('items/' . $quality . '/' . $item->id . '.png') }}' width='24' height='24' class='main-icon margin-right'>{{ $item->name->term }}</a>
							</td>
							@foreach($gear_focus_ids as $stat_id)
							<th class='text-center'>
								@foreach($item->baseparam as $baseparam)
								<?php if ($baseparam->id != $stat_id) continue; ?>

								{{ number_format($baseparam->pivot->{$quality . '_amount'}) }}

								<?php break; ?>
								@endforeach
							</th>
							@endforeach
							<th class='text-center'>
								{{ $item->materia }}
							</th>

						</tr>
						@endforeach
						@endforeach
					</tbody>
				</table>
				@else
				<div class='panel-body'>
					<p>No {{ $title }} Results</p>
				</div>
				@endif
			</div>
		</div>
	</div>
	@endforeach

	<fieldset>
		<legend>Stats Legend</legend>

		<p>icon descriptions</p>
	</fieldset>
@stop
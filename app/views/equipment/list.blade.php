@extends('layout')

@section('vendor-css')
	<link href='/css/bootstrap-switch.css' rel='stylesheet'>
@stop

@section('javascript')
	<script type='text/javascript' src='http://xivdb.com/tooltips.js'></script>
	<script type='text/javascript' src='/js/equipment.js'></script>
	<script type='text/javascript' src='/js/bootstrap-switch.js'></script>
@stop

@section('content')

<h1>
	Gear for a Level {{ $level }} {{ $job->name }}
</h1>

@if($craftable_only && $first_time)
<div class='alert alert-warning alert-dismissable'>
	You're currently seeing a restricted list that only shows craftable items.
	To view all items use the options at the <a href='#options'>bottom</a>.<br>

	<small><em>This is the only time you'll see this message.</em></small>
	<a href='#' class='small' data-dismiss="alert" aria-hidden="true">Close</a>
</div>
@endif

<table class='table table-bordered table-striped' id='gear'>
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
	<tfoot>
		<tr>
			<th class='invisible'>&nbsp;</th>
			@foreach(array_keys($equipment) as $th_level)
			<?php if ($th_level > 50) continue; ?>
			<th class='text-center alert alert-{{ $th_level == $level ? 'success' : ($th_level < $level ? 'info' : 'warning') }}{{ $th_level == $kill_column ? ' hidden' : '' }}'>
				<div class='stats-box row'>
					test
				</div>
			</th>
			@endforeach
		</tr>
	</tfoot>
	<tbody>
		@foreach($slots as $slot)
		<tr>
			<td class='text-center no-summary'>
				<img src='/img/equipment/{{ $slot->name }}.png' class='equipment-icon' rel='tooltip' title='{{ $slot->name }}'>
				<div>
					<strong>{{ $slot->name }}</strong>
				</div>
			</td>
			@foreach(array_keys($equipment) as $td_level)
			<?php if ($td_level > 50) continue; ?>
			<?php $new = isset($changes[$td_level][$slot->name]); ?>
			<td class='{{ $new ? ('alert alert-' . ($td_level == $level ? 'success' : ($td_level < $level ? 'info' : 'warning'))) : '' }}{{ $td_level == $kill_column ? ' hidden no-summary' : '' }}'>
				<div class='items'>
					@foreach($equipment[$td_level][$slot->name] as $key => $item)
					<div class='item clearfix {{ $key > 0 ? 'hidden' : 'active' }}{{ $item->crafted_by ? ' craftable' : '' }}' data-item-id='{{ $item->id }}'>
						<div class='obtain-box pull-right'>
							@if($item->crafted_by)
							@foreach(explode(',', $item->crafted_by) as $crafted_by)
							<div>
								<img src='/img/classes/{{ $crafted_by }}.png' class='stat-crafted_by pull-right' rel='tooltip' title='Crafted By {{ $job_list[$crafted_by] }}' width='24' height='24'>
							</div>
							@endforeach
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
							<div class='col-sm-6 text-center stat{{ ! in_array($stat, $job_focus) ? ' hidden' : '' }}' data-stat='{{ $stat }}' data-amount='{{ $amount }}'>
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
		
<div class='panel panel-primary'>
	<div class='panel-heading'>
		<h3 class='panel-title'>Stat Display</h3>
	</div>
	<div class='panel-body'>
		<div class='row stat-displayer'>
			@foreach($visible_stats as $stat)
			<div class='col-xs-6 col-sm-3 col-lg-2'>
				<span class='stat-toggle {{ ! in_array($stat, $job_focus) ? ' opaque' : '' }}' rel='tooltip' title='Click to toggle' data-stat='{{ $stat }}'>
					<img src='/img/stats/{{ $stat }}.png' class='stat-icon'>
					{{ $stat }}
				</span>
			</div>
			@endforeach
		</div>
		@if($stats_to_avoid)
		<hr>
		<strong>Gear with these stats were omitted:</strong>

		<div class='row'>
			@foreach($stats_to_avoid as $stat)
			<div class='col-xs-6 col-sm-3 col-lg-2'>
				<span class='stat-toggle' rel='tooltip' title='Click to toggle' data-stat='{{ $stat }}'>
					<img src='/img/stats/{{ $stat }}.png' class='stat-icon'>
					{{ $stat }}
				</span>
			</div>
			@endforeach
		</div>
		@endif
	</div>
</div>

<div class='panel panel-success'>
	<div class='panel-heading'>
		<h3 class='panel-title'>Craft These Items</h3>
	</div>
	<div class='panel-body'>
		<div class='row'>
			<div class='col-sm-3'>
				<div class='form-group'>
					<label for='craft_level'>Level</label>
					<select id='craft_level' class='form-control' required='required'>
						<option value='all'>All visible levels</option>
						<?php $i = 3; ?>
						@foreach(array_keys($equipment) as $craft_level)
						<?php if ($craft_level == $kill_column) continue; ?>
						<option value='{{ $i++ }}'{{ $craft_level == $level ? ' selected="selected"' : '' }}>{{ $craft_level }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class='col-sm-3'>
				<div class='form-group'>
					<label for='craft_slot'>Slot</label>
					<select id='craft_slot' class='form-control' required='required'>
						<option value='all'>All slots</option>
						<?php $i = 1; ?>
						@foreach($slots as $slot)
						<option value='{{ $i++ }}'>{{ $slot->name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class='col-sm-3'>
				<div class='form-group'>
					<label for='craft_status'>Status</label>
					<select id='craft_status' class='form-control' required='required'>
						<option value='new'>New Crafts</option>
						<option value='all'>Everything</option>
					</select>
				</div>
			</div>
			<div class='col-sm-3'>
				<div class='form-group'>
					<label>&nbsp;</label>
					<button type='submit' id='craft_these' class='btn btn-success form-control'>Let's go!</button>
				</div>
			</div>
		</div>
	</div>
</div>

<a name='options'></a>

<div class='panel panel-info'>
	<div class='panel-heading'>
		<h3 class='panel-title'>Switch</h3>
	</div>
	<div class='panel-body'>
		<form action='/equipment' method='post' role='form' class='form-horizontal'>
			<input type='hidden' name='class' value='{{ $job->abbreviation }}'>
			<input type='hidden' name='level' value='{{ $level }}'>
			<input type='hidden' name='forecast' value='{{ $forecast }}'>
			<input type='hidden' name='hindsight' value='{{ $hindsight }}'>
			<label>
				Only Craftable
			</label>
			<div class='make-switch' data-on='success' data-off='warning'>
				<input type='checkbox' id='craftable_only_switch' name='craftable_only' value='1' {{ $craftable_only ? " checked='checked'" : '' }}>
			</div>
			<small><em>* Refreshes page</em></small>
		</form>
	</div>
</div>

<div class='panel panel-info'>
	<div class='panel-heading'>
		<h3 class='panel-title'>Switch Class</h3>
	</div>
	<div class='panel-body'>
		<form action='/equipment' method='post' role='form' class='form-horizontal'>

			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Disciples of the Hand &amp; Land</label>
				<div class='col-sm-8 col-md-9'>
					<div class='btn-group'>
						@foreach(array('CRP','BSM','ARM','GSM','LTW','WVR','ALC','CUL') as $switch_job)
						<button type='submit' name='class' value='{{ $switch_job }}' class='btn btn-primary'>
							<img src='/img/classes/{{ $switch_job }}.png' rel='tooltip' title='{{ $job_list[$switch_job] }}'>
						</button>
						@endforeach
					</div>
					<div class='btn-group'>
						@foreach(array('MIN','BTN','FSH') as $switch_job)
						<button type='submit' name='class' value='{{ $switch_job }}' class='btn btn-info'>
							<img src='/img/classes/{{ $switch_job }}.png' rel='tooltip' title='{{ $job_list[$switch_job] }}'>
						</button>
						@endforeach
					</div>
				</div>
			</div>

			<div class='form-group'>
				<label class='col-sm-4 col-md-3 control-label'>Disciples of War &amp; Magic</label>
				<div class='col-sm-8 col-md-9'>
					<div class='btn-group'>
						@foreach(array('GLA', 'PGL', 'MRD', 'LNC', 'ARC') as $switch_job)
						<button type='submit' name='class' value='{{ $switch_job }}' class='btn btn-danger'>
							<img src='/img/classes/{{ $switch_job }}.png' rel='tooltip' title='{{ $job_list[$switch_job] }}'>
						</button>
						@endforeach
					</div>
					<div class='btn-group'>
						@foreach(array('CNJ', 'THM', 'ACN') as $switch_job)
						<button type='submit' name='class' value='{{ $switch_job }}' class='btn btn-warning'>
							<img src='/img/classes/{{ $switch_job }}.png' rel='tooltip' title='{{ $job_list[$switch_job] }}'>
						</button>
						@endforeach
					</div>
				</div>
			</div>

			<input type='hidden' name='level' value='{{ $level }}'>
			<input type='hidden' name='forecast' value='{{ $forecast }}'>
			<input type='hidden' name='hindsight' value='{{ $hindsight }}'>
			<input type='hidden' name='craftable_only' value='{{ $craftable_only }}'>

		</form>
	</div>
</div>

<div class='panel panel-info'>
	<div class='panel-heading'>
		<h3 class='panel-title'>Switch Level</h3>
	</div>
	<div class='panel-body text-center'>
		<ul class='pagination'>
			<li>
				<a href='/equipment/list?{{ $job->abbreviation }}:1:{{ $forecast }}:{{ $hindsight }}:{{ $craftable_only }}'>1{{ $level - 10 > 2 ? '...' : '' }}</a>
			</li>
			@foreach(range($level - 10, $level + 10) as $switch_level)
			<?php if ($switch_level < 2 || $switch_level > 49) continue; ?>
			<li>
				<a href='/equipment/list?{{ $job->abbreviation }}:{{ $switch_level }}:{{ $forecast }}:{{ $hindsight }}:{{ $craftable_only }}'>{{ $switch_level }}</a>
			</li>
			@endforeach
			<li>
				<a href='/equipment/list?{{ $job->abbreviation }}:50:{{ $forecast }}:{{ $hindsight }}:{{ $craftable_only }}'>{{ 49 > $switch_level ? '...' : '' }}50</a>
			</li>
		</ul>
	</div>
</div>

<div class='panel panel-info'>
	<div class='panel-heading'>
		<h3 class='panel-title'>Legend</h3>
	</div>
	<div class='panel-body text-center'>
		<div class='row'>
			<div class='col-sm-4 small'>
				<div class='alert alert-success'>
					Selected Level Gear
				</div>
				<div class='alert alert-warning'>
					Foresight Gear
				</div>
				<div class='alert alert-info'>
					Hindsight Gear
				</div>
			</div>
			<div class='col-sm-8'>
				<div class='alert alert-info'>
					<p>
						<strong>Why should I upgrade?</strong>
					</p>
					<div class="text-center stat" style='margin: 10px 0;'>
						<span>+8</span> &nbsp; 
						<img src="/img/stats/Craftsmanship.png" class="stat-icon" rel="tooltip" title="Craftsmanship">
					</div>
					<p><em>Hover and find out</em></p>
					<p><small>Compares hovered item against the previous level's item</small></p>
				</div>
			</div>
		</div>
	</div>
</div>

<div class='well'>
	<p>
		<strong>Crafting as a Service</strong> couldn't have done it without these resources.  Please support them!
	</p>
	<p>
		<small><em>
			<a href='http://xivdb.com/' target='_blank'>XIVDB</a> for their tooltips and data.
			<a href='http://game-icons.net/' target='_blank'>Game-icons.net</a> for some of the icons.
			And of course <a href='http://www.finalfantasyxiv.com/' target='_blank'>Square Enix</a>.
		</em></small>
	</p>
</div>

@stop
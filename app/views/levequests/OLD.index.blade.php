@extends('wrapper.layout')

@section('javascript')
	<script src='{{ cdn('/js/levequests.js') }}'></script>
@stop

@section('banner')
	<h1>Levequests</h1>
@stop

@section('content')

<p>** TODO Mobile? **</p>

<ul class='nav nav-tabs'>
	@foreach($crafting_job_list as $job)
	<li {{ $job->id == reset($crafting_job_ids) ? 'class="active" ' : '' }}rel='tooltip' title='{{{ $job->name->term }}}'>
		<a href='#{{ $job->en_abbr->term }}-tab' data-toggle='tab'><img src="/img/classes/{{ $job->en_abbr->term }}.png"> <span class='visible-md-inline visible-lg-inline'>{{ $job->en_abbr->term }}</span></a>
	</li>
	@endforeach
</ul>
<div class='tab-content'>
	@foreach($crafting_job_list as $job)
	<div class='tab-pane{{ $job->id == reset($crafting_job_ids) ? ' active' : '' }}' id='{{ $job->en_abbr->term }}-tab'>
		<h2>{{{ $job->name->term }}}</h2>

		<div class='table-responsive'>
			<table class='levequests-table table table-bordered table-striped' id='{{ $job->en_abbr->term }}-table'>
				@foreach(array_merge(array(1), range(5,45, 5)) as $level)
				<tbody class='level-header' id='{{ $job->en_abbr->term }}-{{ $level }}-section'>
					<tr>
						<th colspan='8' data-section='#{{ $job->en_abbr->term }}-{{ $level }}'>
							<h4>
								<i class='glyphicon glyphicon-chevron-down pull-right'></i>
								<img src="/img/classes/{{ $job->en_abbr->term }}.png"> Level {{ $level }}
							</h4>
						</th>
					</tr>
				</tbody>
				<tbody class='level-section hidden' id='{{ $job->en_abbr->term }}-{{ $level }}'>
					@foreach($leves[$job->en_abbr->term][$level] as $leve)
					<?php $recipe_id = count($leve->item->recipe) ? $leve->item->recipe[0]->id : 0; ?>
					<tr>
						@if($leve->triple)
						<td class='valign' width='23' style='padding: 0 4px;'>
							<i class='glyphicon glyphicon-fire text-danger' rel='tooltip' title='Triple Turnin!'></i>
						</td>
						@endif
						<td class='item' colspan='{{ $leve->triple ? 1 : 2 }}'>
							<p>
								<strong>
									<i class='glyphicon glyphicon-{{ $type_to_icon[$leve->type] }} leve-type' rel='tooltip' title='{{{ $leve->type }}}'></i><a href='/leve/breakdown/{{ $leve->id }}' class='text-success' rel='tooltip' title='More Leve Information'>{{ $leve->name }}</a>
								</strong>
							</p>

							@if($recipe_id == 0)
							<a href='http://xivdb.com/?item/{{ $leve->item->id }}' class='item-name' target='_blank'>
							@else
							<a href='http://xivdb.com/?recipe/{{ $recipe_id }}' class='item-name' target='_blank'>
							@endif
								<img src='' data-src='{{ assetcdn('items/nq/' . $leve->item->id . '.png') }}' class='reveal-later' width='24' height='24' style='margin-right: 10px;'>{{ $leve->item->name->term }}
							</a>
							@if ($leve->amount > 1)
							<span class='label label-primary' rel='tooltip' title='Amount Required' data-container='body'>
								x {{ $leve->amount }}
							</span>
							@endif
						</td>
						<td class='text-center location {{ preg_replace('/\W/', '', strtolower($leve->major_location)) }}'>
							<div>{{ ! empty($leve->location) ? $leve->location : '' }}</div>
							<div>{{ ! empty($leve->minor_location) ? $leve->minor_location : '' }}</div>
						</td>
						<td class='text-center reward valign'>
							<img src='/img/xp.png' width='20' height='20'>
							{{ number_format($leve->xp) }}
						</td>
						<td class='text-center reward valign'>
							<img src='/img/coin.png' width='20' height='20'>
							{{ number_format($leve->gil) }}
						</td>
						<td class='text-center reward valign'>
							<button class='btn btn-default leve_rewards' data-class='{{ $leve->classjob_id }}' data-level='{{ $level }}' data-toggle='popover' data-trigger='focus' data-content-id='#rewards_for_{{ $leve->id }}' rel='tooltip' title='Potential Rewards'>
								<i class='glyphicon glyphicon-gift'></i>
							</button>
							<div class='hidden' id='rewards_for_{{ $leve->id }}'>
								X
							</div>
						</td>
						<td class='text-center valign'>
							@if(count($leve->item->vendors))
							<a href='#' class='btn btn-default vendors margin-right' rel='tooltip' title='Available for {{ $leve->item->min_price }} gil, Click to load Vendors'>
								<img src='/img/coin.png' width='20' height='20'>
								{{ number_format($leve->item->min_price) }} ea
							</a>
							@endif
						</td>
						<td class='text-right valign'>
							<button class='btn btn-default add-to-list' data-item-id='{{ $leve->item->id }}' data-item-name='{{{ $leve->item->name->term }}}' data-item-quantity='{{{ $leve->amount }}}'>
								<i class='glyphicon glyphicon-shopping-cart'></i>
								<i class='glyphicon glyphicon-plus'></i>
							</button>
						</td>
					</tr>
					@endforeach
				</tbody>
				@endforeach
			</table>
		</div>
	</div>
	@endforeach
</div>

<div class='panel panel-info margin-top'>
	<div class='panel-heading'>Legend</div>
	<div class='panel-body'>
		<p>
			<strong><i class='glyphicon glyphicon-fire text-danger'></i> Triple Turnins</strong> &ndash;
			Use your Levequest allowances wisely, turn in three sets of these marked leves at a time.
		</p>
		
		<hr>

		<p>
			<strong><i class='glyphicon glyphicon-home'></i> Town Levequests</strong> &ndash;
			You'll stick in town to deliver the specified item(s).
		</p>
		
		<hr>

		<p>
			<strong><i class='glyphicon glyphicon-leaf'></i> Field Levequests</strong> &ndash;
			You will need to leave town to deliver the specified item(s).
		</p>
		
		<hr>

		<p>
			<strong><i class='glyphicon glyphicon-envelope'></i> Courier Levequests</strong> &ndash;
			You will need to deliver the item(s) to a levecamp.
		</p>
		
		<hr>

		<p>
			<strong><i class='glyphicon glyphicon-plane'></i> Reverse Courier Levequests</strong> &ndash;
			Starting at an outside camp, you will deliver the specified item(s) into town.
		</p>
		
		<hr>

		<p>
			<strong>Sorting</strong> &ndash; Each level displays triple leves first, then the rest.  Each subset is ordered by XP and Gil value as well.
		</p>

	</div>
</div>

@stop
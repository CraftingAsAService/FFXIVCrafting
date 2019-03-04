@extends('app')

@section('meta')
	<meta name="robots" content="noindex,nofollow">
@stop

@section('vendor-css')
	<link href='{{ cdn('/css/bootstrap-switch.css') }}' rel='stylesheet'>
	<link href='{{ cdn('/css/bootstrap-tour.css') }}' rel='stylesheet'>
@stop

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/crafting.js') }}'></script>
	<script type='text/javascript' src='{{ cdn('/js/bootstrap-tour.min.js') }}'></script>
	<script type='text/javascript' src='{{ cdn('/js/bootstrap-switch.js') }}'></script>
@stop

@section('banner')

	{{-- <a href='#' id='start_tour' class='start btn btn-primary pull-right hidden-print' style='margin-top: 12px;'>
		<i class='glyphicon glyphicon-play'></i>
		Tour
	</a> --}}

	<a href='#' id='csv_download' class='btn btn-info pull-right hidden-print margin-top margin-right'>
		<i class='glyphicon glyphicon-download-alt'></i>
		Download
	</a>

	<a href='#' class='btn btn-default pull-right hidden-print margin-top margin-right' id='clear-localstorage' rel='tooltip' title='Clear Page Progress'><i class='glyphicon glyphicon-floppy-remove'></i></a>

	{{--
	<span class="dropdown pull-right hidden-print" style='margin-top: 12px; margin-right: 10px;'>
		<button class='btn btn-success' id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class='glyphicon glyphicon-globe'></i>
			Map
			<span class="caret"></span>
		</button>
		<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
			<li><a href='#' id='map_all'>All</a></li>
			<li><a href='#' id='map_remaining'>Remaining</a></li>
		</ul>
	</span>
	--}}

	<h1 class='csv-filename' style='margin-top: 0;'>
		@if(isset($jobs))
		@if(count($jobs) == 1)
		<img src='/img/jobs/{{ $jobs[0]->abbr }}-inactive.png' width='32' height='32' style='position: relative; top: -3px;'>
		{{ $jobs[0]->name }} Crafting
		@else
		Crafting for {{ implode(', ', $jobs->pluck('name')->all()) }}
		@endif
		@elseif(isset($item))
		Crafting {{ $item->display_name }}
		@else
		Your Crafting List
		@endif
	</h1>
	@if(isset($jobs))
	<h2>
	@if($start < 55 || $start > 110)
	recipes between {{ $start }} and {{ $end }}
	@else
		level 50
		@if ($start >= 55)
			<i class='glyphicon glyphicon-star'></i>
		@endif
		@if ($start >= 70)
			<i class='glyphicon glyphicon-star'></i>
		@endif
		@if ($start >= 90)
			<i class='glyphicon glyphicon-star'></i>
		@endif
		@if ($start >= 110)
			<i class='glyphicon glyphicon-star'></i>
		@endif
		recipes
	@endif
	</h2>
	@endif
@stop

@section('content')


<div class='table-responsive'>
	<table class='table table-bordered table-striped text-center' id='obtain-these-items'>
		<thead>
			<tr>
				<th class='text-center'>Item</th>
				<th class='text-center' width='75'>Needed</th>
				<th class='text-center hidden-print' width='102'>Obtained</th>
				<th class='text-center hidden-print' width='75'>Total</th>
				<th class='text-center'>Source</th>
			</tr>
		</thead>
		@foreach($reagent_list as $section => $list)
		<?php if (empty($list)) continue; ?>
		<?php if (isset($list[1]) && empty($list[1])) continue; ?>
		<tbody id='{{ preg_replace('/\s|\-/', '', $section) }}-section'>
			<tr>
				<th colspan='6'>
					<button class='btn btn-default btn-sm pull-right collapsible'><i class='glyphicon glyphicon-chevron-down'></i></button>
					@if($section == 'Gathered')
					<button class='btn btn-default btn-sm pull-right margin-right' id='toggle-crystals'>Toggle Crystals</button>
					@endif
					<div style='margin-top: 4px;'>Origin: {{ $section }}</div>
				</th>
			</tr>
			@foreach($list as $level => $reagents)
			<?php $i = 0; ?>
			@foreach($reagents as $reagent)
			<?php
				$requires = []; $yields = 1;
				$item_level = $reagent['item']->level;
				$link = item_link() . $reagent['item']->id;
				if ($section == 'Pre-Requisite Crafting')
				{
					$item_level = $reagent['item']->recipes[0]->level;
					$yields = $reagent['item']->recipes[0]->yield;
					foreach ($reagent['item']->recipes[0]->reagents as $rr_item)
						$requires[] = $rr_item->pivot->amount . 'x' . $rr_item->id;
					// $link = 'recipe/' . $reagent['item']->recipes[0]->id;
				}
			?>
			<tr class='reagent' data-item-id='{{ $reagent['item']->id }}' data-requires='{{ implode('&', $requires) }}' data-yields='{{ $yields }}' data-ilvl='{{ $reagent['item']->ilvl }}' data-item-category='{{ $reagent['item']->category->name }}'>
				<td class='text-left'>
					@if($level != 0)
					<a class='close ilvl' rel='tooltip' title='Level'>
						{{ $item_level }}
					</a>
					@endif
					<a href='{{ $link }}' target='_blank'>
						<img src='{{ assetcdn('item/' . $reagent['item']->icon . '.png') }}' width='36' height='36' class='margin-right'><span class='name'>{{ $reagent['item']->display_name }}</span>
					</a>
					@if ($yields > 1)
					<span class='label label-primary' rel='tooltip' title='Amount Yielded'>
						x {{ $yields }}
					</span>
					@endif
				</td>
				<td class='needed valign hidden-print'>
					<span>...<!--{{ $reagent['make_this_many'] }}--></span>@if(isset($reagent['both_list_warning']))
					<a href='#' class='nowhere tt-force' rel='tooltip' title='Note: Item exists in main list but is also required for another.'>*</a>
					@endif
				</td>
				<td class='valign hidden-print'>
					<div class='input-group'>
						<input type='number' autocomplete='off' class='form-control obtained text-center' min='0' value='0' step='{{ $yields }}' style='padding: 6px 3px;'>
						<div class='input-group-btn'>
							<button class='btn btn-default obtained-ok' type='button' style='padding: 7px 6px 6px;'><span class='glyphicon glyphicon-ok-circle'></span></button>
						</div>
					</div>
				</td>
				<td class='valign total'>0</td>
				<td class='valign'>
					@foreach(array_keys(array_reverse($reagent['cluster_jobs'])) as $cluster_job)
					<img src='/img/jobs/{{ $cluster_job }}-inactive.png' width='24' height='24' class='click-to-view' data-type='{{ strtolower($cluster_job) }}nodes' rel='tooltip' title='Click to load {{ $cluster_job }} Nodes'>
					@endforeach

					@foreach($reagent['item']->recipes as $recipe)
					<img src='/img/jobs/{{ $recipe->job->abbr }}{{ isset($classes) && in_array($recipe->job->abbr, $classes) ? '' : '-inactive' }}.png' width='24' height='24' class='click-to-view' data-type='recipes' rel='tooltip' title='Click to load {{ $recipe->job->abbr }}&#39;s Recipe'>
					@endforeach

					@if($reagent['item']->shops->count())
					<img src='/img/shop.png' width='24' height='24' rel='tooltip' title='Available for {{ $reagent['item']->price }} gil, Click to load Shops' class='click-to-view{{ $reagent['self_sufficient'] ? ' opaque' : '' }}' data-type='shops'>
					<span class='hidden vendors'>{{ $reagent['item']->price }}</span>
					@endif

					@if($reagent['item']->mobs->count())
					<img src='/img/mob-inactive.png' class='click-to-view' data-type='mobs' width='24' height='24' rel='tooltip' title='Click to load Beasts'>
					@endif

				</td>
				<?php continue; ?>
			</tr>
			@endforeach
			@endforeach
		</tbody>
		@endforeach
		<tbody id='CraftingList-section'>
			<tr>
				<th colspan='6'>
					<button class='btn btn-default btn-sm pull-right collapsible'><i class='glyphicon glyphicon-chevron-down'></i></button>
					<div style='margin-top: 4px;'>Crafting List</div>
				</th>
			</tr>
			@foreach($recipes as $recipe)
			<?php
				$requires = [];
				foreach ($recipe->reagents as $reagent)
					$requires[] = $reagent->pivot->amount . 'x' . $reagent->id;
			?>
			<tr class='reagent exempt' data-item-id='{{ $recipe->item->id }}' data-requires='{{ implode('&', $requires) }}' data-yields='{{ $recipe->yield }}'>
				<td class='text-left'>
					<a class='close ilvl' rel='tooltip' title='Level'>
						{{ $recipe->recipe_level }}
					</a>
					<a href='{{ item_link() . $recipe->item->id }}' target='_blank'>
						<img src='{{ assetcdn('item/' . $recipe->item->icon . '.png') }}' width='36' height='36' style='margin-right: 5px;'><span class='name'>{{ $recipe->item->display_name }}</span>
					</a>
					@if ($recipe->req_craftsmanship)
					<span class='craftsmanship pull-right margin-right' rel='tooltip' title='Required Craftsmanship'>
						<img src="/img/stats/Craftsmanship.png" class="stat-icon">
						{{ $recipe->req_craftsmanship }}
					</span>
					@endif
					@if ($recipe->req_control)
					<span class='control pull-right margin-right' rel='tooltip' title='Required Control'>
						<img src="/img/stats/Control.png" class="stat-icon">
						{{ $recipe->req_control }}
					</span>
					@endif
					@if ($recipe->yield > 1)
					<span class='label label-primary' rel='tooltip' title='Amount Yielded'>
						x {{ $recipe->yield }}
					</span>
					@endif
					<div class='pull-right' style='clear: right;'>
						@if($include_quests && isset($recipe->item->quest[0]))
						<img src='/img/{{ $recipe->item->quest[0]->quality ? 'H' : 'N' }}Q.png' rel='tooltip' title='Turn in {{ $recipe->item->quest[0]->amount }}{{ $recipe->item->quest[0]->quality ? ' (HQ)' : '' }} to the Guildmaster{{ $recipe->item->quest[0]->notes ? ', see bottom for note' : '' }}' width='24' height='24'>
						@endif

						@if($recipe->item->leve_required->count())
							@foreach ($recipe->item->leve_required as $leve)
							@if($leve->repeats)
							<img src='/img/leve_icon_red.png' rel='tooltip' title='{{ $leve->name }}. Repeatable Leve!' style='margin-left: 5px;' width='16'>
							@else
							<img src='/img/leve_icon.png' rel='tooltip' title='{{ $leve->name }}' style='margin-left: 5px;' width='16'>
							@endif
							@endforeach
						@endif
					</div>
				</td>
				<td class='needed valign hidden-print'>
					<?php
						$needed = (isset($item_amounts) && isset($item_amounts[$recipe->item->id]) ? $item_amounts[$recipe->item->id] : (1 + (@$recipe->item->quest[0]->amount ? $recipe->item->quest[0]->amount - 1 : 0)));
						$needed = ceil($needed / $recipe->yield) * $recipe->yield;
					?>

					<input type='number' autocomplete='off' class='recipe-amount form-control text-center' min='0' step='{{ $recipe->yield }}' value='{{ $needed }}' style='padding: 6px 3px;'>
				</td>
				<td class='valign hidden-print'>
					<div class='input-group'>
						<input type='number' autocomplete='off' class='form-control obtained text-center' min='0' step='{{ $recipe->yield }}' value='0' style='padding: 6px 3px;'>
						<div class='input-group-btn'>
							<button class='btn btn-default obtained-ok' type='button' style='padding: 7px 6px 6px;'><span class='glyphicon glyphicon-ok-circle'></span></button>
						</div>
					</div>
				</td>
				<td class='valign total'>{{ $needed }}</td>
				<td class='valign'>
					@if (is_null($recipe->job))
					<img src='/img/FC.png' width='24' height='24' class='click-to-view' data-type='recipes' title='Free Company Craft'></i>
					@else
					<img src='/img/jobs/{{ $recipe->job->abbr }}{{ isset($classes) && in_array($recipe->job->abbr, $classes) ? '' : '-inactive' }}.png' width='24' height='24' class='click-to-view' data-type='recipes' rel='tooltip' title='Click to load {{ $recipe->job->abbr }}&#39;s Recipe'>
					@endif

					@if($recipe->item->shops->count())
					<img src='/img/shop.png' width='24' height='24' rel='tooltip' title='Available for {{ $recipe->item->price }} gil, Click to load Shops' class='click-to-view{{ $reagent['self_sufficient'] ? ' opaque' : '' }}' data-type='shops'>
					@endif
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>

@if( ! isset($item))
<a name='options'></a>
<div class='panel panel-info hidden-print'>
	<div class='panel-heading'>
		<span class='panel-title'>Switch Options</span>
	</div>
	<div class='panel-body'>

		<a href='{{ toggle_query_string('self_sufficient') }}' class='btn btn-primary'>Turn Self Sufficient {{ isset($options) && isset($options['self_sufficient']) ? 'Off' : 'On' }}</a>

		@if(isset($options['inclusions']))

			<a href='{{ toggle_query_string('inclusions') }}' class='btn btn-primary'>Disregard Inclusions</a>

		@endif
	</div>
</div>
@endif

<div class='row'>
	<?php $left_pane_test = isset($jobs) && isset($quest_items) && count($quest_items); ?>
	@if($left_pane_test)
	<div class='col-sm-6'>
		<div class='panel panel-primary' id='leveling-information'>
			<div class='panel-heading'>
				<span class='panel-title'>Leveling Information</span>
			</div>
			<div class='panel-body'>
				<p>Be efficient, make quest items in advance!</p>
				<p>Materials needed already reflected in lists above.</p>

				<ul>
					@foreach($quest_items as $quest)
					<li>
						@if(count($jobs) > 1)
						<img src='/img/jobs/{{ $quest->job_category->jobs[0]->abbr }}-inactive.png' width='16' height='16' style='position: relative; top: -1px;' rel='tooltip' title='{{ $quest->job_category->jobs[0]->name }}'>
						@endif
						Level {{ $quest->level }}:
						@if ( ! $quest->requirements)
							No data!
						@else
							@foreach ($quest->requirements as $req_item)
							{{ $req_item->display_name }}
							@endforeach
						@endif
					</li>
					@endforeach
				</ul>

				<p><em>Want to level faster?  Visit the <a href='/levequests'>Leves</a> page.</em></p>
			</div>
		</div>
	</div>
	@endif
	<div class='hidden-print col-sm-{{ $left_pane_test ? '6' : '12' }}'>
		<div class='panel panel-info'>
			<div class='panel-heading'>
				<span class='panel-title'>Tips</span>
			</div>
			<div class='panel-body text-center'>
				<p>Get extras in case of a failed synthesis.</p>

				<p>Improve your chances for HQ items by using the <a href='/equipment'>gear profiler</a>.</p>

				<p>Don't forget the <a href='/food'>food</a> or <a href='/materia'>materia</a>!</p>
			</div>
		</div>
	</div>
</div>

@stop
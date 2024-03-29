@extends('app')

@section('meta')
	<meta name="robots" content="noindex,nofollow">
@endsection

@section('vendor-css')
	<link href='{{ cdn('/css/bootstrap-switch.css') }}' rel='stylesheet'>
	<link href='{{ cdn('/css/bootstrap-tour.css') }}' rel='stylesheet'>
	<style type='text/css'>
		.reagent .category {
			position: absolute;
			bottom: 8px;
			left: 8px;
			width: 36px;
			text-align: center;
			white-space: nowrap;
			overflow: hidden;
			font-size: .65em;
		}
	</style>
@endsection

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/crafting.js') }}'></script>
	<script type='text/javascript' src='{{ cdn('/js/bootstrap-tour.min.js') }}'></script>
	<script type='text/javascript' src='{{ cdn('/js/bootstrap-switch.js') }}'></script>
@endsection

@section('banner')

{{--	@include('partials.support-us')--}}

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
@endsection

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
					@if ($section == 'Gathered')
					<button class='btn btn-default btn-sm pull-right margin-right' id='toggle-crystals'>Toggle Crystals</button>
					<button class='btn btn-default btn-sm pull-right margin-right' id='toggle-sort'>Default Sort</button>
					@elseif ($section == 'Pre-Requisite Crafting')
					<button class='btn btn-default btn-sm pull-right margin-right' id='toggle-pr-sort'>Default Sort</button>
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
					$item_level = $reagent['item']->recipes[0]->{$section == 'Pre-Requisite Crafting' ? 'recipe_level' : 'level'};
					$yields = $reagent['item']->recipes[0]->yield;
					foreach ($reagent['item']->recipes[0]->reagents as $rr_item)
						$requires[] = $rr_item->pivot->amount . 'x' . $rr_item->id;
					// $link = 'recipe/' . $reagent['item']->recipes[0]->id;
				}
			?>
			<tr class='reagent' data-item-id='{{ $reagent['item']->id }}' data-item-name='{{ $reagent['item']->display_name }}' data-requires='{{ implode('&', $requires) }}' data-yields='{{ $yields }}' data-ilvl='{{ $reagent['item']->ilvl }}' data-item-category='{{ $reagent['item']->category->name }}' data-item-location='{{ is_string($level) && $level ? $level : '' }}@if ( ! empty($reagent['item']->nodes->first()->level)), L{{ $reagent['item']->nodes->first()->level }}@endif' data-recipe-class='{{ $reagent['item']->recipes[0]->job->abbr ?? '' }}' data-sorting='{{ $reagent['item']->category->rank . '.' . str_pad($reagent['item']->ilvl, 3, '0', STR_PAD_LEFT) . '.' . str_pad($reagent['item']->id, 8, '0', STR_PAD_LEFT) }}'>
				<td class='text-left'>
					@if ($level != 0)
					<a class='close ilvl' rel='tooltip' title='Level'>
						{{ $item_level }}
					</a>
					@endif
					<img src='{{ icon($reagent['item']->icon) }}' width='36' height='36' class='margin-right pull-left'>
					<div>
						@if ($yields > 1)
						<span class='label label-primary pull-right margin-right' rel='tooltip' title='Amount Yielded'>
							x {{ $yields }}
						</span>
						@endif
						<a href='{{ $link }}' target='_blank' class='name'>
							{{ $reagent['item']->display_name }}
						</a>
						<div class='bonus-info'>
							<small class='text-muted'>{{ $reagent['item']->category->name }}</small>
							@if (is_string($level) && $level)
								<small class='text-muted hidden-xs pointer pull-right'>
									{{-- &mdash; --}}
									@if ($reagent['item']->nodes->where('timer', '!=', null)->count())
									@foreach ($reagent['item']->nodes->where('timer', '!=', null) as $node)
										<span rel="tooltip" data-html='true' title="{{ ucfirst($node->timer_type) }} - {{ $node->timer }}" style="margin-right: 3px;">
											@if ($node->timer_type)
											<span>{{ ucfirst($node->timer_type)[0] }}</span>
											@endif
											<i class='glyphicon glyphicon-time'></i>
										</span>
									@endforeach
									@endif
									<span rel='tooltip' data-html='true' title='{!! empty($reagent['item']->nodes->first()->coordinates) ? 'N/A' : $reagent['item']->nodes->first()->coordinates !!}{{ $reagent['item']->nodes->count() > 1 ? '<br>' . ($reagent['item']->nodes->count() - 1) . ' other locations available.' : '' }}'>{!! $level !!}@if ( ! empty($reagent['item']->nodes->first()->level)), L{{ $reagent['item']->nodes->first()->level }}@endif</span>
								</small>
							@endif
						</div>
					</div>
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
					@foreach(array_reverse($reagent['cluster_jobs']) as $cluster_job => $node)
					<img src='/img/gathering/nodes/{{ $node['type'] . ($node['timer'] ? '-unspoiled' : '') }}.png' width='24' height='24' class='click-to-view' data-type='{{ strtolower($cluster_job) }}nodes' data-notice='{{ ucwords(strtolower($cluster_job)) }} Nodes' rel='tooltip' title='Click to load {{ $cluster_job }} Nodes'>
					@endforeach

					@foreach($reagent['item']->recipes as $recipe)
					<img src='/img/jobs/{{ $recipe->job->abbr }}{{ isset($classes) && in_array($recipe->job->abbr, $classes) ? '' : '-inactive' }}.png' width='24' height='24' class='click-to-view' data-type='recipes' rel='tooltip' title='Click to load {{ $recipe->job->abbr }}&#39;s Recipe'>
					@endforeach

					@if($reagent['item']->special_buy || $reagent['item']->gc_price || $reagent['item']->shops->count())
					<img src='/img/shop.png' width='24' height='24' rel='tooltip' title='Available for {{ $reagent['item']->price }} gil, Click to load Shops' class='click-to-view{{ $reagent['self_sufficient'] ? ' opaque' : '' }}' data-type='shops'>
					<span class='hidden vendors'>{{ $reagent['item']->price }}</span>
					@endif

					@if($reagent['item']->mobs->count())
					<img src='/img/mob-inactive.png' class='click-to-view' data-type='mobs' width='24' height='24' rel='tooltip' title='Click to load Beasts'>
					@endif

					<i class='glyphicon glyphicon-magnet isearch' rel='tooltip' title='Click to copy /isearch command' data-clipboard='/isearch "{{ $reagent['item']->display_name }}"'></i>
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
					<div class='pull-right text-right'>
						{{-- TODO ENABLE @spaceless --}}
						<a class='close ilvl' rel='tooltip' title='Level'>
							<div class='text-right'>
								{{ $lvlType == 'r' ? $recipe->recipe_level : $recipe->level }}
								@if ($lvlType == 'r' && $recipe->recipe_level == 50)
								<br>
								<span style='font-size: .7em;'>
									@if ($recipe->level >= 55)<i class='glyphicon glyphicon-star'></i>@endif
									@if ($recipe->level >= 70)<i class='glyphicon glyphicon-star'></i>@endif
									@if ($recipe->level >= 90)<i class='glyphicon glyphicon-star'></i>@endif
									@if ($recipe->level >= 110)<i class='glyphicon glyphicon-star'></i>@endif
								</span>
								@endif
							</div>
						</a>
						{{-- TODO ENABLE @endspaceless --}}
						<div>
							@if($include_quests && isset($recipe->item->quest[0]))
							<img src='/img/{{ $recipe->item->quest[0]->quality ? 'H' : 'N' }}Q.png' rel='tooltip' title='Turn in {{ $recipe->item->quest[0]->amount }}{{ $recipe->item->quest[0]->quality ? ' (HQ)' : '' }} to the Guildmaster{{ $recipe->item->quest[0]->notes ? ', see bottom for note' : '' }}' width='24' height='24'>
							@endif

							@if($recipe->item->leve_required->count())
								@foreach ($recipe->item->leve_required as $leve)
								@if($leve->repeats)
								<img src='/img/leve_icon_red.png' rel='tooltip' title='{{ $leve->name }}. Repeatable Leve!' width='16'>
								@else
								<img src='/img/leve_icon.png' rel='tooltip' title='{{ $leve->name }}' width='16'>
								@endif
								@endforeach
							@endif
						</div>
					</div>
					<img src='{{ icon($recipe->item->icon) }}' width='36' height='36' class='margin-right pull-left'>
					<div>
						@if ($recipe->yield > 1)
						<span class='label label-primary pull-right margin-right' rel='tooltip' title='Amount Yielded'>
							x {{ $recipe->yield }}
						</span>
						@endif
						<a href='{{ item_link() . $recipe->item->id }}' target='_blank' class='name'>
							{{ $recipe->item->display_name }}
						</a>
						<div>
							<small class='text-muted'>{{ $recipe->item->category->name }}</small>
						</div>
					</div>
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

					<i class='glyphicon glyphicon-magnet isearch' rel='tooltip' title='Click to copy /isearch command' data-clipboard='/isearch "{{ $recipe->item->display_name }}"'></i>
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
				<p>Materials needed already reflected in lists above, but vital information is missing (quantity, HQ requirements).</p>

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
								{{ $req_item->display_name }}{{ $loop->last ? '' : ',' }}
							@endforeach
						@endif
					</li>
					@endforeach
				</ul>
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

				<p>Don't forget the <a href='/food'>food</a> and <a href='/levequests'>leves</a> for faster leveling!</p>
			</div>
		</div>
	</div>


</div>

@include('partials.support-us')


<script>
	const su = localStorage.getItem('support-us');
	if (su !== 'Hide') {
		for (let item of document.getElementsByClassName('support-us')) {
			item.classList.remove('collapse');
			item.getElementsByClassName('hide-btn')[0].addEventListener('click', () => {
				localStorage.setItem('support-us', 'Hide');
				for (let item of document.getElementsByClassName('support-us')) {
					item.classList.add('collapse');
				}
			});
		}
	}
</script>

@endsection

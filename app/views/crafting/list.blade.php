@extends('layout')

@section('vendor-css')
	<link href='/css/bootstrap-switch.css' rel='stylesheet'>
	<link href='/css/bootstrap-tour.min.css' rel='stylesheet'>
@stop

@section('javascript')
	<script type='text/javascript' src='http://xivdb.com/tooltips.js'></script>
	<script type='text/javascript' src='/js/crafting.js'></script>
	<script type='text/javascript' src='/js/bootstrap-tour.min.js'></script>
	<script type='text/javascript' src='/js/bootstrap-switch.js'></script>
@stop

@section('content')

<a href='#' id='start_tour' class='start btn btn-primary pull-right' style='margin-top: 12px;'>
	<i class='glyphicon glyphicon-play'></i>
	Start Tour
</a>

<h1>
	@if(isset($job))
	{{ implode(' ', explode(',', $desired_job)) }} 
	Recipes between Levels {{ $start }} and {{ $end }}
	@else
	Custom Recipe List
	@endif
</h1>

<div class='table-responsive'>
	<table class='table table-bordered table-striped text-center' id='obtain-these-items'>
		<thead>
			<tr>
				<th class='text-center'>Item</th>
				<th class='text-center' width='75'>Needed</th>
				<th class='text-center' width='102'>Obtained</th>
				<th class='text-center'>Can be Bought</th>
				<th class='text-center'>Source</th>
			</tr>
		</thead>
		@foreach($reagent_list as $section => $list)
		<?php if (empty($list)) continue; ?>
		<tbody id='{{ preg_replace('/\s|\-/', '', $section) }}-section'>
			<tr>
				<th colspan='6'>
					<button class='btn btn-default pull-right glyphicon glyphicon-chevron-down collapse'></button>
					Origin: {{ $section }}
				</th>
			</tr>
			@foreach($list as $level => $reagents)
			<?php $i = 0; ?>
			@foreach($reagents as $reagent)
			<?php $item =& $reagent['item']; ?>
			<?php
				$requires = array();
				if ($section == 'Pre-Requisite Crafting')
					foreach ($item->recipes[0]->reagents as $rr_item)
						$requires[] = $rr_item->pivot->amount . 'x' . $rr_item->id;
			?>
			<tr class='reagent' data-item-id='{{ $item->id }}' data-requires='{{ implode('&', $requires) }}'>
				<td class='text-left'>
					@if($section == 'Pre-Requisite Crafting')
					@foreach($item->recipes as $recipe)
					@if($level != 0)
					<a class='close' rel='tooltip' title='Level'>
						{{ $recipe->level }}
					</a>
					@endif
					<a href='http://xivdb.com/?recipe/{{ $recipe->id }}' target='_blank'>
						<img src='/img/items/{{ $recipe->icon ?: '../noitemicon' }}.png' style='margin-right: 5px;'>{{ $recipe->name }}
					</a>
					<?php break; ?>
					@endforeach
					@else
					@if($level != 0 && $section != 'Bought')
					<a class='close' rel='tooltip' title='Level'>
						{{ $item->level }}
					</a>
					@endif
					<a href='http://xivdb.com/{{ $item->href }}' target='_blank'>
						<img src='/img/items/{{ $item->icon ?: '../noitemicon' }}.png' style='margin-right: 5px;'>{{ $item->name }}
					</a>
					@endif
				</td>
				<td class='needed valign'>
					<span>{{ $reagent['make_this_many'] }}</span>@if(isset($reagent['both_list_warning']))
					<a href='#' class='nowhere tt-force' rel='tooltip' title='Note: Item exists in main list but is also required for another.'>*</a>
					@endif
				</td>
				<td class='valign'>
					<div class='input-group'>
						<input type='number' class='form-control obtained text-center' min='0' value='0' style='padding: 6px 3px;'>
						<div class='input-group-btn'>
							<button class='btn btn-default obtained-ok' type='button' style='padding: 6px 6px;'><span class='glyphicon glyphicon-ok-circle'></span></button>
						</div>
					</div>
				</td>
				<td>
					@if($item->buy)
					<div{{ $reagent['self_sufficient'] ? ' class="opaque"' : '' }}>

						<img src='/img/coin.png' class='stat-vendors' width='24' height='24'>
						{{ number_format($item->buy) }}

						<button class='btn btn-default btn-sm margin-left' data-toggle='popover' data-container='body' data-html='true' data-placement='left' data-content-id='#vendors_for_{{ $item->id }}'>
							{{ $reagent['vendor_count'] }} Vendor{{ $reagent['vendor_count'] > 1 ? 's' : '' }} 
							@if($reagent['vendor_count'] > 1 && count($reagent['vendors']) > 1)
							in {{ count($reagent['vendors']) }} Area{{ count($reagent['vendors']) > 1 ? 's' : '' }}
							@endif
						</button>
						<div class='hidden' id='vendors_for_{{ $item->id }}'>
							@foreach($reagent['vendors'] as $location_name => $vendors)
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
					</div>
					@endif
				</td>
				<td class='crafted_gathered'>
					@foreach(array_reverse(array_keys($reagent['node_jobs'])) as $reagent_job)
					<span class='btn btn-{{ $reagent_job == $reagent['self_sufficient'] ? 'success' : 'info' }}' data-toggle='popover' data-container='body' data-html='true' data-placement='left' data-content-id='#cg_for_{{ $item->id }}_{{ $reagent_job }}'>
						<img src='/img/classes/{{ $reagent_job }}.png' rel='tooltip' title='{{ $job_list[$reagent_job] }}'>
					</span>

					<div class='hidden' id='cg_for_{{ $item->id }}_{{ $reagent_job }}'>
						@foreach($reagent['nodes'][$reagent_job] as $location_name => $nodes)
						<p>{{ $location_name }}</p>
						@foreach($nodes as $node_level => $node_actions)
						<p>
							<span class='label label-primary' rel='tooltip' title='Node Level'>&commat;{{ $node_level }}</span>
							@foreach ($node_actions as $action => $levels)
							<span class='label label-default' rel='tooltip' title='Action'>{{ $action }}</span>
							<span class='label label-success' rel='tooltip' title='Item Level'>{{ implode(', ', $levels) }}</span>
							@endforeach
						</p>
						@endforeach
						@endforeach
					</div>
					@endforeach
					@foreach($item->recipes as $recipe)
					<span class='alert alert-{{ $recipe->job->abbreviation == $reagent['self_sufficient'] ? 'success' : 'warning' }}'>
						<img src='/img/classes/{{ $recipe->job->abbreviation }}.png' rel='tooltip' title='{{ $job_list[$recipe->job->abbreviation] }}'>
					</span>
					@endforeach
				</td>
			</tr>
			@endforeach
			@endforeach
		</tbody>
		@endforeach
		<tbody id='CraftingList-section'>
			<tr>
				<th colspan='6'>
					<button class='btn btn-default pull-right glyphicon glyphicon-chevron-down collapse'></button>
					Crafting List
				</th>
			</tr>
			@foreach($recipes as $recipe)
			<?php
				$requires = array();
				foreach ($recipe->reagents as $item)
					$requires[] = $item->pivot->amount . 'x' . $item->id;
			?>
			<tr class='reagent exempt' data-item-id='{{ $recipe->item->id }}' data-requires='{{ implode('&', $requires) }}'>
				<td class='text-left'>
					<a class='close' rel='tooltip' title='Level'>
						{{ $recipe->level }}
					</a>
					<a href='http://xivdb.com/?recipe/{{ $recipe->id }}' target='_blank'>
						<img src='/img/items/{{ $recipe->icon ?: '../noitemicon' }}.png' style='margin-right: 5px;'>{{ $recipe->name }}
					</a>
					@if ($recipe->yields > 1)
					<span class='label label-primary' rel='tooltip' title='Amount Yielded' data-container='body'>
						x {{ $recipe->yields }}
					</span>
					@endif
					<div class='pull-right' style='clear: right;'>
						@if( ! isset($job))
							<img src='/img/classes/{{ $recipe['abbreviation'] }}.png' rel='tooltip' title='{{ $job_list[$recipe['abbreviation']] }}'>
						@endif
						@if($include_quests && isset($recipe->item->quest[0]))
							<img src='/img/{{ $recipe->item->quest[0]->quality ? 'H' : 'N' }}Q.png' rel='tooltip' title='Turn in {{ $recipe->item->quest[0]->amount }}{{ $recipe->item->quest[0]->quality ? ' (HQ)' : '' }} to the Guildmaster{{ $recipe->item->quest[0]->notes ? ', see bottom for note' : '' }}' width='24' height='24'>
						@endif
						@if(isset($recipe->item->leve[0]))
							{{-- Disabled because I would also have to do it for class, and I'm lazy right now --}}
							{{-- <a href='/leve?name={{ $recipe->item->leve[0]->name }}'> --}}
							@if($recipe->item->leve[0]->triple)
							<img src='/img/triple.png' class='rotate-90' rel='tooltip' title='Triple Leve' style='margin-left: 5px;' width='16'>
							@else
							<img src='/img/leve.png' class='rotate-90' rel='tooltip' title='Regular Leve' style='margin-left: 5px;' width='16'>
							@endif
							{{-- </a> --}}
						@endif
					</div>
				</td>
				<td class='needed valign'>
					<input type='number' class='recipe-amount form-control text-center' min='0' value='{{ (isset($item_amounts) && isset($item_amounts[$recipe->item->id]) ? $item_amounts[$recipe->item->id] : (1 + (@$recipe->item->quest[0]->amount ? $recipe->item->quest[0]->amount - 1 : 0))) }}' style='padding: 6px 3px;'>
				</td>
				<td class='valign'>
					<div class='input-group'>
						<input type='number' class='form-control obtained text-center' min='0' value='0' style='padding: 6px 3px;'>
						<div class='input-group-btn'>
							<button class='btn btn-default obtained-ok' type='button' style='padding: 6px 6px;'><span class='glyphicon glyphicon-ok-circle'></span></button>
						</div>
					</div>
				</td>
				<td>
					@if($recipe->item->buy)
					<div{{ $reagent['self_sufficient'] ? ' class="opaque"' : '' }}>

						<img src='/img/coin.png' class='stat-vendors' width='24' height='24'>
						{{ number_format($recipe->item->buy) }}

						<button class='btn btn-default btn-sm margin-left' data-toggle='popover' data-container='body' data-html='true' data-placement='left' data-content-id='#vendors_for_{{ $item->id }}'>
							{{ $recipe_vendors[$recipe->id]['count'] }} Vendor{{ $recipe_vendors[$recipe->id]['count'] > 1 ? 's' : '' }} 
							@if($recipe_vendors[$recipe->id]['count'] > 1 && count($recipe_vendors[$recipe->id]['vendors']) > 1)
							in {{ count($recipe_vendors[$recipe->id]['vendors']) }} Area{{ count($recipe_vendors[$recipe->id]['vendors']) > 1 ? 's' : '' }}
							@endif
						</button>
						<div class='hidden' id='vendors_for_{{ $item->id }}'>
							@foreach($recipe_vendors[$recipe->id]['vendors'] as $location_name => $vendors)
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
					</div>
					@endif
				</td>
				<td class='crafted_gathered'>
					<span class='alert alert-success'>
						<img src='/img/classes/{{ $recipe['abbreviation'] }}.png' rel='tooltip' title='{{ $job_list[$recipe['abbreviation']] }}'>
					</span>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>

<a name='options'></a>

<div class='panel panel-info'>
	<div class='panel-heading'>
		<h3 class='panel-title'>Options</h3>
	</div>
	<div class='panel-body'>
		@if( ! isset($item_ids))
		<form action='/crafting' method='post' role='form' class='form-horizontal' id='self-sufficient-form'>
			<input type='hidden' name='class' value='{{ $desired_job }}'>
			<input type='hidden' name='start' value='{{ $start }}'>
			<input type='hidden' name='end' value='{{ $end }}'>
			<label>
				Self Sufficient
			</label>
			<div class='make-switch' data-on='success' data-off='warning' data-on-label='Yes' data-off-label='No'>
				<input type='checkbox' id='self_sufficient_switch' name='self_sufficient' value='1' {{ $self_sufficient ? " checked='checked'" : '' }}>
			</div>
			<small><em>* Refreshes page</em></small>
		</form>
		@else
		<form action='/crafting/list' method='post' role='form' class='form-horizontal' id='self-sufficient-form'>
			<input type='hidden' name='List:::{{ $self_sufficient ? 0 : 1 }}' value=''>
			<label>
				Self Sufficient
			</label>
			<div class='make-switch' data-on='success' data-off='warning' data-on-label='Yes' data-off-label='No'>
				<input type='checkbox' id='self_sufficient_switch' value='1' {{ $self_sufficient ? " checked='checked'" : '' }}>
			</div>
			<small><em>* Refreshes page</em></small>
		</form>
		@endif
	</div>
</div>

<div class='row'>
	@if(isset($job))
	<div class='col-sm-6'>
		@if($end - $start >= 4)
		<div class='panel panel-primary' id='leveling-information'>
			<div class='panel-heading'>
				<h3 class='panel-title'>Leveling Information</h3>
			</div>
			<div class='panel-body'>
				<p>Be efficient, make quest items in advance!</p>
				<p>Materials needed already reflected in lists above.</p>

				<ul>
					@foreach($quest_items as $quest)
					<li>
						@if(count($job) > 2)
						{{ $quest->job->abbreviation }} 
						@endif
						Level {{ $quest->level }}: 
						@if ( ! $quest->item)
							No data! Please help complete the list.
						@else
							{{ $quest->item->name }} 
							<small>x</small>{{ $quest->amount }}
							@if($quest->quality)
							<strong>(HQ)</strong>
							@endif
						@endif
						@if($quest->notes)
						({{ $quest->notes }})
						@endif
					</li>
					@endforeach
				</ul>

				<p><em>Want to level faster?  Visit the <a href='/leve'>Leves</a> page.</em></p>
			</div>
		</div>
		@endif
	</div>
	@endif
	<div class='col-sm-{{ isset($job) ? '6' : '12' }}'>
		<div class='panel panel-info'>
			<div class='panel-heading'>
				<h3 class='panel-title'>Tips</h3>
			</div>
			<div class='panel-body text-center'>
				<p>Get extras in case of a failed synthesis.</p>

				<p>Improve your chances for HQ items by using the <a href='/equipment'>equipment calculator</a>.</p>

				<p>Don't forget the <a href='/food'>food</a> or <a href='/materia'>materia</a>!</p>
			</div>
		</div>
	</div>
</div>

@stop
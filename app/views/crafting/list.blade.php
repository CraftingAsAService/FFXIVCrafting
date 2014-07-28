@extends('wrapper.layout')

@section('vendor-css')
	<link href='{{ cdn('/css/bootstrap-switch.css') }}' rel='stylesheet'>
	<link href='{{ cdn('/css/bootstrap-tour.min.css') }}' rel='stylesheet'>
@stop

@section('javascript')
	<script type='text/javascript' src='http://xivdb.com/tooltips.js'></script>
	<script type='text/javascript'>
		var xivdb_tooltips = 
		{ 
			"language"      : "EN",
			"frameShadow"   : true,
			"compact"       : false,
			"statsOnly"     : false,
			"replaceName"   : false,
			"colorName"     : true,
			"showIcon"      : false,
		} 
	</script>
	<script type='text/javascript' src='{{ cdn('/js/crafting.js') }}'></script>
	<script type='text/javascript' src='{{ cdn('/js/bootstrap-tour.min.js') }}'></script>
	<script type='text/javascript' src='{{ cdn('/js/bootstrap-switch.js') }}'></script>
@stop

@section('banner')

	<a href='#' id='start_tour' class='start btn btn-primary pull-right hidden-print' style='margin-top: 12px;'>
		<i class='glyphicon glyphicon-play'></i>
		Tour
	</a>

	<a href='#' id='csv_download' class='btn btn-info pull-right hidden-print' style='margin-top: 12px; margin-right: 10px;'>
		<i class='glyphicon glyphicon-download-alt'></i>
		Download
	</a>

	<a href='#' id='map_it' class='btn btn-success pull-right hidden-print' style='margin-top: 12px; margin-right: 10px;'>
		<i class='glyphicon glyphicon-globe'></i>
		Map It
	</a>

	<h1 class='csv-filename' style='margin-top: 0;'>
		@if(isset($job))
		@if(count(explode(',', $desired_job)) == 1)
		<i class='class-icon {{ $desired_job }} large hidden-print' style='position: relative; top: 5px;'></i>
		{{ $full_name_desired_job }}
		@else
		Crafting for {{ implode(', ', explode(',', $desired_job)) }} 
		@endif
		@else
		Custom Recipe List
		@endif
	</h1>
	@if(isset($job))
	<h2>recipes between levels {{ $start }} and {{ $end }}</h2>
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
				<th class='text-center' width='100'>Purchase</th>
				<th class='text-center'>Source</th>
			</tr>
		</thead>
		@foreach($reagent_list as $section => $list)
		<?php if (empty($list)) continue; ?>
		<?php if (isset($list[1]) && empty($list[1])) continue; ?>
		<tbody id='{{ preg_replace('/\s|\-/', '', $section) }}-section'>
			<tr>
				<th colspan='6'>
					<button class='btn btn-default pull-right glyphicon glyphicon-chevron-down collapse'></button>
					<div style='margin-top: 4px;'>Origin: {{ $section }}</div>
				</th>
			</tr>
			@foreach($list as $level => $reagents)
			<?php $i = 0; ?>
			@foreach($reagents as $reagent)
			<?php $item =& $reagent['item']; ?>
			<?php
				$requires = array(); $yields = 1;
				$item_level = $item->level;
				$link = 'item/' . $item->id;
				if ($section == 'Pre-Requisite Crafting')
				{
					$yields = $item->recipe[0]->yields;
					foreach ($item->recipe[0]->reagents as $rr_item)
						$requires[] = $rr_item->pivot->amount . 'x' . $rr_item->id;
					$link = 'recipe/' . $item->recipe[0]->id;
				}
			?>
			<tr class='reagent' data-item-id='{{ $item->id }}' data-requires='{{ implode('&', $requires) }}' data-yields='{{ $yields }}'>
				<td class='text-left'>
					@if($level != 0)
					<a class='close ilvl' rel='tooltip' title='Level'>
						{{ $item_level }}
					</a>
					@endif
					<a href='http://xivdb.com/?{{ $link }}' target='_blank'>
						<img src='/img/items/nq/{{ $item->id ?: '../noitemicon' }}.png' width='36' height='36' class='margin-right'><span class='name'>{{ $item->name->term }}</span>
					</a>
					@if ($yields > 1)
					<span class='label label-primary' rel='tooltip' title='Amount Yielded' data-container='body'>
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
						<input type='number' class='form-control obtained text-center' min='0' value='0' step='{{ $yields }}' style='padding: 6px 3px;'>
						<div class='input-group-btn'>
							<button class='btn btn-default obtained-ok' type='button' style='padding: 6px 6px;'><span class='glyphicon glyphicon-ok-circle'></span></button>
						</div>
					</div>
				</td>
				<td class='valign total'>0</td>
				<td>
					@if(count($item->vendors))
					<a href='#' class='btn btn-default vendors{{ $reagent['self_sufficient'] ? ' opaque' : '' }}' rel='tooltip' title='Available for {{ $item->min_price }} gil, Click to load Vendors'>
						<img src='/img/coin.png' width='24' height='24'>
						{{ number_format($item->min_price) }}
					</a>
					@endif
				</td>
				<td class='crafted_gathered'>
					@foreach(array_keys(array_reverse($reagent['cluster_jobs'])) as $cluster_job)
					<i class='class-icon {{ $cluster_job }} clusters' title='{{ $cluster_job }}'></i>
					@endforeach
					@foreach($item->recipe as $recipe)
					<i class='class-icon {{ $recipe->classjob->abbr->term }}' title='{{ $recipe->classjob->abbr->term }}'></i>
					@endforeach
					@if(count($item->beasts))
					<img src='/img/mob.png' width='20' height='20' class='mob-icon beasts' data-item-id='{{ $item->id }}' rel='tooltip' title='Click to load Beasts' data-container='body'>
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
					<button class='btn btn-default pull-right glyphicon glyphicon-chevron-down collapse'></button>
					<div style='margin-top: 4px;'>Crafting List</div>
				</th>
			</tr>
			@foreach($recipes as $recipe)
			<?php
				$requires = array();
				foreach ($recipe->reagents as $item)
					$requires[] = $item->pivot->amount . 'x' . $item->id;
			?>
			<tr class='reagent exempt' data-item-id='{{ $recipe->item->id }}' data-requires='{{ implode('&', $requires) }}' data-yields='{{ $recipe->yields }}'>
				<td class='text-left'>
					<a class='close ilvl' rel='tooltip' title='Level'>
						{{ $recipe->level }}
					</a>
					<a href='http://xivdb.com/?recipe/{{ $recipe->id }}' target='_blank'>
						<img src='/img/items/nq/{{ $recipe->item->id ?: '../noitemicon' }}.png' width='36' height='36' style='margin-right: 5px;'><span class='name'>{{ $recipe->item->name->term }}</span>
					</a>
					@if ($recipe->yields > 1)
					<span class='label label-primary' rel='tooltip' title='Amount Yielded' data-container='body'>
						x {{ $recipe->yields }}
					</span>
					@endif
					<div class='pull-right' style='clear: right;'>
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
				<td class='needed valign hidden-print'>
					<?php 
						$needed = (isset($item_amounts) && isset($item_amounts[$recipe->item->id]) ? $item_amounts[$recipe->item->id] : (1 + (@$recipe->item->quest[0]->amount ? $recipe->item->quest[0]->amount - 1 : 0))); 
						$needed = ceil($needed / $recipe->yields) * $recipe->yields;
					?>

					<input type='number' class='recipe-amount form-control text-center' min='0' step='{{ $recipe->yields }}' value='{{ $needed }}' style='padding: 6px 3px;'>
				</td>
				<td class='valign hidden-print'>
					<div class='input-group'>
						<input type='number' class='form-control obtained text-center' min='0' step='{{ $recipe->yields }}' value='0' style='padding: 6px 3px;'>
						<div class='input-group-btn'>
							<button class='btn btn-default obtained-ok' type='button' style='padding: 6px 6px;'><span class='glyphicon glyphicon-ok-circle'></span></button>
						</div>
					</div>
				</td>
				<td class='valign total'>{{ $needed }}</td>
				<td>
					@if(count($recipe->item->vendors))
					<a href='#' class='btn btn-default vendors{{ $reagent['self_sufficient'] ? ' opaque' : '' }}'>
						<img src='/img/coin.png' width='24' height='24' rel='tooltip' title='Available for {{ $recipe->item->min_price }} gil, Click to load Vendors'>
						{{ number_format($recipe->item->min_price) }}
					</a>
					@endif
				</td>
				<td class='crafted_gathered'>
					<i class='class-icon {{ $recipe->classjob->abbr->term }}' title='{{ $recipe->classjob->abbr->term }}'></i>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>

<a name='options'></a>

<div class='panel panel-info hidden-print'>
	<div class='panel-heading'>
		<small class='pull-right'><em>* Refreshes page</em></small>
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
			<label class='margin-left'>
				Misc Items
			</label>
			<div class='make-switch' data-on='success' data-off='warning' data-on-label='Yes' data-off-label='No'>
				<input type='checkbox' id='misc_items_switch' name='misc_items' value='1' {{ $misc_items ? " checked='checked'" : '' }}>
			</div>
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

<div class='row '>
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
						{{ $quest->classjob->abbr->term }} 
						@endif
						Level {{ $quest->level }}: 
						@if ( ! $quest->item)
							No data! Please help complete the list.
						@else
							{{ $quest->item->name->term }} 
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
	<div class='hidden-print col-sm-{{ isset($job) ? '6' : '12' }}'>
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
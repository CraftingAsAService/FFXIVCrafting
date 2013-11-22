@extends('layout')

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
<!--<script type='text/javascript' src='/js/career.recipes.js'></script>-->
@stop

@section('content')

<h1>{{ $job->name }}'s Producing Career</h1>

<p>List supports the following class{{ count($jobs) > 1 ? 'es' : '' }} between levels {{ $min_level }} and {{ $max_level }}:</p>
<p>@foreach($jobs as $j) <span class='label label-default'>{{ $j }}</span> @endforeach</p>

<div class='table-responsive'>
	<table class='table table-bordered table-striped hide_quests' id='career-items-table'>
		<thead>
			<tr>
				<th class='text-left'>Recipe</th>
				<th class='text-center'>Amount Needed</th>
				@if($show_quests)
				<th class='text-center quest_amount' rel='tooltip' title='Gather this many extra for your quests!' data-container='body'>Quest</th>
				@endif
				<th class='text-center'>Buy</th>
				<th class='text-center'>Vendors</th>
			</tr>
		</thead>
		<tbody>
			@foreach($recipies as $recipe)
			<tr>
				<td>
					@if(isset($recipe->job_level))
					<span class='close' rel='tooltip' title='Job Level'>{{ $recipe->job_level }}</span>
					@endif
					<a href='http://xivdb.com/?recipe/{{ $recipe->recipe_id }}' target='_blank'>
						<img src='/img/items/{{ $recipe->icon ?: '../noitemicon' }}.png' style='margin-right: 5px;'>{{ $recipe->name }}
					</a>
				</td>
				<td class='valign text-center'>
					{{ number_format($recipe->amount + .49) }}
				</td>
				@if($show_quests)
				<td class='valign text-center quest_amount'>
					@if(isset($recipe->quest_level) && $recipe->quest_level > 0)
						<img src='/img/{{ $recipe->quest_quality ? 'H' : 'N' }}Q.png' width='24' height='24' class='quest_marker' rel='tooltip' title='Level {{ $recipe->quest_level }} Quest{{ $recipe->quest_quality ? '<br>HQ Items required' : '' }}' data-container='body' data-html='true'>
						<span class='amount'>{{ number_format($recipe->quest_amount) }}</span>
					@endif
				</td>
				@endif
				<td class='valign text-center'>
					@if($recipe->buy)
						<img src='/img/coin.png' class='stat-vendors pull-left' width='24' height='24'>
						{{ number_format($recipe->buy) }}
					@endif
				</td>
				<td class='valign text-center'>
					@if($recipe->buy)
					<button class='btn btn-default btn-sm' data-toggle='popover' data-container='body' data-html='true' data-placement='left' data-content-id='#vendors_for_{{ $recipe->id }}'>
						{{ $recipe->vendor_count }} Vendor{{ $recipe->vendor_count > 1 ? 's' : '' }} 
						@if($recipe->vendor_count > 1 && count($recipe->vendors) > 1)
						in {{ count($recipe->vendors) }} Area{{ count($recipe->vendors) > 1 ? 's' : '' }}
						@endif
					</button>
					<div class='hidden' id='vendors_for_{{ $recipe->id }}'>
						@foreach($recipe->vendors as $location_name => $vendors)
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
					@endif
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>

<p><small>Amounts are simply an estimate; the math should be correct but I'm not going to guarantee it.  In the least you will need more for Leves or failed syntheses.</small></p>

<p><small>Recipes requiring only one production are not shown.</small></p>

@stop
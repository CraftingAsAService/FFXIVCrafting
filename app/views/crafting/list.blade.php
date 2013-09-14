@extends('layout')

@section('vendor-css')
	<link href='/css/bootstrap-switch.css' rel='stylesheet'>
@stop

@section('javascript')
	<script type='text/javascript' src='http://xivdb.com/tooltips.js'></script>
	<script type='text/javascript' src='/js/crafting.js'></script>
	<script type='text/javascript' src='/js/bootstrap-switch.js'></script>
@stop

@section('content')

<h1>
	@if(isset($job))
	{{ $job->name }} Recipes between Levels {{ $start }} and {{ $end }}
	@else
	Custom Recipe List
	@endif
</h1>

@if($self_sufficient && $first_time)
<div class='alert alert-warning alert-dismissable'>
	You're currently seeing a full list allowing you to be self sufficient.
	If you would rather buy everything, turn this off at the <a href='#options'>bottom</a>.<br>

	<small><em>This is the only time you'll see this message.</em></small>
	<a href='#' class='small' data-dismiss="alert" aria-hidden="true">Close</a>
</div>
@endif

<div class='row'>
	<div class='col-sm-8 col-sm-push-4 item-list'>
		<h3>
			<a href='#howToUse' class='btn btn-sm btn-info pull-right' data-toggle='modal'>How to use</a>
			But first, obtain these items
		</h3>
		<div class='modal fade' id='howToUse'>
			<div class='modal-dialog'>
				<div class='modal-content'>
					<div class='modal-header'>
						<button type='button' class='close' data-dismissal='modal'>&times;</button>
						<h4 class='modal-title'>How to use this tool</h4>
					</div>
					<div class='modal-body'>
						<p>
							Clicking a row will turn it green.  
							If you want to track your progress this way you can.
							The checks will not save if you go to another page.  
							The XIVDB links open in a new window and will not erase your progress.
						</p>

						<h4>On the right</h4>

						<p>
							<strong>1)</strong> First up you'll see items that you can gather yourself.  
							These items will also feed into the last section: Crafting.
						</p>
						<p>
							<strong>2)</strong> Next up are bought materials.  These are items that cannot be gathered.  
							They might be monster drops, however.  
							So do some research in this area if you want to continue with the self sustaining.
						</p>
						<p>
							<strong>3)</strong> Sometimes items aren't flagged as craftable or buyable and are put into the "Other" section.
							These are more than likely monster drops.  Start killing!
						</p>
						<p>
							<strong>4)</strong> Craft all of the items under the "Crafting" section.  
							Some of them might be in your list already, and this has been taken into account.
						</p>
						<p>
							<strong>5)</strong> Finish up by crafting the actual recipes on the left.
						</p>
					</div>
					<div class='modal-footer'>
						<button type='button' class='btn btn-default' data-dismiss='modal'>Okay</button>
					</div>
				</div>
			</div>
		</div>
		<table class='table table-bordered table-striped table-responsive text-center'>
			<thead>
				<tr>
					<th class='invisible'>&nbsp;</th>
					<th class='text-center'>Item</th>
					<th class='text-center'>Needed</th>
					<th class='text-center'>Can be Bought</th>
					<th class='text-center'>Source</th>
					<th class='text-center'><input type='checkbox' disabled='disabled' checked='checked'></th>
				</tr>
			</thead>
			@foreach($reagent_list as $section => $list)
			<?php if (empty($list)) continue; ?>
			<tbody>
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
				<tr class='reagent'>
					@if($i++ == 0)
					<th rowspan='{{ count($reagents) }}' class='text-center' style='vertical-align: middle; background-color: #f5f5f5;'>
						@if($level != 0 && $section != 'Bought')
						{{ $level }}
						@endif
					</th>
					@endif
					<td>
						<a href='http://xivdb.com/{{ $item->href }}' target='_blank'>{{ $item->name }}</a>
					</td>
					<td>
						{{ $reagent['make_this_many'] }}
					</td>
					<td>
						@if($item->vendors)
						<div{{ $reagent['self_sufficient'] ? ' class="opaque"' : '' }}>
							<img src='/img/coin.png' class='stat-vendors' width='24' height='24'>
							{{ number_format($item->gil) }}
						</div>
						@endif
					</td>
					<td class='crafted_gathered'>
						@foreach($item->jobs as $reagent_job)
						<span class='alert alert-{{ $reagent_job->abbreviation == $reagent['self_sufficient'] ? 'success' : 'info' }}'>
							<img src='/img/classes/{{ $reagent_job->abbreviation }}.png' rel='tooltip' title='{{ $job_list[$reagent_job->abbreviation] }}'>
						</span>
						@endforeach
						@foreach($item->recipes as $recipe)
						<span class='alert alert-{{ $recipe->job->abbreviation == $reagent['self_sufficient'] ? 'success' : 'warning' }}''>
							<img src='/img/classes/{{ $recipe->job->abbreviation }}.png' rel='tooltip' title='{{ $job_list[$recipe->job->abbreviation] }}'>
						</span>
						@endforeach
					</td>
					<th class='text-center'>
						<input type='checkbox'>
					</th>
				</tr>
				@endforeach
				@endforeach
			</tbody>
			@endforeach
		</table>
	</div>
	<div class='col-sm-4 crafting-list col-sm-pull-8'>
		<h3>
			<button class='btn btn-default pull-right glyphicon glyphicon-chevron-down collapse'></button>
			What you'll be crafting
		</h3>

		<div class='recipe-holder'>
			@foreach($recipes as $recipe)
			<div class='well'>
				<a class='close' rel='tooltip' title='Level'>
					{{ $recipe->level }}
				</a>
				@if( ! isset($job))
				<div class='pull-right' style='clear: right;'>
					<img src='/img/classes/{{ $recipe['abbreviation'] }}.png' rel='tooltip' title='{{ $job_list[$recipe['abbreviation']] }}'>
				</div>
				@endif
				<a href='http://xivdb.com/?recipe/{{ $recipe->id }}' class='recipe-name' target='_blank'>
					{{ $recipe->name }}
				</a>
				<p><small>Yields: {{ $recipe->yields }}</small></p>
				{{--@foreach($recipe->reagents as $reagent)

				@endforeach--}}
			</div>
			@endforeach
		</div>
	</div>
</div>

<a name='options'></a>

<div class='panel panel-info'>
	<div class='panel-heading'>
		<h3 class='panel-title'>Options</h3>
	</div>
	<div class='panel-body'>
		@if(isset($job))
		<form action='/crafting' method='post' role='form' class='form-horizontal'>
			<input type='hidden' name='class' value='{{ $job->abbreviation }}'>
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
		<form action='/crafting/gear' method='post' role='form' class='form-horizontal'>
			<input type='hidden' name='ids' value='{{ implode(':', $item_ids) }}'>
			<label>
				Self Sufficient
			</label>
			<div class='make-switch' data-on='success' data-off='warning' data-on-label='Yes' data-off-label='No'>
				<input type='checkbox' id='self_sufficient_switch' name='self_sufficient' value='1' {{ $self_sufficient ? " checked='checked'" : '' }}>
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
		<div class='panel panel-primary'>
			<div class='panel-heading'>
				<h3 class='panel-title'>Leveling Information</h3>
			</div>
			<div class='panel-body'>
				<p>Be efficient, make quest items in advance!</p>

				<ul>
					@foreach($quest_items as $quest)
					<li>
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

				<p><em>Repeatable Turn-in Leve information coming soon.</em></p>
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

<div class='well'>
	<p>
		<strong>Crafting as a Service</strong> couldn't have done it without these resources.  Please support them!
	</p>
	<p>
		<small><em>
			<a href='http://xivdb.com/' target='_blank'>XIVDB</a> for their tooltips and data.
			<a href='http://www.daevaofwar.net/index.php?/topic/628-all-crafting-turn-ins-to-50/' target='_blank'>These</a>
			<a href='http://www.daevaofwar.net/index.php?/topic/629-all-gathering-turn-ins-to-50/' target='_blank'>Posts</a>
			for some quest knowledge.
			And of course <a href='http://www.finalfantasyxiv.com/' target='_blank'>Square Enix</a>.
		</em></small>
	</p>
</div>

@stop
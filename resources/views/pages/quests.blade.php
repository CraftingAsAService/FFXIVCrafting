@extends('app')

@section('meta')
	<meta name="robots" content="nofollow">
@stop

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/bootstrap-multiselect.js') }}'></script>
	<script type='text/javascript' src='{{ cdn('/js/home.js') }}'></script>
	<script src='{{ cdn('/js/quests.js') }}'></script>
@stop

@section('banner')
	<a href='/quests' class='btn btn-default pull-right' id='load-setup' rel='tooltip' title='Load saved setup'><i class='glyphicon glyphicon-folder-open'></i></a>
	<a href='#' class='btn btn-default margin-right pull-right' id='save-setup' rel='tooltip' title='Save setup for later'><i class='glyphicon glyphicon-floppy-disk'></i></a>

	<h1>Quest Information</h1>
@stop

@section('content')
<div class='alert alert-info'>
	<a href='#' data-toggle='popover' data-container='body' data-placement='bottom' title='Data Warning' data-content='Amounts required, HQ requirement and materia melding data are missing.  Generally speaking quest requirements at and above level 25 require a High Quality item.  Visit your local quest giver for more information.'>Data Warning, Please Read</a>
</div>
<div class='panel panel-default'>
	<div class='panel-heading'>
		Quest Filter
	</div>
	<div class='panel-body'>
		<form class='quest-form form form-inline'>
			<div class='row'>
				<div class='col-sm-12'>
					<button type='button' role='button' class='filter-form btn btn-success pull-right'>
						Filter &raquo;
					</button>

					<div class='form-group'>
						<label>Class</label>
						<select class='multiselect hidden' multiple='multiple' id='class-selector'>
							@foreach($job_list as $job)
							<option value='{{ $job->abbr }}'{{ $job->id == reset($job_ids) ? ' selected="selected"' : '' }}>
								{{ $job->name }}
							</option>
							@endforeach
						</select>
					</div>

					<div class='form-group margin-left'>
						<label>Min Level</label>
						<input type='number' min='0' max='{{ config('site.max_level') }}' step='5' value='1' class='form-control text-center' id='min-level'>
					</div>

					<div class='form-group margin-left'>
						<label>Max Level</label>
						<input type='number' min='0' max='{{ config('site.max_level') }}' step='5' value='{{ config('site.max_level') }}' class='form-control text-center' id='max-level'>
					</div>

					<div class='form-group margin-left'>
						<input type='text' id='quest_item' placeholder='Item Name Search' class='form-control quest-text-search'>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<div class='table-responsive'>
	<table class='table table-bordered table-striped' id='quest-table'>
		<thead>
			<tr>
				<th class='invisible'>&nbsp;</th>
				<th class='text-center'>Item, Level</th>
				{{-- <th class='text-center'>Amount</th> --}}
				{{-- <th class='text-center'>Quality</th> --}}
				{{-- <th>Notes</th> --}}
				<th class='text-center valign' rel='tooltip' title='Add to Crafting List'>
					<i class='glyphicon glyphicon-shopping-cart'></i>
					<i class='glyphicon glyphicon-plus'></i>
				</th>
			</tr>
		</thead>
		<tbody>
			@foreach($quests as $job_abbr => $quest_list)
			@foreach($quest_list as $quest)
			@foreach($quest->items as $item)
			<tr class='quest{{ $quest->job->id != reset($job_ids) ? ' hidden' : '' }}' data-abbr='{{ $quest->job->abbr }}'>
				<td width='24' class='valign'>
					<i class='class-icon {{ $quest->job->abbr }}'></i>
				</td>
				<td>
					<span class='close level' rel='tooltip' title='Quest Level'>{{ $quest->level }}</span>
					<a href='http://xivdb.com/?item/{{ $item['id'] }}' class='item-name' target='_blank'>
						<img src='{{ assetcdn('item/' . $item['icon'] . '.png') }}' width='36' height='36' style='margin-right: 10px;'><span class='name'>{{ $item['name'] }}</span>
					</a>
				</td>
				{{-- <td class='text-center amount'>{{ $quest->amount }}</td> --}}
				{{-- <td class='text-center'>
					@if($quest->quality)
					<img src='/img/HQ.png' width='24' height='24'>
					@endif
				</td> --}}
				{{-- <td>{{ $quest->notes }}</td> --}}
				<td class='text-center valign'>
					<button class='btn btn-default add-to-list' data-item-id='{{ $item['id'] }}' data-item-name='{{ $item['name'] }}'>
						<i class='glyphicon glyphicon-shopping-cart'></i>
						<i class='glyphicon glyphicon-plus'></i>
					</button>
				</td>
			</tr>
			@endforeach
			@endforeach
			@endforeach
		</tbody>
	</table>
</div>

@stop
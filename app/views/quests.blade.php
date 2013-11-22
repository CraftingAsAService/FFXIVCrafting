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
	<script type='text/javascript' src='/js/bootstrap-multiselect.js'></script>
	<script src='/js/home.js'></script>
	<script src='/js/quests.js'></script>
@stop

@section('content')

<h1>Quest Information</h1>

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
							@foreach(array('CRP','BSM','ARM','GSM','LTW','WVR','ALC','CUL', 'MIN','BTN','FSH') as $job)
							<option value='{{ $job }}'{{ $job == 'CRP' ? ' selected="selected"' : '' }}>{{ $job_list[$job] }}</option>
							@endforeach
						</select>
					</div>

					<div class='form-group margin-left'>
						<label>Min Level</label>
						<input type='number' min='0' max='50' step='5' value='1' class='form-control text-center' id='min-level'>
					</div>

					<div class='form-group margin-left'>
						<label>Max Level</label>
						<input type='number' min='0' max='50' step='5' value='50' class='form-control text-center' id='max-level'>
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
				<th class='text-center'>Amount</th>
				<th class='text-center'>Quality</th>
				<th>Notes</th>
				<th class='text-center valign' rel='tooltip' title='Add to Shopping List'>
					<i class='glyphicon glyphicon-shopping-cart'></i>
					<i class='glyphicon glyphicon-plus'></i>
				</th>
			</tr>
		</thead>
		<tbody>
			@foreach($quests as $job => $quest_list)
			@foreach($quest_list as $q)
			<tr class='quest{{ $q->job->abbreviation != 'CRP' ? ' hidden' : '' }}'>
				<td width='24' class='valign'>
					<img src='/img/classes/{{ $q->job->abbreviation }}.png' class='class-icon' rel='tooltip' title='{{ $q->job->name }}' data-abbr='{{ $q->job->abbreviation }}'>
				</td>
				<td>
					<span class='close level' rel='tooltip' title='Quest Level'>{{ $q->level }}</span>
					<a href='http://xivdb.com/{{ $q->recipe ? ('?recipe/' . $q->recipe->id) : ('?item/' . $q->item->id) }}' class='item-name' target='_blank'>
						<img src='/img/items/{{ $q->item->icon ?: '../noitemicon' }}.png' style='margin-right: 10px;'><span class='name'>{{ $q->item->name }}</span>
					</a>
				</td>
				<td class='text-center amount'>{{ $q->amount }}</td>
				<td class='text-center'>
					@if($q->quality)
					<img src='/img/HQ.png' width='24' height='24'>
					@endif
				</td>
				<td>{{ $q->notes }}</td>
				<td class='text-center valign'>
					<button class='btn btn-default add-to-list' data-item-id='{{ $q->item->id }}' data-item-name='{{{ $q->item->name }}}'>
						<i class='glyphicon glyphicon-shopping-cart'></i>
						<i class='glyphicon glyphicon-plus'></i>
					</button>
				</td>
			</tr>
			@endforeach
			@endforeach
		</tbody>
	</table>
</div>

@stop
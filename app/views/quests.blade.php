@extends('layout')

@section('javascript')
	<script type='text/javascript' src='http://xivdb.com/tooltips.js'></script>
	<script src='/js/home.js'></script>
	<script src='/js/quests.js'></script>
@stop

@section('content')

<h1>Quest Requirements</h1>

<div class='panel'>
	<div class='panel-body'>
		<div class="btn-toolbar">
			<div class='btn-group' data-toggle='buttons'>
				@foreach(array('CRP','BSM','ARM','GSM','LTW','WVR','ALC','CUL') as $job)
				<label class='btn btn-primary class-selector' data-job='{{ $job }}'>
					<input type='radio' name='class' value='{{ $job }}'>
					<img src='/img/classes/{{ $job }}.png' rel='tooltip' title='{{ $job_list[$job] }}'>
				</label>
				@endforeach
			</div>
			<div class='btn-group' data-toggle='buttons'>
				@foreach(array('MIN','BTN','FSH') as $job)
				<label class='btn btn-info class-selector' data-job='{{ $job }}'>
					<input type='radio' name='class' value='{{ $job }}'> 
					<img src='/img/classes/{{ $job }}.png' rel='tooltip' title='{{ $job_list[$job] }}'>
				</label>
				@endforeach
			</div>
		</div>
	</div>
</div>

@foreach($quests as $job => $quest_list)
<table id='{{ $job }}' class='quest-table table table-bordered table-striped'>
	<thead>
		<tr>
			<th>
				<img src='/img/classes/{{ $job }}.png' rel='tooltip' title='{{ $job_list[$job] }}'>
				{{ $job_list[$job] }}
			</th>
			<th class='text-center'>Amount Required</th>
			<th class='text-center'>Quality</th>
			<th>Notes</th>
			<th class='text-center valign' rel='tooltip' title='Add to Shopping List'>
				<i class='glyphicon glyphicon-shopping-cart'></i>
				<i class='glyphicon glyphicon-plus'></i>
			</th>
		</tr>
	</thead>
	<tbody>
		@foreach($quest_list as $q)
		<tr>
			<td>
				<span class='close' rel='tooltip' title='Quest Level'>{{ $q->level }}</span>
				<a href='http://xivdb.com/{{ $q->recipe ? ('?recipe/' . $q->recipe->id) : $q->item->href }}' class='item-name' target='_blank'>
					<img src='/img/items/{{ $q->item->icon ?: '../noitemicon.png' }}' style='margin-right: 10px;'>{{ $q->item->name }}
				</a>
			</td>
			<td class='text-center'>{{ $q->amount }}</td>
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
	</tbody>
</table>

@endforeach

@stop
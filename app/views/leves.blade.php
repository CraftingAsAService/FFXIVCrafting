@extends('layout')

@section('javascript')
	<script type='text/javascript' src='http://xivdb.com/tooltips.js'></script>
	<script src='/js/home.js'></script>
	<script src='/js/leves.js'></script>
@stop

@section('content')

<h1>Leve Information</h1>

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
			{{--
			<div class='btn-group' data-toggle='buttons'>
				@foreach(array('MIN','BTN','FSH') as $job)
				<label class='btn btn-info class-selector' data-job='{{ $job }}'>
					<input type='radio' name='class' value='{{ $job }}'> 
					<img src='/img/classes/{{ $job }}.png' rel='tooltip' title='{{ $job_list[$job] }}'>
				</label>
				@endforeach
			</div>
			--}}
		</div>
	</div>
</div>

@foreach($leves as $job => $leve_list)
<table id='{{ $job }}' class='leve-table table table-bordered table-striped'>
	<thead>
		<tr>
			<th>
				<img src='/img/classes/{{ $job }}.png' rel='tooltip' title='{{ $job_list[$job] }}'>
				{{ $job_list[$job] }} - {{ end($leve_list)->major->name }}
			</th>
			<th class='text-center'>Amount</th>
			<th class='text-center'>Type</th>
			<th class='text-center'>Leve Name</th>
			<th class='text-center'>XP<br>Reward</th>
			<th class='text-center'>Gil<br>Reward</th>
			<th class='text-center'>Location</th>
			<th class='text-center valign' rel='tooltip' title='Add to Shopping List'>
				<i class='glyphicon glyphicon-shopping-cart'></i>
				<i class='glyphicon glyphicon-plus'></i>
			</th>
		</tr>
	</thead>
	<tbody>
		@foreach($leve_list as $leve)
		<tr>
			<td{{ $leve->triple ? ' class="triple" rel="tooltip" title="Triple Leve" data-container="body"' : '' }}>
				<span class='close' rel='tooltip' title='Leve Level'>{{ $leve->level }}</span>
				<a href='http://xivdb.com/{{ $leve->item->href }}' class='item-name' target='_blank'>
					<img src='/img/items/{{ $leve->item->icon ?: '../noitemicon.png' }}' style='margin-right: 10px;'>{{ $leve->item->name }}
				</a>
			</td>
			<td class='text-center'>{{ $leve->amount }}</td>
			<td class='text-center'>{{ $leve->type }}</td>
			<td class='text-center'>{{ $leve->name }}</td>
			<td class='text-center'>{{ number_format($leve->xp) }} XP</td>
			<td class='text-center'>
				<img src='/img/coin.png' class='stat-vendors' width='24' height='24'>
				{{ number_format($leve->gil) }}
			</td>
			<td class='text-center'>
				<div>{{ ! empty($leve->minor) ? $leve->minor->name : '' }}</div>
				<div>{{ ! empty($leve->location) ? $leve->location->name : '' }}</div>
			</td>
			<td class='text-center valign'>
				<button class='btn btn-default add-to-list' data-item-id='{{ $leve->item->id }}' data-item-name='{{{ $leve->item->name }}}' data-item-quantity='{{{ $leve->amount }}}'>
					<i class='glyphicon glyphicon-shopping-cart'></i>
					<i class='glyphicon glyphicon-plus'></i>
				</button>
			</td>
		</tr>
		@endforeach
	</tbody>
</table>

@endforeach

<div class='well'>
	Information gathered from <a href='http://www.bluegartr.com/threads/118238-DoH-DoL-Leves-Dyes-Material-Tiers' target='_blank'>BluGartr user Seravi Edalborez</a>.  Thanks!
</div>

@stop
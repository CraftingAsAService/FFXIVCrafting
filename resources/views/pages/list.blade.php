@extends('app')

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/list.js') }}'></script>
@stop

@section('banner')
	<h1>
		Crafting List
	</h1>
@stop

@section('content')

@if(isset($incomplete_saved))
<div class='alert alert-warning'>
	Your saved list was malformed!  Please check the URL again.
</div>
@endif

@if(empty($list))
<p>Your list is empty.</p>
<p>Visit the <a href='/recipes'>Recipe Book</a> to add items.</p>
<p>Also, clicking on a DOH class icon (like <img src='/img/jobs/CRP.png' width='24' height='24'>) on other pages will add that item to your crafting list.</p>
@else

@if(isset($saved))
<div class='alert alert-info'>
	Saved list retrieved.  @if(isset($flushed))Previous contents were removed.@endif
</div>
@endif

<div class='table-responsive'>
	<table class='table table-bordered table-striped text-center' id='list'>
		<thead>
			<tr>
				<th class='invisible'>&nbsp;</th>
				<th class='text-center' width='10%'>Yields</th>
				<th class='text-center' width='10%'>Amount</th>
				<th class='text-center' width='10%'>Delete</th>
			</tr>
		</thead>
		<tbody>
		@foreach($list as $item_id => $list_item)
		@if (is_null($list_item['item']))
			{{--
			<tr>
				<td colspan='4'>
					Could not find item {{ $item_id }}
				</td>
			</tr>
			--}}
		@else
			<tr data-item-id='{{ $item_id }}' data-item-name='{{{ $list_item['item']->display_name }}}'>
				<td class='text-left'>
					<a href='{{ item_link() . $list_item['item']->id }}' target='_blank'>
						<img src='{{ icon($list_item['item']->icon) }}' width='36' height='36' style='margin-right: 5px;'>{{ $list_item['item']->display_name }}
					</a>
				</td>
				<td class='text-center valign'>
					{{ $list_item['item']->recipes[0]->yield }}
				</td>
				<td class='text-center valign'>
					<input type='number' class='form-control update-list-item text-center' value='{{ $list_item['amount'] }}'>
				</td>
				<td class='text-center valign'>
					<i class='glyphicon glyphicon-trash delete-list-item text-danger'></i>
				</td>
			</tr>
		@endif
		@endforeach
		</tbody>
	</table>
</div>
<a href='/list/flush' class='btn btn-danger pull-right'>Delete All</a>
<button class='btn btn-info pull-right margin-right' data-toggle='modal' data-target='#savedList'>Shareable Link</button>
@php
	$teamcraftString = base64_encode(implode(';', array_map(function($listItem) {
		return $listItem['item']->id . ',null,' . $listItem['amount'];
	}, $list)));
@endphp
<a href='https://ffxivteamcraft.com/import/{!! $teamcraftString !!}' class='btn btn-default pull-right margin-right' target='_blank'>Export to Teamcraft <small><i class='glyphicon glyphicon-new-window'></i></small></a>
<a href='/crafting/from-list?self_sufficient=1' class='btn btn-success fix-self-sufficient'>Craft These Items &raquo;</a>

@endif

@stop

@section('modals')
<div class="modal fade" id='savedList' data-base-url='http://{{ $_SERVER['HTTP_HOST'] }}/list/saved/'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Copy this link</h4>
			</div>
			<div class="modal-body">
				<textarea class='form-control'></textarea>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
@stop
@extends('wrapper.layout')

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/list.js') }}'></script>
@stop

@section('banner')
	<h1>
		Crafting List
	</h1>
@stop

@section('content')

@if(empty($list))
<p>Your list is empty.</p>
<p>Visit the <a href='/recipes'>Recipe Book</a> to add items.</p>
<p>Also, clicking on a DOH class icon (like <img src='/img/classes/CRP.png'>) on other pages will add that item to your crafting list.</p>
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
		@if ($list_item['item'] == null)
			{{--
			<tr>
				<td colspan='4'>
					Could not find item {{ $item_id }}
				</td>
			</tr>
			--}}
		@else
			<tr data-item-id='{{ $item_id }}' data-item-name='{{{ $list_item['item']->name->term }}}'>
				<td class='text-left'>
					<a href='http://xivdb.com/?recipe/{{ $list_item['item']->recipe[0]->id }}' target='_blank'>
						<img src='{{ assetcdn('items/nq/' . $list_item['item']->id . '.png') }}' width='36' height='36' style='margin-right: 5px;'>{{ $list_item['item']->name->term }}
					</a>
				</td>
				<td class='text-center valign'>
					{{ $list_item['item']->recipe[0]->yields }}
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
<button class='btn btn-info pull-right margin-right' data-toggle='modal' data-target='#savedList'>Get Link</button>
<a href='/crafting/list?List:::1' class='btn btn-success'>Craft These Items &raquo;</a>

@endif

@stop

@section('modals')
<div class="modal fade" id='savedList'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Copy this link</h4>
			</div>
			<div class="modal-body">
				<textarea class='form-control'>http://craftingasaservice.com/list/saved/{{ $saved_link }}</textarea>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
@stop
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
<script type='text/javascript' src='/js/list.js'></script>
@stop

@section('content')

<h1>
	<i class='glyphicon glyphicon-shopping-cart'></i>
	Crafting List
</h1>

@if(empty($list))
<p>Your list is empty.</p>
<p>Visit the <a href='/recipes'>Recipe Book</a> to add items.</p>
<p>Also, clicking on a DOH class icon (like <img src='/img/classes/CRP.png'>) on other pages will add that item to your crafting list.</p>
@else
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
			<tr data-item-id='{{ $item_id }}' data-item-name='{{{ $list_item['item']->name }}}'>
				<td class='text-left'>
					<a href='http://xivdb.com/?recipe/{{ $list_item['item']->recipes[0]->id }}' target='_blank'>
						<img src='/img/items/{{ $list_item['item']->recipes[0]->icon ?: '../noitemicon' }}.png' style='margin-right: 5px;'>{{ $list_item['item']->recipes[0]->name }}
					</a>
				</td>
				<td class='text-center valign'>
					{{ $list_item['item']->recipes[0]->yields }}
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
<a href='/crafting/list?List:::1' class='btn btn-success'>Craft These Items &raquo;</a>
@endif

@stop
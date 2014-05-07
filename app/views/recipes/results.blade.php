{{-- inside a tbody --}}
<?php $count = 0; ?>
@foreach($list as $recipe)
<?php $count++; ?>
<tr>
	<td class='text-left valign'>
		<a href='http://xivdb.com/?recipe/{{ $recipe->id }}' target='_blank'>
			<img src='/img/items/nq/{{ $recipe->item_id ?: '../noitemicon' }}.png' width='36' height='36' style='margin-right: 5px;'>{{ $recipe->item->name->term }}
		</a>
	</td>
	<td class='text-center valign'>
		<i class='class-icon class-id-{{ $recipe->classjob_id }} add-to-list' data-item-id='{{ $recipe->item_id }}' data-item-name='{{{ $recipe->item->name->term }}}'></i>
	</td>
	<td class='text-center valign'>
		{{ $recipe->level }}
	</td>
	<td class='text-center valign'>
		<button class='btn btn-default add-to-list' data-item-id='{{ $recipe->item_id }}' data-item-name='{{{ $recipe->name->term }}}'>
			<i class='glyphicon glyphicon-shopping-cart'></i>
			<i class='glyphicon glyphicon-plus'></i>
		</button>
	</td>
</tr>
@endforeach
@if($count == 0)
<tr>
	<td colspan='4'>
		No Results
	</td>
</tr>
@endif
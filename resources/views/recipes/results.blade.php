{{-- inside a tbody --}}
<?php $count = 0; ?>
@foreach($list as $recipe)
<?php $count++; ?>
<tr>
	<td class='text-left valign'>
		{{-- <a href='http://xivdb.com/?recipe/{{ $recipe->recipe_id }}' target='_blank'> --}}
			<img src='{{ assetcdn('item/' . $recipe->item->icon . '.png') }}' width='36' height='36' style='margin-right: 5px;'>{{ $recipe->item->name }}
		{{-- </a> --}}
	</td>
	<td class='text-center valign'>
		<i class='class-icon class-id-{{ $recipe->job_id }} add-to-list' data-item-id='{{ $recipe->item_id }}' data-item-name='{{ $recipe->item->name }}'></i>
	</td>
	<td class='text-center valign'>
		{{ $recipe->recipe_level }}
	</td>
	<td class='text-center valign'>
		<button class='btn btn-{{ in_array($recipe->item_id, $crafting_list_ids) ? 'success' : 'default' }} add-to-list success-after-add' data-item-id='{{ $recipe->item_id }}' data-item-name='{{ $recipe->item->name }}' title='Already In Crafting List'>
			<i class='glyphicon glyphicon-shopping-cart'></i>
			<i class='glyphicon glyphicon-{{ in_array($recipe->item_id, $crafting_list_ids) ? 'ok' : 'plus' }}'></i>
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
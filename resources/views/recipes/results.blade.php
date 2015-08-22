{{-- inside a tbody --}}
@forelse($list as $recipe)
<tr>
	<td class='text-left valign'>
		<a href='http://xivdb.com/?item/{{ $recipe->item->id }}' target='_blank'>
			<img src='{{ assetcdn('item/' . $recipe->item->icon . '.png') }}' width='36' height='36' style='margin-right: 5px;'>{{ $recipe->item->display_name }}
		</a>
	</td>
	<td class='text-center valign'>
		@if (count($recipe->job))
		<img src='/img/jobs/{{ strtoupper($recipe->job->abbr) }}.png' width='24' height='24' rel='tooltip' title='{{ $recipe->job->name }}'>
		@endif
	</td>
	<td class='text-center valign'>
		{{ $recipe->recipe_level }}
	</td>
	<td class='text-center valign'>
		<button class='btn btn-{{ in_array($recipe->item_id, $crafting_list_ids) ? 'success' : 'default' }} add-to-list success-after-add' data-item-id='{{ $recipe->item_id }}' data-item-name='{{ $recipe->item->display_name }}' title='Already In Crafting List'>
			<i class='glyphicon glyphicon-shopping-cart'></i>
			<i class='glyphicon glyphicon-{{ in_array($recipe->item_id, $crafting_list_ids) ? 'ok' : 'plus' }}'></i>
		</button>
	</td>
</tr>
@empty
<tr>
	<td colspan='4'>
		No Results
	</td>
</tr>
@endforelse
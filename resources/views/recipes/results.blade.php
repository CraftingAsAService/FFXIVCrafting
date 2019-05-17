{{-- inside a tbody --}}
@forelse($list as $recipe)
@if (isset($recipe->item->id))
<tr>
	<td class='text-left valign'>
		<a href='{{ item_link() . $recipe->item->id }}' target='_blank'>
			<img src='{{ icon($recipe->item->icon) }}' width='36' height='36' style='margin-right: 5px;'>{{ $recipe->item->display_name }}
		</a>
	</td>
	<td class='text-center valign'>
		@if (isset($recipe->job) && $recipe->job->count())
		<img src='/img/jobs/{{ strtoupper($recipe->job->abbr) }}.png' width='24' height='24' rel='tooltip' title='{{ $recipe->job->name }}'>
		@endif
	</td>
	<td class='text-center valign'>
		{{ $recipe->item->ilvl }}
	</td>
	<td class='text-center valign'>
		<button class='btn btn-{{ in_array($recipe->item_id, $crafting_list_ids) ? 'success' : 'default' }} add-to-list success-after-add' data-item-id='{{ $recipe->item_id }}' data-item-name='{{ $recipe->item->display_name }}' title='Already In Crafting List'>
			<i class='glyphicon glyphicon-shopping-cart'></i>
			<i class='glyphicon glyphicon-{{ in_array($recipe->item_id, $crafting_list_ids) ? 'ok' : 'plus' }}'></i>
		</button>
	</td>
</tr>
@endif
@empty
<tr>
	<td colspan='4'>
		No Results
	</td>
</tr>
@endforelse
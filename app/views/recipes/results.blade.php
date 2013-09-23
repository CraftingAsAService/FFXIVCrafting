{{-- inside a tbody --}}
@if( ! $list)
<tr colspan='4'>
	<td>
		No Results
	</td>
</tr>
@else
@foreach($list as $recipe)
<tr>
	<td class='text-left valign'>
		<a href='http://xivdb.com/?recipe/{{ $recipe->id }}' target='_blank'>
			<img src='/img/items/{{ $recipe->icon ?: '../noitemicon.png' }}' style='margin-right: 5px;'>{{ $recipe->name }}
		</a>
	</td>
	<td class='text-center valign'>
		<img src='/img/classes/{{ $recipe->job->abbreviation }}.png' rel='tooltip' title='{{ $recipe->job->name }}' class='add-to-list' data-item-id='{{ $recipe->item_id }}' data-item-name='{{{ $recipe->name }}}'>
	</td>
	<td class='text-center valign'>
		{{ $recipe->level }}
	</td>
	<td class='text-center valign'>
		<button class='btn btn-default add-to-list' data-item-id='{{ $recipe->item_id }}' data-item-name='{{{ $recipe->name }}}'>
			<i class='glyphicon glyphicon-shopping-cart'></i>
			<i class='glyphicon glyphicon-plus'></i>
		</button>
	</td>
</tr>
@endforeach
@endif
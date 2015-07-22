<div class="modal fade entity-modal" id='recipes-for-{{ $item->id }}'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><img src="/img/hq_icon.png" width="24" height="24"> {{ $item->name }} Recipes</h4>
			</div>
			<div class="modal-body">
				@foreach ($item->recipes as $recipe)
				<table class='table table-bordered table-striped'>
					<caption><i class='class-icon {{ $recipe->job->abbr }} margin-right'></i>{{ $recipe->job->name }} (lvl {{ $recipe->recipe_level }})</caption>
					<thead>
						<tr>
							<th>Item</th>
							<th class='text-center'>Amount</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($recipe->reagents as $reagent)
						<tr>
							<td>
								<img src='{{ assetcdn('item/' . $reagent->icon . '.png') }}' width='36' height='36' class='margin-right'><span class='name'>{{ $reagent->name }}</span>
							</td>
							<td class='text-center'>
								{{ $reagent->pivot->amount }}
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
				@endforeach
			</div>
		</div>
	</div>
</div>
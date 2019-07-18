<div class="modal fade entity-modal" id='recipes-for-{{ $item->id }}'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><img src="/img/hq-icon.png" width="24" height="24"> {{ $item->name }} Recipes</h4>
			</div>
			<div class="modal-body">
				@foreach ($item->recipes as $recipe)
				<table class='table table-bordered table-striped'>
					<caption>
						@if (is_null($recipe->job))
						<img src='/img/FC.png' width='20' height='20' class='margin-right'></i>Free Company Craft
						@else
						<img src='/img/jobs/{{ strtoupper($recipe->job->abbr) }}.png' width='32' height='32'>{{ $recipe->job->name }}
						@endif
						(lvl {{ $recipe->recipe_level }})
					</caption>
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
								<img src='{{ icon($reagent->icon) }}' width='36' height='36' class='margin-right'><span class='name'>{{ $reagent->name }}</span>
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
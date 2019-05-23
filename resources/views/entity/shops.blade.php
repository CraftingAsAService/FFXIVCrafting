<div class="modal fade entity-modal" id='shops-for-{{ $item->id }}'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><img src="/img/shop.png" width="24" height="24"> {{ $item->name }} Shops - {{ number_format($item->price, 0, '.', ',') }} <img src="/img/coin.png" width="24" height="24"></h4>
			</div>
			<div class="modal-body">
				<table class='table table-bordered table-striped'>
					<thead>
						<tr>
							<th>Vendor</th>
							<th>Location</th>
							<th>Coordinates</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($shops as $npc)
						<tr>
							<td>
								{{ ucwords($npc['name']) }}
							</td>
							<td>
								{{ $npc['location'] }}
							</td>
							<td>
								@if( ! ($npc['x'] == 0 || $npc['y'] == 0))
								{{ $npc['x'] }} x {{ $npc['y'] }}
								@endif
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
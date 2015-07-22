<div class="modal fade entity-modal" id='mobs-for-{{ $item->id }}'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><img src="/img/mob.png" width="24" height="24"> {{ $item->name }} Mobs</h4>
			</div>
			<div class="modal-body">
				<table class='table table-bordered table-striped'>
					<thead>
						<tr>
							<th>Name</th>
							<th>Location</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($item->mobs as $mob)
						<tr>
							<td>
								<span class='pull-right'>lvl {{ $mob->level }}</span>
								{{ $mob->name }}
							</td>
							<td>
								{{ $mob->location->name }}
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
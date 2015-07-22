<div class="modal fade entity-modal" id='instances-for-{{ $item->id }}'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><img src="/img/dungeon.png" width="24" height="24"> {{ $item->name }} Instances</h4>
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
						@foreach ($item->instances as $instance)
						<tr>
							<td>
								{{ $instance->name }}
							</td>
							<td>
								@if(preg_replace('/[^\w]/', '', $instance->name) == preg_replace('/[^\w]/', '', $instance->location->name))
								{{ $instance->location->location->name }}
								@else
								{{ $instance->location->name }}
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
<div class="modal fade entity-modal" id='{{ strtolower($job->abbr) }}nodes-for-{{ $item->id }}'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><img src="/img/jobs/{{ strtoupper($job->abbr) }}.png" width="24" height="24"> {{ $item->name }} {{ $job->name }} Nodes</h4>
			</div>
			<div class="modal-body">
				<table class='table table-bordered table-striped'>
					<thead>
						<tr>
							<th>Zone</th>
							<th>Area</th>
							<th>Type</th>
							<th>Coordinates</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($nodes as $zone => $areas)
						@foreach ($areas as $area => $types)
						@foreach ($types as $type => $coordinates)
						<tr>
							<td>{{ $zone }}</td>
							<td>{{ $area }}</td>
							<td>{{ $type }}</td>
							<td>{{ $coordinates }}</td>
						</tr>
						@endforeach
						@endforeach
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
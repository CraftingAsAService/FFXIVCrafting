<div class="modal fade entity-modal" id='quests-for-{{ $item->id }}'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><img src="/img/quest.png" width="24" height="24"> {{ $item->name }} Quests</h4>
			</div>
			<div class="modal-body">
				<table class='table table-bordered table-striped'>
					<thead>
						<tr>
							<th>Name, Level</th>
							<th>Location</th>
							<th class='text-center'>Rewarded</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($item->quest_rewards as $quest)
						<tr>
							<td>
								<span class='pull-right small'>lvl {{ $quest->level }}</span>
								{{ $quest->name }}
							</td>
							<td>{{ $quest->location->name }}@if( ! empty($quest->location->location)), {{ $quest->location->location->name }}@endif</td>
							<td class='text-center'>{{ $quest->pivot->amount ?: 1 }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
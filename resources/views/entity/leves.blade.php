<div class="modal fade entity-modal" id='leves-for-{{ $item->id }}'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><img src="/img/leve_icon.png" width="24" height="24"> {{ $item->name }} Leves</h4>
			</div>
			<div class="modal-body">
				<table class='table table-bordered table-striped'>
					<thead>
						<tr>
							<th class='text-left'>Job, Name, Level</th>
							<th class='text-center'>Rewarded</th>
							<th class='text-center'>Chance</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($leves as $percentage => $amounts)
						@foreach ($amounts as $amount => $levels)
						@foreach ($levels as $level => $datas)
						@foreach ($datas as $data)
						<tr>
							<td>
								<span class='pull-right small'>lvl {{ $level }}</span>
								@if ($data['job_count'] > 1)
									<img src='/img/fight.png' width='24' height='24' rel='tooltip' title='{{ $data['job_category_name'] }}'></i>
								@else
									<img src='/img/jobs/{{ $data['job']->abbr }}.png' width='24' height='24' rel='tooltip' title='Leve for {{ $data['job']->name }}'></i>
								@endif
								{{ $data['name'] }}
							</td>
							<td class='text-center'>{{ $amount }}</td>
							<td class='text-center'>{{ $percentage }}%</td>
						</tr>
						@endforeach
						@endforeach
						@endforeach
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
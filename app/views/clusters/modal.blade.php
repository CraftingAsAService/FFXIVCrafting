<div class="modal fade" id='clusters_for_{{ $item->id }}'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><img src="/img/items/nq/{{ $item->id }}.png" width="24" height="24"> {{ $item->name->term }}, Gathering Locations</h4>
			</div>
			<div class="modal-body">
					@foreach ($clusters as $location => $levels)
					<div>
						{{ $location }}
						<ul class='plain'>
							@foreach ($levels as $level => $icons)
							<li>
								Level {{ $level }}
								<ul class='plain'>
								@foreach ($icons as $icon => $descriptions)
									@foreach ($descriptions as $desc => $count)
									<li>
										<img src='/img/maps/node_icons/{{ $icon }}.png'> {{ $desc }} <span class='label label-default'>{{ $count }} nodes</span>
									</li>
									@endforeach
								@endforeach
								</ul>
							</li>
							@endforeach
						</ul>
					</div>
					@endforeach
			</div>
		</div>
	</div>
</div>
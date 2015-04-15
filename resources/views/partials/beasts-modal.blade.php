<div class="modal fade" id='beasts_for_{{ $item->id }}'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><img src="/img/mob.png" width="24" height="24"> {{ $item->name->term }} Drops</h4>
			</div>
			<div class="modal-body">
				@foreach ($beasts as $location => $mobs)
				<div>
					<p><strong>{{ $location }}</strong></p>
					<ul>
						@foreach ($mobs as $name => $levels)
						<li>
							{{ $name }} 
							@foreach ($levels as $level)
								<label class='label label-primary'>Level {{ implode('-', explode(',', $level)) }}</label>
							@endforeach
						</li>
						@endforeach
					</ul>
				</div>
				@endforeach
				<p class='margin-top'><small>*Mob could be triggered via Quest, Leve or Fate</small></p>
			</div>
		</div>
	</div>
</div>
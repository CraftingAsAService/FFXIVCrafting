<div class="modal fade" id='vendors_for_{{ $item->id }}'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><img src="/img/coin.png" width="24" height="24"> {{ $item->min_price }}, {{ $item->name->term }}</h4>
			</div>
			<div class="modal-body">
				@foreach ($vendors as $location_id => $location)
				<div data-location-id='{{ $location_id }}'>
					<p><strong>{{ $location['name'] }}</strong></p>
					<ul>
						@foreach ($location['npcs'] as $npc)
						<li data-npc-id='{{ $npc['id'] }}'>
							@if($npc['color'])<span class='label label-default color-square' style='background-color: rgb({{ $npc['color'] }});'>&nbsp;</span>@endif
							<em>{{ $npc['name'] }}</em>
							@if(isset($npc['coords']))<span class='label label-default'>{{ $npc['coords']['x'] }}x{{ $npc['coords']['y'] }}</span>@endif
						</li>
						@endforeach
					</ul>
				</div>
				@endforeach
			</div>
		</div>
	</div>
</div>
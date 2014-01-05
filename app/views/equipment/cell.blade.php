<td class='role role-{{ str_replace(' ', '-', $role) }}{{ isset($original_level) && $original_level > $level ? ' hidden' : '' }}' data-level='{{ $level }}'>
	<div class='role-wrap'>
		<div class='items'>
			@foreach($items as $key => $item)
			<div class='item cf {{ $key > 0 ? 'hidden' : 'active' }}{{ $item->crafted_by ? ' craftable' : '' }}' 
				data-item-id='{{ $item->id }}' data-item-ilvl='{{ $item->ilvl }}' data-cannot-equip='{{{ $item->cannot_equip }}}'>

				<div class='icons pull-left text-center'>
					<a href='http://xivdb.com/?item/{{ $item->id }}' target='_blank'>
						<img src='/img/items/{{ $item->icon ?: '../noitemicon.png' }}.png' width='40' height='40' class='main-icon'>
					</a>
					<div>
						@if($item->crafted_by)
						@foreach(explode(',', $item->crafted_by) as $crafted_by)
						<div class='crafted_by'>
							<img src='/img/classes/{{ $crafted_by }}.png' class='stat-crafted_by add-to-list' data-item-id='{{ $item->id }}' data-item-name='{{{ $item->name }}}' rel='tooltip' width='24' height='24' title='Crafted By {{ $job_list[$crafted_by] }}'>
						</div>
						<?php break; ?>
						@endforeach
						@elseif($item->rewarded)
						<div class='rewarded'>
							<img src='/img/reward.png' class='rewarded' width='24' height='24' rel='tooltip' title='Reward from quest, leve, achievement, etc'>
						</div>
						@endif
						@if($item->buy)
						<div class='gil'>
							<img src='/img/coin.png' class='stat-vendors' width='24' height='24' data-toggle='popover' data-placement='bottom' data-content-id='#vendors_for_{{ $item->id }}_{{ $level }}' title='Available for {{ $item->buy }} gil'>
						</div>
						<div class='hidden' id='vendors_for_{{ $item->id }}_{{ $level }}'>
							@foreach($item->vendors as $location_name => $vendors)
							<p>{{ $location_name }}</p>
							<ul>
								@foreach($vendors as $vendor)
								<li>
									<em>{{ $vendor->name }}</em>@if($vendor->title), {{ $vendor->title }}@endif
									@if($vendor->x && $vendor->y)
									<span class='label label-default' rel='tooltip' title='Coordinates' data-container='body'>{{ $vendor->x }}x{{ $vendor->y }}</span>
									@endif
								</li>
								@endforeach
							</ul>
							@endforeach
						</div>
						@endif
					</div>
				</div>
				
				<div class='name-box'>
					<a href='http://xivdb.com/?item/{{ $item->id }}' target='_blank' class='text-primary'>{{ $item->name }}</a>
				</div>

				<div class='stats-box row'>
					@foreach($item->stats as $stat => $amount)
					<div class='col-sm-6 text-center stat{{ ! in_array($stat, $job_focus) ? ' hidden boring' : '' }}' data-stat='{{ $stat }}' data-amount='{{ $amount }}'>
						<img src='/img/stats/{{ $stat }}.png' class='stat-icon' rel='tooltip' title='{{ $stat }}'>
						<span>{{ $amount }}</span>
					</div>
					@endforeach
				</div>
			</div>
			@endforeach
		</div>
		@if(count($items) > 1)
		<div class='td-navigation-buffer cf'></div>
		<div class='td-navigation text-right'>
			More options <span class="current">1</span> / <span class="total">{{ count($items) }}</span>
			<a href='#' class="item-next">Next &raquo;</a>
		</div>
		@endif
	</div>
</td>
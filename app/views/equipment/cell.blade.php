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
						@endforeach
						@endif
						@if($item->buy)
						<div class='gil'>
							<img src='/img/coin.png' class='stat-vendors' rel='tooltip'  width='24' height='24' title='Available from {{ $item->vendor_count }} vendor{{ $item->vendor_count != 1 ? 's' : '' }} for {{ number_format($item->buy) }} gil'>
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
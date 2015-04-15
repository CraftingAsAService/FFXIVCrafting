<td class='role role-{{ str_replace(' ', '-', $role) }}{{ isset($original_level) && ($original_level > $level || ( ! $slim_mode && $original_level + 3 == $level)) ? ' hidden' : '' }}' data-level='{{ $level }}'>
	<div class='role-wrap'>
		<div class='items'>
			<?php $i = 0; ?>
			@foreach($items as $ilvl => $gear)
			@foreach($gear as $item)
			
			<div class='item nq cf {{ $i++ > 0 ? 'hidden' : 'active' }}{{ count($item->recipe) ? ' craftable' : '' }}' 
				data-item-id='{{ $item->id }}' data-item-ilvl='{{ $item->level }}' data-cannot-equip='{{{ $item->cannot_equip }}}'>

				<div class='icons pull-left text-center'>
					<a href='http://xivdb.com/?item/{{ $item->id }}' target='_blank'>
						<img src='{{ assetcdn('items/nq/' . $item->id . '.png') }}' width='40' height='40' class='main-icon nq'>
						@if ($item->can_hq)
						<img src='{{ assetcdn('items/hq/' . $item->id . '.png') }}' width='40' height='40' class='main-icon hq hidden'>
						@endif
					</a>
					<div>
						@if(count($item->recipe))
						<div class='crafted_by'>
							<i class='class-icon class-id-{{ $item->recipe[0]->classjob_id }} stat-crafted_by add-to-list' data-item-id='{{ $item->id }}' data-item-name='{{{ $item->name->term }}}' rel='tooltip' title='Crafted By {{ $item->recipe[0]->classjob->name->term }}, Click to Add to List'></i>
						</div>
						@elseif($item->rewarded || $item->achievable)
						<div class='rewarded'>
							<img src='/img/reward.png' class='rewarded' width='20' height='20' rel='tooltip' title='Reward from {{ $item->achievable ? 'an Achievement' : 'a Quest' }}'>
						</div>
						@endif
						@if(count($item->vendors))
						<div class='gil'>
							<img src='/img/coin.png' class='vendors' width='24' height='24' rel='tooltip' title='Available for {{ $item->min_price }} gil, Click to load Vendors'>
						</div>
						@endif
					</div>
				</div>
				
				<div class='name-box'>
					<a href='http://xivdb.com/?item/{{ $item->id }}' target='_blank' class='text-primary'>{{ $item->name->term }}</a>
				</div>

				<div class='stats-box row'>
					@foreach($item->baseparam as $param)
					<div class='col-sm-6 text-center nq stat{{ ! in_array($param->id, $stat_ids_to_focus) ? ' hidden boring' : '' }}' data-stat='{{ $param->name->term }}' data-amount='{{ (int) $param->pivot->nq_amount }}'>
						<img src='/img/stats/nq/{{ $param->name->term }}.png' class='stat-icon' rel='tooltip' title='{{ $param->name->term }}'>
						<span>{{ (int) $param->pivot->nq_amount }}</span>
					</div>
					@if($param->pivot->hq_amount)
					<div class='col-sm-6 text-center hq hidden stat{{ ! in_array($param->id, $stat_ids_to_focus) ? ' hidden boring' : '' }}' data-stat='{{ $param->name->term }}' data-amount='{{ (int) $param->pivot->hq_amount }}'>
						<img src='/img/stats/hq/{{ $param->name->term }}.png' class='stat-icon' rel='tooltip' title='{{ $param->name->term }}'>
						<span>{{ (int) $param->pivot->hq_amount }}</span>
					</div>
					@endif
					@endforeach
				</div>
			</div>
			@endforeach
			@endforeach
		</div>
		@if($i > 1)
		<div class='td-navigation-buffer cf'></div>
		<div class='td-navigation text-right'>
			More options <span class="current">1</span> / <span class="total">{{ $i }}</span>
			<a href='#' class="item-next">Next &raquo;</a>
		</div>
		@endif
	</div>
</td>
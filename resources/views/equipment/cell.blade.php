<td class='role role-{{ str_replace(' ', '-', $role) }}{{ isset($original_level) && ($original_level > $level || ( ! $slim_mode && $original_level + 3 == $level)) ? ' hidden' : '' }}' data-level='{{ $level }}'>
	<div class='role-wrap'>
		<div class='items'>
			<?php $i = 0; ?>
			@foreach($items as $ilvl => $gear)
			@foreach($gear as $item)

			<div class='item nq cf {{ $i++ > 0 ? 'hidden' : 'active' }}{{ count($item->recipes) ? ' craftable' : '' }}'
				data-item-id='{{ $item->id }}' data-item-ilvl='{{ $item->ilvl }}' data-cannot-equip='{{{ $item->cannot_equip }}}' data-score='{{ $item->score }}'>

				<div class='icons pull-left text-center'>
					<a href='{{ xivdb_item_link() . $item->id }}' data-replacename="0" data-colorname="0" data-showicon="0" target='_blank'>
						<img src='{{ assetcdn('item/' . $item->icon . '.png') }}' width='40' height='40' class='main-icon nq'>
						@if ($item->can_hq)
						<img src='{{ assetcdn('item/' . $item->icon . '.png') }}' width='40' height='40' class='main-icon hq hidden'>
						@endif
					</a>
					<div>
						@if(count($item->recipes))
						<div class='crafted_by'>
							<img src='/img/jobs/{{ strtoupper($item->recipes[0]->job->abbr) }}.png' width='20' height='20' class='stat-crafted_by add-to-list' data-item-id='{{ $item->id }}' data-item-name='{{{ $item->display_name }}}' rel='tooltip' title='Crafted By {{ $item->recipes[0]->job->name }}, Click to Add to List' style='margin-top: 0; position: relative; top: -3px;'>
						</div>
						@elseif(count($item->instances) || count($item->achievement) || count($item->mobs) || count($item->ventures))
						<div class='rewarded'>
							<img src='/img/reward.png' class='rewarded' width='20' height='20' rel='tooltip' title='Reward from {{ $item->achievable ? 'an Achievement' : 'a Quest' }}'>
						</div>
						@endif
						@if(count($item->shops))
						<div class='gil'>
							<img src='/img/shop.png' class='click-to-view' data-type='shops' width='24' height='24' rel='tooltip' title='Available for {{ $item->price }} gil, Click to load Shop'>
						</div>
						@endif
					</div>
				</div>

				<div class='name-box'>
					<a href='{{ xivdb_item_link() . $item->id }}' target='_blank' class='text-primary' data-showicon="0">{{ $item->display_name }}</a>
				</div>

				<div class='stats-box row'>
					@foreach($item->attributes as $attribute)
					@if($attribute->quality == 'nq')
					<div class='col-sm-6 text-center nq stat{{ ! in_array($attribute->attribute, $stat_ids_to_focus) ? ' hidden boring' : '' }}' data-stat='{{ $attribute->attribute }}' data-amount='{{ (int) $attribute->amount }}' data-stat-image='{{ stat_name($attribute->attribute) }}'>
						<img src='/img/stats/{{ stat_name($attribute->attribute) }}.png' class='stat-icon' rel='tooltip' title='{{ $attribute->attribute }}'>
						<span>{{ (int) $attribute->amount }}</span>
					</div>
					@endif
					@if($attribute->quality == 'hq')
					<div class='col-sm-6 text-center hq hidden stat{{ ! in_array($attribute->attribute, $stat_ids_to_focus) ? ' hidden boring' : '' }}' data-stat='{{ $attribute->attribute }}' data-amount='{{ (int) $attribute->amount }}' data-stat-image='{{ stat_name($attribute->attribute) }}'>
						<img src='/img/stats/{{ stat_name($attribute->attribute) }}.png' class='stat-icon' rel='tooltip' title='{{ $attribute->attribute }}'>
						<span>{{ (int) $attribute->amount }}</span>
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
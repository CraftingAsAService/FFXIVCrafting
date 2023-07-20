@extends('app')

@section('javascript')
	<script type='text/javascript' src='https://code.highcharts.com/highcharts.js'></script>
	<script type='text/javascript' src='{{ cdn('/js/food.js') }}'></script>
@endsection

@section('banner')
	<h1>Food</h1>
	<!--
	<p>
		Food provides a certain percentage of your stat up to the maximum.  The Threshold is what it takes to reach that percentage and maximum.
		For example, if you had 65 Craftsmanship, it would be a waste to use <em>Mashed Popotoes</em> when <em>Mint Lassi</em> is available (and assumedly cheaper).
	</p>
	<p>All battle food contains vitality.</p>
	<p>All food lasts 30 minutes and provides a 3% XP Bonus.</p>
	<p>Click a cell to view the food within.</p>
	-->
@endsection

@section('content')

	<ul class='nav nav-tabs'>
		@foreach($sections as $section_name => $section)
		<li class='{{ $section_name == array_keys($sections)[0] ? 'active' : '' }}'>
			<a href='#{{ strtolower($section_name) }}' data-toggle='tab'>{{ $section_name }}</a>
		</li>
		@endforeach
	</ul>

	<div class='tab-content'>
		@foreach($sections as $section_name => $section)
		<div class='tab-pane food-selection{{ $section_name == array_keys($sections)[0] ? ' active' : '' }}' id='{{ strtolower($section_name) }}'>

			<div class='row'>
				<div class='col-sm-6 food-grid'>
					<div class='table-responsive'>
						<table class='table table-striped table-bordered'>
							<thead>
								<tr>
									<th></th>
									@foreach($section['headers'] as $header)
									<th>
										<img src='/img/stats/{{ $header }}.png' class='stat-icon' rel='tooltip' title='{{ $header }}'>
									</th>
									@endforeach
								</tr>
							</thead>
							<tbody>
								@if($section_name == 'Resistances')
								<tr>
									<td class='row-header'>
										<span class='glyphicon glyphicon-cutlery'></span>
									</td>
									@foreach($section['headers'] as $j)
									<td class='{{ isset($section['intersections'][$j][$j]) && $section['intersections'][$j][$j] == 0 ? 'opaque' : 'reveal' }}' data-a='{{ $j }}' data-b='{{ $j }}' rel='tooltip' title='{{ $j }} Foods'>
										{{ $section['intersections'][$j][$j] ?? 0 }}
									</td>
									@endforeach
								</tr>
								@else
								@foreach($section['headers'] as $i)
								<tr>
									<td class='row-header'>
										<img src='/img/stats/{{ $i }}.png' class='stat-icon' rel='tooltip' title='{{ $i }}'>
									</td>
									@foreach($section['headers'] as $j)

									<td class='{{ isset($section['intersections'][$i][$j]) && $section['intersections'][$i][$j] == 0 ? 'opaque' : 'reveal' }}' data-a='{{ $i }}' data-b='{{ $j }}' rel='tooltip' title='{{ $i }}@if($i != $j) &amp; {{ $j }}@endif Foods'>
										{{ $section['intersections'][$i][$j] ?? 0 }}
									</td>
									@endforeach
								</tr>
								@endforeach
								@endif
							</tbody>
						</table>
					</div>
					<div class='highchart hidden'></div>
					<ul class='list-group margin-top'>
						<li class='list-group-item list-group-item-info'>
							Legend
						</li>
						@foreach($section['headers'] as $header)
						<li class='list-group-item'>
							<img src='/img/stats/{{ $header }}.png' class='stat-icon' rel='tooltip' title='{{ $header }}'>
							{{ $header }}
						</li>
						@endforeach
					</ul>
				</div>
				<div class='col-sm-6'>
					<div class='table-responsive'>
						<table class='table table-bordered table-striped items-table hidden'>
							<caption><h2></h2></caption>
							<thead>
								<tr>
									<th> </th>
									{{-- The first item has already been sorted ascendingly, so clicking again will descend it --}}
									<th class='text-center sort cursor' data-order='asc' data-column='2' rel='tooltip' title='Sort Column'><span class='glyphicon glyphicon-sort-by-attributes'></span></th>
									{{-- The opposite is true with these.  They're unordered, so the next click will switch it to ascending. --}}
									<th class='text-center sort cursor' data-order='desc' data-column='3' rel='tooltip' title='Sort Column'><span class='glyphicon glyphicon-sort'></span></th>
									<th class='text-center sort cursor' data-order='desc' data-column='4' rel='tooltip' title='Sort Column'><span class='glyphicon glyphicon-sort'></span></th>
									<th class='text-center valign' rel='tooltip' title='Add to Crafting List'>
										<i class='glyphicon glyphicon-shopping-cart'></i>
										<i class='glyphicon glyphicon-plus'></i>
									</th>
								</tr>
							</thead>
							<tbody>
								@foreach($section['data'] as $stat_names => $group)
									@foreach($group as $item)
										@foreach(['nq', 'hq'] as $quality)
											<?php if ($quality == 'hq' && ! $item['has_hq']) continue; ?>
											<tr class='hidden' data-stats='{{ $stat_names }}' data-quality='{{ $quality }}' data-item-id='{{ $item['id'] }}'>
												<td>
													@if($item['shops_count'] && $quality == 'nq')
														<a href='#' class='click-to-view pull-right' data-type='shops' rel='tooltip' title='Available for {{ $item['price'] }} gil, Click to load Shops'>
															<img src='/img/coin.png' width='24' height='24'>
														</a>
													@endif

													<a href='{{ item_link() . $item['id'] }}' target='_blank'>
													<span class='overlay-container'>
													@if($quality == 'hq')
														<img src='/img/hq-overlay.png' width='36' height='36' class='hq-overlay' style='top: inherit;'>
													@endif
													<img src='{{ icon($item['icon']) }}' width='36' height='36'>
													</span>
														{{ $item['name'] }}
													</a>
												</td>
												@foreach(explode('|', $stat_names) as $stat_name)
													@if(isset($item['stats'][$stat_name][$quality]))
														<td
															class='text-center valign'
															data-amount='{{ number_format($item['stats'][$stat_name][$quality]['limit']) }}' data-stat-name='{{ $stat_name }}'@if ($item['stats'][$stat_name][$quality]['amount'] != 0) rel='tooltip' title='Maximum output of {{ number_format($item['stats'][$stat_name][$quality]['amount']) }}% with {{ number_format($item['stats'][$stat_name][$quality]['threshold']) }} {{ $stat_name }}'@endif
														>
															+{{ number_format($item['stats'][$stat_name][$quality]['limit']) }}
															<img src='/img/stats/{{ $stat_name }}.png' class='stat-icon'>
														</td>
													@else
														<td class='text-center valign' data-stat-name='{{ $stat_name }}' rel='tooltip'>
															+0
															<img src='/img/stats/{{ $stat_name }}.png' class='stat-icon'>
														</td>
													@endif
												@endforeach
												<td class='cart text-center valign'>
													<button class='btn btn-default add-to-list' data-item-id='{{ $item['id'] }}' data-item-name='{{ $item['name'] }}'>
														<i class='glyphicon glyphicon-shopping-cart'></i>
														<i class='glyphicon glyphicon-plus'></i>
													</button>
												</td>
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
		@endforeach
	</div>

@endsection
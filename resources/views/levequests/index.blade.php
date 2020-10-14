@extends('app')

@section('javascript')
	<script src='{{ cdn('/js/levequests.js') }}'></script>
@stop

@section('banner')
	<a href='/levequests/advanced' class='btn btn-primary pull-right'>Advanced Tool <i class='glyphicon glyphicon-arrow-right'></i></a>
	<h1>Levequests</h1>
	<h2>Get the most out of your allowances</h2>
@stop

@section('content')

<div class='row'>
	<div class='col-sm-3 col-lg-2'>
		<fieldset>
			<legend>Leve Level</legend>
			<div class='list-group leve-level-select'>
				@foreach(array_merge(array(1), range(5, 45, 5), range(50, config('site.max_level') - 2, 2)) as $level)
				<a href='#' class='list-group-item{{ $level == 1 ? ' active' : '' }}' data-level='{{ $level }}'>
					Level {{ $level }}
				</a>
				@endforeach
			</div>
		</fieldset>
	</div>
	<div class='col-sm-9 col-lg-10'>
		<fieldset class='margin-bottom'>
			<legend>Class</legend>
			<div class='btn-group jobs-list'>
				@foreach($crafting_job_list as $job)
				<label class='btn btn-{{ $job->abbr == 'FSH' ? 'info' : 'primary' }} class-selector{{ $job->id == reset($crafting_job_ids) ? ' active' : '' }}' data-level='0' data-class='{{ $job->abbr }}'>
					<img src='/img/jobs/{{ strtoupper($job->abbr) }}-inactive.png' width='24' height='24' rel='tooltip' title='{{ $job->name }}'>
				</label>
				@endforeach
			</div>
		</fieldset>
		<fieldset style='margin-top: 30px;'>

			@foreach($crafting_job_list as $job)
			<div class='leve-section' id='{{ $job->abbr }}-leves'>

				@foreach(array_merge(array(1), range(5,45, 5), range(50, config('site.max_level') - 2, 2)) as $level)
				@php
					$veryFirstLoop = $loop->parent->first && $loop->first;
				@endphp
				<div id='{{ $job->abbr }}-{{ $level }}-leves' class='{!! $veryFirstLoop ? '' : 'hidden' !!}'>
					<div class='table-responsive'>
						<legend>Level {{ $level }} {{ $job->name }} Levequests</legend>
						<table class='levequests-table table table-bordered table-striped table-condensed'>
							<thead>
								<tr>
									<th colspan='2'>Details</th>
									<th>Rewards</th>
									<th class='text-center'>
										Craft or Buy
									</th>
								</tr>
							</thead>
							<tbody>
								@if (isset($leves[$job->abbr][$level]))
								@foreach($leves[$job->abbr][$level] as $leve)
								<?php $item = $leve->requirements[0]; ?>
								<tr data-item-id='{{ $item->id }}'>
									<td width='57' class='valign text-center'>
										<div style='position: relative; overflow: hidden; width: 47px; opacity: 1;'>
											<img {!! $veryFirstLoop ? '' : 'src="" data-' !!}src='{{ icon($leve->frame) }}' width='47' height='75' style='position: absolute;'>
											<img {!! $veryFirstLoop ? '' : 'src="" data-' !!}src='{{ icon($leve->plate) }}' width='47' height='75'>
										</div>
									</td>
									<td class='valign details'>

										<h4>
											<a href='/levequests/breakdown/{{ $leve->id }}'rel='tooltip' title='More Leve Information'>{{ $leve->name }}</a>
										</h4>

										<p>
											<a href='{{ item_link() . $item->id }}' class='item-name' target='_blank'><img {!! $veryFirstLoop ? '' : 'src="" data-' !!}src='{{ icon($item->icon) }}' width='24' height='24' style='margin-right: 10px;'>{{ $item->display_name }}</a>

											@if ($item->pivot->amount > 1)
											<span class='label label-primary' rel='tooltip' title='Amount Required'>
												x {{ $item->pivot->amount }}
											</span>
											@endif

											@if($leve->repeats)
											<i class='glyphicon glyphicon-fire text-danger margin-left' rel='tooltip' title='Repeatable Turnin!'></i>
											@endif
										</p>

										{{-- <p>
											<img src='/img/locations/{{ preg_replace('/\W/', '', strtolower($leve->major_location)) }}_banner.png' width='24' height='24'>
											<span class='label label-info'>{{ $leve->type }}</span>
											{{ ! empty($leve->location) ? $leve->location : '' }}{{ ! empty($leve->location) && ! empty($leve->minor_location) ? ',' : '' }}
											{{ ! empty($leve->minor_location) ? $leve->minor_location : '' }}
										</p> --}}
									</td>
									<td class='text-center rewards valign'>
										<div class='text-left inline'>
											<p class='xp-reward'>
												<img src='/img/xp.png' width='24' height='24'>
												{{ number_format($leve->xp) }}
											</p>

											<p class='gil-reward'>
												<img src='/img/coin.png' width='24' height='24'>
												{{ number_format($leve->gil) }}
											</p>
										</div>
									</td>
									<td class='text-center valign'>
										<button class='btn btn-default add-to-list' data-item-id='{{ $item->id }}' data-item-name='{{ $item->display_name }}' data-item-quantity='{{ $leve->amount }}'>
											<i class='glyphicon glyphicon-shopping-cart'></i>
											<i class='glyphicon glyphicon-plus'></i>
										</button>
										@if($item->shops->count())
										<p class='margin-top'>
											<a href='#' class='btn btn-default click-to-view' data-type='shops' rel='tooltip' title='Available for {{ $item->price }} gil, Click to load Vendors'>
												<img src='/img/coin.png' width='20' height='20'>
												{{ number_format($item->price) }}
											</a>
										</p>
										@endif
									</td>
								</tr>
								@endforeach
								@endif
							</tbody>
						</table>
					</div>

					<legend>Level {{ $level }} {{ $job->name }} Rewards</legend>

					@if(isset($rewards[$job->id][$level]))
					<div class='row'>
						@foreach($rewards[$job->id][$level] as $item_id => $reward)
						<div class='col-sm-6 col-md-4 margin-bottom'>
							<a href='{{ item_link() . $item_id }}' class='item-name' target='_blank'><img {!! $veryFirstLoop && $loop->first ? '' : 'src="" data-' !!}src='{{ icon($reward['item']->icon) }}' width='24' height='24' style='margin-right: 10px;'>{{ $reward['item']->display_name }}</a>

							@foreach ($reward['amounts'] as $amount)
							<span class='label label-primary'>{{ $amount }}</span>
							@endforeach
						</div>
						@endforeach
					</div>
					@endif
				</div>
				@endforeach
			</div>
			@endforeach

		</fieldset>
	</div>
</div>

@stop
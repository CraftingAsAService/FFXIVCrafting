@extends('app')

@section('javascript')
	<script src='{{ cdn('/js/levequests.js') }}'></script>
@stop

@section('banner')
	<h1>Levequests</h1>
	<h2>Get the most out of your allowances</h2>
@stop

@section('content')

<div class='row'>
	<div class='col-sm-3 col-lg-2'>
		<fieldset>
			<legend>Leve Level</legend>
			<div class='list-group leve-level-select'>
				@foreach(array_merge(array(1), range(5, 45, 5), range(50, 58, 2)) as $level)
				<a href='#' class='list-group-item{{ $level == 1 ? ' active' : '' }}' data-level='{{ $level }}'>
					Level {{ $level }}
				</a>
				@endforeach
			</div>
			{{-- <a href='/levequests/advanced'>Advanced Version</a> --}}
		</fieldset>
	</div>
	<div class='col-sm-9 col-lg-10'>
		<fieldset class='margin-bottom'>
			<legend>Class</legend>
			<div class='btn-group jobs-list'>
				@foreach($crafting_job_list as $job)
				<label class='btn btn-{{ $job->abbr == 'FSH' ? 'info' : 'primary' }} class-selector{{ $job->id == reset($crafting_job_ids) ? ' active' : '' }}' data-level='{{ $account ? $account['levels'][strtolower($job->name)] : 0 }}' data-class='{{ $job->abbr }}'>
					<img src='/img/classes/{{ $job->abbr }}.png' rel='tooltip' title='{{ $job->name }}'>
				</label>
				@endforeach
			</div>
		</fieldset>
		<fieldset style='margin-top: 30px;'>

			@foreach($crafting_job_list as $job)
			<div class='leve-section' id='{{ $job->abbr }}-leves'>

				@foreach(array_merge(array(1), range(5,45, 5), range(50, 58, 2)) as $level)
				<div class='table-responsive hidden' id='{{ $job->abbr }}-{{ $level }}-leves'>
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
							@foreach($leves[$job->abbr][$level] as $leve)
							<?php $item = $leve->requirements[0]; ?>
							<tr data-item-id='{{ $item->id }}'>
								<td width='57' class='valign text-center'>
									<div style='position: relative; overflow: hidden; width: 47px; opacity: 1;'>
										<img src='' data-src='{{ assetcdn('leve/frame/' . $leve->frame . '.png') }}' width='47' height='75' style='position: absolute;'>
										<img src='' data-src='{{ assetcdn('leve/plate/' . $leve->plate . '.png') }}' width='47' height='75'>
									</div>
								</td>
								<td class='valign details'>
									
									<h4>
										<a href='/levequests/breakdown/{{ $leve->id }}'rel='tooltip' title='More Leve Information'>{{ $leve->name }}</a>
									</h4>

									<p>
										<a href='http://xivdb.com/?item/{{ $item->id }}' class='item-name' target='_blank'><img src='' data-src='{{ assetcdn('item/' . $item->icon . '.png') }}' width='24' height='24' style='margin-right: 10px;'>{{ $item->name }}</a>

										@if ($item->pivot->amount > 1)
										<span class='label label-primary' rel='tooltip' title='Amount Required' data-container='body'>
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
									<button class='btn btn-default add-to-list' data-item-id='{{ $item->id }}' data-item-name='{{ $item->name }}' data-item-quantity='{{ $leve->amount }}'>
										<i class='glyphicon glyphicon-shopping-cart'></i>
										<i class='glyphicon glyphicon-plus'></i>
									</button>
									@if(count($item->shops))
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
						</tbody>
					</table>

					<legend>Level {{ $level }} {{ $job->name }} Rewards</legend>

					@if(isset($rewards[$job->id][$level]))
					<div class='row'>
						@foreach($rewards[$job->id][$level] as $item_id => $reward)
						<div class='col-sm-6 col-md-4 margin-bottom'>
							<a href='http://xivdb.com/?item/{{ $item_id }}' class='item-name' target='_blank'><img src='' data-src='{{ assetcdn('item/' . $item_id . '.png') }}' width='24' height='24' style='margin-right: 10px;'>{{ $reward['item']->name }}</a>

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
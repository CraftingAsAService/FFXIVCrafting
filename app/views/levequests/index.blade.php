@extends('wrapper.layout')

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
				@foreach(array_merge(array(1), range(5,45, 5)) as $level)
				<a href='#' class='list-group-item{{ $level == 1 ? ' active' : '' }}' data-level='{{ $level }}'>
					Level {{ $level }}
				</a>
				@endforeach
			</div>
			<a href='/leve'>Advanced Version</a>
		</fieldset>
	</div>
	<div class='col-sm-9 col-lg-10'>
		<fieldset class='margin-bottom'>
			<legend>Class</legend>
			<div class='btn-group jobs-list'>
				@foreach($crafting_job_list as $job)
				<label class='btn btn-{{ $job->en_abbr->term == 'FSH' ? 'info' : 'primary' }} class-selector{{ $job->id == reset($crafting_job_ids) ? ' active' : '' }}' data-level='{{ $account ? $account['levels'][strtolower($job->en_name->term)] : 0 }}' data-class='{{{ $job->en_abbr->term }}}'>
					<img src='/img/classes/{{ $job->en_abbr->term }}.png' rel='tooltip' title='{{{ $job->name->term }}}'>
				</label>
				@endforeach
			</div>
		</fieldset>
		<fieldset style='margin-top: 30px;'>

			@foreach($crafting_job_list as $job)
			<div class='leve-section' id='{{ $job->en_abbr->term }}-leves'>

				@foreach(array_merge(array(1), range(5,45, 5)) as $level)
				<div class='table-responsive hidden' id='{{ $job->en_abbr->term }}-{{ $level }}-leves'>
					<legend>Level {{ $level }} {{{ $job->name->term }}} Levequests</legend>
					<table class='levequests-table table table-bordered table-striped table-condensed'>
						<thead>
							<tr>
								<th>Details</th>
								<th>Rewards</th>
								<th class='text-center'>
									Craft or Buy
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($leves[$job->en_abbr->term][$level] as $leve)
							<?php $recipe_id = count($leve->item->recipe) ? $leve->item->recipe[0]->id : 0; ?>
							<tr>
								
								<td class='valign details'>
									
									<h4>
										<a href='/levequests/breakdown/{{ $leve->id }}'rel='tooltip' title='More Leve Information'>{{ $leve->name }}</a>
									</h4>

									<p>
										<?php $url = $recipe_id == 0 ? 'item/' . $leve->item->id : 'recipe/' . $recipe_id; ?>
										<a href='http://xivdb.com/?{{ $url }}' class='item-name' target='_blank'><img src='' data-src='{{ assetcdn('items/nq/' . $leve->item->id . '.png') }}' width='24' height='24' style='margin-right: 10px;'>{{ $leve->item->name->term }}</a>

										@if ($leve->amount > 1)
										<span class='label label-primary' rel='tooltip' title='Amount Required' data-container='body'>
											x {{ $leve->amount }}
										</span>
										@endif

										@if($leve->triple)
										<i class='glyphicon glyphicon-fire text-danger margin-left' rel='tooltip' title='Triple Turnin!'></i>
										@endif
									</p>

									<p>
										<img src='/img/locations/{{ preg_replace('/\W/', '', strtolower($leve->major_location)) }}_banner.png' width='24' height='24'>
										<span class='label label-info'>{{ $leve->type }}</span>
										{{ ! empty($leve->location) ? $leve->location : '' }}{{ ! empty($leve->location) && ! empty($leve->minor_location) ? ',' : '' }}
										{{ ! empty($leve->minor_location) ? $leve->minor_location : '' }}
									</p>
								</td>
								<td class='text-center rewards valign'>
									<div class='text-left inline'>
										<p class='xp-reward'>
											<img src='/img/xp.png' width='24' height='24'>
											<span class='opaque'>{{ $leve->xp_spread > 0 ? '~' : '' }}</span>{{ number_format($leve->xp) }}
										</p>

										<p class='gil-reward'>
											<img src='/img/coin.png' width='24' height='24'>
											<span class='opaque'>{{ $leve->gil_spread > 0 ? '~' : '' }}</span>{{ number_format($leve->gil) }}
										</p>
									</div>
								</td>
								<td class='text-center valign'>
									<button class='btn btn-default add-to-list' data-item-id='{{ $leve->item->id }}' data-item-name='{{{ $leve->item->name->term }}}' data-item-quantity='{{{ $leve->amount }}}'>
										<i class='glyphicon glyphicon-shopping-cart'></i>
										<i class='glyphicon glyphicon-plus'></i>
									</button>
									@if(count($leve->item->vendors))
									<p class='margin-top'>
										<a href='#' class='btn btn-default vendors' data-item-id='{{ $leve->item->id }}' rel='tooltip' title='Available for {{ $leve->item->min_price }} gil, Click to load Vendors'>
											<img src='/img/coin.png' width='20' height='20'>
											{{ number_format($leve->item->min_price) }}
										</a>
									</p>
									@endif
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>

					<legend>Level {{ $level }} {{{ $job->name->term }}} Rewards</legend>

					@if(isset($rewards[$job->id][$level]))
					<div class='row'>
						@foreach($rewards[$job->id][$level] as $item_id => $reward)
						<div class='col-sm-6 col-md-4 margin-bottom'>
							<a href='http://xivdb.com/?item/{{ $item_id }}' class='item-name' target='_blank'><img src='' data-src='{{ assetcdn('items/nq/' . $item_id . '.png') }}' width='24' height='24' style='margin-right: 10px;'>{{ $reward['item']->name->term }}</a>

							@if (count($reward['amounts']) > 1 || $reward['amounts'][0] > 1)
							<span class='label label-primary' rel='tooltip' title='Amount Rewarded' data-container='body'>
								x {{ preg_replace('/, (\d+)$/', ' or $1', implode(', ', $reward['amounts'])) }}
							</span>
							@endif
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
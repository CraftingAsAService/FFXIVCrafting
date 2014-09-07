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
			<legend>Recipe Level</legend>
			<div class='list-group leve-level-select'>
				@foreach(array_merge(array(1), range(5,45, 5)) as $level)
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
								<th colspan='2'>Details</th>
								<th>Location</th>
								<th>Vendors</th>
								<th>Rewards</th>
								<th class='text-center'>
									<i class='glyphicon glyphicon-shopping-cart'></i>
									<i class='glyphicon glyphicon-plus'></i>
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($leves[$job->en_abbr->term][$level] as $leve)
							<?php $recipe_id = count($leve->item->recipe) ? $leve->item->recipe[0]->id : 0; ?>
							<tr>
								@if($leve->triple)
								<td class='valign' width='23' style='padding: 0 4px;'>
									<i class='glyphicon glyphicon-fire text-danger' rel='tooltip' title='Triple Turnin!'></i>
								</td>
								@endif
								<td class='valign details' colspan='{{ $leve->triple ? 1 : 2 }}'>
									
									<h4>
										<a href='/leve/breakdown/{{ $leve->id }}'rel='tooltip' title='More Leve Information'>{{ $leve->name }}</a>
									</h4>

									<p>
										<?php $url = $recipe_id == 0 ? 'item/' . $leve->item->id : 'recipe/' . $recipe_id; ?>
										<a href='http://xivdb.com/?{{ $url }}' class='item-name' target='_blank'>
											<img src='' data-src='{{ assetcdn('items/nq/' . $leve->item->id . '.png') }}' width='20' height='20' style='margin-right: 10px;'>{{ $leve->item->name->term }}
										</a>

										@if ($leve->amount > 1)
										<span class='label label-primary' rel='tooltip' title='Amount Required' data-container='body'>
											x {{ $leve->amount }}
										</span>
										@endif
									</p>
								</td>
								<td class='valign text-center location {{ preg_replace('/\W/', '', strtolower($leve->major_location)) }}'>
									<div>
										<span class='label label-info'>{{ $leve->type }}</span>
									</div>
									<div>
										{{ ! empty($leve->location) ? $leve->location : '' }}
									</div>
									<div>
										{{ ! empty($leve->minor_location) ? $leve->minor_location : '' }}
									</div>
								</td>
								<td class='valign text-center'>
									@if(count($leve->item->vendors))
									<a href='#' class='btn btn-default vendors' rel='tooltip' title='Available for {{ $leve->item->min_price }} gil, Click to load Vendors'>
										<img src='/img/coin.png' width='20' height='20'>
										{{ number_format($leve->item->min_price) }}
									</a>
									@endif
								</td>
								<td class='text-center rewards valign'>
									<span class='xp-reward margin-right' rel='tooltip' title='XP Reward'>
										<img src='/img/xp.png' width='18' height='18'>
										{{ number_format($leve->xp) }}
									</span>

									<span class='gil-reward margin-right' rel='tooltip' title='Gil Reward'>
										<img src='/img/coin.png' width='18' height='18'>
										{{ number_format($leve->gil) }}
									</span>

									<button class='btn btn-default leve_rewards' data-class='{{ $leve->classjob_id }}' data-level='{{ $level }}' data-toggle='popover' data-trigger='focus' data-content-id='#rewards_for_{{ $leve->id }}' rel='tooltip' title='Potential Rewards'>
										<i class='glyphicon glyphicon-gift'></i>
									</button>
									<div class='hidden' id='rewards_for_{{ $leve->id }}'>
										X
									</div>
								</td>
								<td class='text-center valign'>
									<button class='btn btn-default add-to-list' data-item-id='{{ $leve->item->id }}' data-item-name='{{{ $leve->item->name->term }}}' data-item-quantity='{{{ $leve->amount }}}'>
										<i class='glyphicon glyphicon-shopping-cart'></i>
										<i class='glyphicon glyphicon-plus'></i>
									</button>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				@endforeach
			</div>
			@endforeach

		</fieldset>
	</div>
</div>

@stop
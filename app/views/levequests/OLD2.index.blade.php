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
			<div class='leve-section hidden' id='{{ $job->en_abbr->term }}-leves'>
				<legend>{{{ $job->name->term }}} Levequests</legend>

				@foreach(array_merge(array(1), range(5,45, 5)) as $level)
				<div class='row hidden' id='{{ $job->en_abbr->term }}-{{ $level }}-leves'>
					@foreach($leves[$job->en_abbr->term][$level] as $leve)
					<?php $recipe_id = count($leve->item->recipe) ? $leve->item->recipe[0]->id : 0; ?>
					<div class='col-sm-6 leve margin-bottom'>
						<div class='panel panel-info'>
							<div class='panel-heading'>
								<img src='/img/locations/{{ preg_replace('/\W/', '', strtolower($leve->major_location)) }}_banner.png' width='24' height='24' class='primary-location pull-right' rel='tooltip' title='{{{ $leve->major_location }}}'>

								@if($leve->triple)
								<i class='glyphicon glyphicon-fire text-danger pull-left triple-turnin' rel='tooltip' title='Triple Turnin!'></i>
								@endif


								<h4 class='text-center'>
									{{ $leve->name }}
								</h4>
							</div>
							<div class='panel-body'>
								<div class='item'>

									<button class='btn btn-default add-to-list pull-right' data-item-id='{{ $leve->item->id }}' data-item-name='{{{ $leve->item->name->term }}}' data-item-quantity='{{{ $leve->amount }}}' rel='tooltip' title='Add to Crafting List'>
										<i class='glyphicon glyphicon-shopping-cart'></i>
										<i class='glyphicon glyphicon-plus'></i>
									</button>
									
									<?php $url = $recipe_id == 0 ? 'item/' . $leve->item->id : 'recipe/' . $recipe_id; ?>
									<a href='http://xivdb.com/?{{ $url }}' class='item-name' target='_blank'>
										<img src='' data-src='{{ assetcdn('items/nq/' . $leve->item->id . '.png') }}' width='36' height='36' style='margin-right: 10px;'>{{ $leve->item->name->term }}
									</a>
									@if ($leve->amount > 1)
									<span class='label label-primary' rel='tooltip' title='Amount Required' data-container='body'>
										x {{ $leve->amount }}
									</span>
									@endif
								</div>

								<div class='rewards'>
									@if(count($leve->item->vendors))
									<a href='#' class='btn btn-default vendors pull-right' rel='tooltip' title='Available for {{ $leve->item->min_price }} gil, Click to load Vendors'>
										<img src='/img/coin.png' width='20' height='20'>
										{{ number_format($leve->item->min_price) }}
									</a>
									@endif

									<span class='xp-reward margin-right' rel='tooltip' title='XP Reward'>
										<img src='/img/xp.png' width='24' height='24'>
										{{ number_format($leve->xp) }}
									</span>

									<span class='gil-reward margin-right' rel='tooltip' title='Gil Reward'>
										<img src='/img/coin.png' width='24' height='24'>
										{{ number_format($leve->gil) }}
									</span>

									<button class='btn btn-default item-reward' data-class='{{ $leve->classjob_id }}' data-level='{{ $level }}' data-toggle='popover' data-trigger='focus' data-content-id='#rewards_for_{{ $leve->id }}' rel='tooltip' title='Potential Rewards'>
										<i class='glyphicon glyphicon-gift'></i>
									</button>
									<div class='hidden' id='rewards_for_{{ $leve->id }}'></div>
								</div>

								<div class='location'>
									<span class='label label-default margin-right'>
										<i class='glyphicon glyphicon-{{ $type_to_icon[$leve->type] }} leve-type'></i>
										{{ $leve->type }}
									</span>
									{{ ! empty($leve->location) ? $leve->location : '' }}{{ ! empty($leve->location) && ! empty($leve->minor_location) ? ',' : '' }}
									{{ ! empty($leve->minor_location) ? $leve->minor_location : '' }}
									&nbsp;
								</div>
							</div>
							<div class='panel-footer text-right'>
								<a href='/leve/breakdown/{{ $leve->id }}' class='btn btn-primary'>Detailed Breakdown</a>
							</div>
						</div>
					</div>
					@endforeach
				</div>
				@endforeach
			</div>
			@endforeach

		</fieldset>
	</div>
</div>

@stop
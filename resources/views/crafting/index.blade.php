@extends('app')

@section('meta')
	<meta name="robots" content="nofollow">
@stop

@section('vendor-css')
	<link href='//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' rel='stylesheet'>
	<link href='{{ cdn('/css/bootstrap-switch.css') }}' rel='stylesheet'>
@stop

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/home.js') }}'></script>
	<script type='text/javascript' src='{{ cdn('/js/bootstrap-switch.js') }}'></script>
	<script type='text/javascript' src='{{ cdn('/js/crafting-index.js') }}'></script>
@stop

@section('banner')
	@if($previous)
	<a href='{{ $previous }}' class='btn btn-default pull-right' rel='tooltip' title='Load your last setup'><i class='glyphicon glyphicon-folder-open'></i></a>
	@endif
	<h1>Crafting Calculator</h1>
	<h2>Display all the materials needed to craft one of each item between two levels.</h2>
	<p>In general this will not level you to your desired level.  Visit the <a href='/leve'>Leves</a> page when you're done crafting!</p>
@stop

@section('content')

	@if(isset($error) && $error)
	<div class='alert alert-danger'>
		The job you selected is unrecognized.  Try again.
	</div>
	@endif

	{!! Form::open(['url' => '/crafting', 'class' => 'form-horizontal crafting-form', 'autocomplete' => 'off']) !!}
		<div class='row'>
			<div class='col-sm-3 col-lg-2'>
				<fieldset>
					<legend><img src='/img/ilvl.png' width='18' height='18' rel='tooltip' title='Item Level (ilvl)' style='position: relative; top: -2px;'> Range</legend>

					<div class='row'>
						<div class='col-xs-4' style='line-height: 34px;'>
							Start
						</div>
						<div class='col-xs-8'>
							<input type='number' class='form-control' name='start' id='recipe-level-start' value='1'>
						</div>
					</div>

					<div class='row margin-top'>
						<div class='col-xs-4' style='line-height: 34px;'>
							End
						</div>
						<div class='col-xs-8'>
							<input type='number' class='form-control' name='end' id='recipe-level-end' value='5'>
						</div>
					</div>
				</fieldset>

				<hr>

				<span class='label label-info' style='display: block;'>
					<i class='glyphicon glyphicon-question-sign'></i> Need help with your ilvl?
				</span>

				<a href='#' class='select-recipe-level btn btn-default btn-block margin-top' data-toggle='modal' data-target='#ilvl-modal'><img src='/img/ilvl.png' width='16' height='16' rel='tooltip' title='Item Level (ilvl)' style='position: relative; top: -2px;'> Finder</a>

				{{-- @if(isset($account) && $account)

				<a href='#' class='btn btn-default btn-block margin-top' id='account-ilvl'><img src='{!! $account['avatar'] !!}' width='16' height='16' class='border-radius'> Use Account</a>

				@else

				<span class='label label-default hidden-xs' style='display: block; margin-top: 20px;'>
					Use the Account option<br>for more functionality!
				</span>

				@endif --}}
			</div>
			<div class='col-sm-9 col-lg-10'>
				<fieldset class='margin-bottom mobile-margin-top'>
					<legend>Class</legend>
					<div class='btn-group jobs-list'>
						@foreach($job_list as $job)
						<?php $this_job = $job->id == reset($crafting_job_ids); ?>
						<?php $this_level = $account ? $account['levels'][strtolower($job->name)] : 0; ?>
						<label class='btn btn-primary class-selector{{ $this_job ? ' select-me' : '' }}' data-level='{{ $this_level }}'>
							<input type='checkbox' name='classes[]' value='{{ $job->abbr }}' class='hidden'>
							<img src='/img/jobs/{{ $job->abbr }}-inactive.png' data-active-src='/img/jobs/{{ $job->abbr }}-active.png' width='24' height='24' rel='tooltip' title='{{ $job->name }}'>
						</label>
						@endforeach
					</div>
					<div class='margin-top hidden-xs hidden' id='crafting-multiple-classes'><small><em>CTRL/CMD click to select multiple classes!</em> <a href='#' class='hide-me' data-storage='crafting-multiple-classes' data-target='#crafting-multiple-classes'>Got it, hide this message</a></small></div>
				</fieldset>

				<fieldset style='margin-top: 28px;'>
					<legend>Options</legend>

					<div>
						<span>
							<input type='checkbox' name='self_sufficient' id='self_sufficient_switch' value='1' checked='checked' class='bootswitch' data-on-text='YES' data-off-text='NO' data-size='small'>
						</span>
						<strong class='margin-left'>Self Sufficient</strong> <small class='visible-sm-inline visible-md-inline visible-lg-inline'> <i class='glyphicon glyphicon-question-sign pointer text-info' data-toggle='popover' data-title='Self Sufficient Defined' data-content='<em>"I want to gather things manually instead of buying them."</em><hr><small>Option is saved and used throughout the site.</small>'></i></small>
					</div>
				</fieldset>

				<fieldset>
					<legend class='visible-xs' style='padding-top: 28px;'>Inclusions</legend>

					<div class='inclusions-list margin-top'>
						{{-- Ignore airship pieces here, won't ever show --}}
						{{-- <span class='label label-default opaque' rel='tooltip' title='Click to toggle Airship Pieces' data-ids='{{ implode(',', range(90,93)) }}'>Airship Pieces</span> --}}
						<span class='label label-default opaque' rel='tooltip' title='Click to toggle Dye' data-ids='55'>Dye</span>
						<span class='label label-default opaque' rel='tooltip' title='Click to toggle Furniture.  Doors, Windows, Fishmeal, etc' data-ids='57,82,{{ implode(',', range(65,80)) }}'>Furniture &amp; Housing</span>
						<span class='label label-default opaque' rel='tooltip' title='Click to toggle Minions' data-ids='81'>Minions</span>
						<span class='label label-default opaque' rel='tooltip' title='Click to toggle Miscellany; Feed, Wheels, Etc' data-ids='61'>Miscellany</span>
						<span class='label label-default opaque' rel='tooltip' title='Click to toggle Others; Components, Chocobo stuff, Books, etc' data-ids='63'>Others</span>
						<span class='label label-default opaque' rel='tooltip' title='Click to toggle Parts; Augmentations, Linings, Pads, Plates, Etc' data-ids='56'>Parts</span>
						<span class='label label-default opaque' rel='tooltip' title='Click to toggle Prisms' data-ids='60'>Prisms</span>
						<input type='hidden' name='inclusions' id='inclusions' value=''>
					</div>

				</fieldset>

				<div class='mobile-text-right' style='margin-top: 30px;'>
					<button type='submit' class='btn btn-success btn-lg'>Synthesize!</button>
				</div>
			</div>
		</div>
	{!! Form::close() !!}
@stop

@section('modals')

<div class="modal fade" id='ilvl-modal'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><img src="/img/ilvl.png" width="24" height="24"> Recipe to Item Level Converter</h4>
			</div>
			<div class="modal-body">
				<p>The item level range for Recipes below level 50 are the same as the recipe level.  At 50+, the levels separate.</p>
				<p>Use the radio buttons, or simply click on a level range for a shortcut!</p>
				<div class='row'>
					<div class='col-xs-12 col-sm-6'>
						<table class='table table-condensed'>
							<thead>
								<tr>
									<th>Level</th>
									<th class='text-center'>Start</th>
									<th class='text-center'>End</th>
								</tr>
							</thead>
							<tbody>
								{{-- Show 1 to 50 --}}
								@foreach(range(1,50 - 4,5) as $level)
								<tr>
									<td>
										<a href='#' class='select-range'>{{ $level }} - {{ $level + 4 }}</a>
									</td>
									<td class='text-center'>
										<input type='radio' name='start' id='start{{ $level }}' data-start='{{ $level }}'>
									</td>
									<td class='text-center'>
										<input type='radio' name='end' id='end{{ $level + 4 }}' data-end='{{ $level + 4 }}'>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<div class='col-xs-12 col-sm-6'>
						<table class='table table-condensed'>
							<thead>
								<tr>
									<th>Level</th>
									<th>ilvl</th>
									<th class='text-center'>Start</th>
									<th class='text-center'>End</th>
								</tr>
							</thead>
							<tbody>
								@foreach ([55, 70, 90, 110] as $key => $value)
								<tr>
									<td>
										50 <small class='label label-default'>{{ $key + 1 }} <i class='glyphicon glyphicon-star'></i></small>
									</td>
									<td>
										<a href='#' class='select-range'>{{ $value }}</a>
									</td>
									<td class='text-center'>
										<input type='radio' name='start' id='start50-{{ $key + 1 }}' data-start='{{ $value }}'>
									</td>
									<td class='text-center'>
										<input type='radio' name='end' id='end50-{{ $key + 1 }}' data-end='{{ $value }}'>
									</td>
								</tr>
								@endforeach
								<tr>
									<td>
										51 - 55
									</td>
									<td>
										<a href='#' class='select-range'>115 - 136</a>
									</td>
									<td class='text-center'>
										<input type='radio' name='start' id='start51' data-start='115'>
									</td>
									<td class='text-center'>
										<input type='radio' name='end' id='end55' data-end='136'>
									</td>
								</tr>
								<tr>
									<td>
										56 - 60
									</td>
									<td>
										<a href='#' class='select-range'>139 - 160</a>
									</td>
									<td class='text-center'>
										<input type='radio' name='start' id='start56' data-start='139' checked='checked'>
									</td>
									<td class='text-center'>
										<input type='radio' name='end' id='end60' data-end='160' checked='checked'>
									</td>
								</tr>
								<tr>
									<td>
										61 - 70
									</td>
									<td>
										<a href='#' class='select-range'>161 - 375</a>
									</td>
									<td class='text-center'>
										<input type='radio' name='start' id='start61' data-start='161' checked='checked'>
									</td>
									<td class='text-center'>
										<input type='radio' name='end' id='end70' data-end='375' checked='checked'>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class='modal-footer'>
				<a href='#' class='btn btn-success choose'>
					Use Selected Item Level Range
				</a>
			</div>
		</div>
	</div>
</div>

@stop
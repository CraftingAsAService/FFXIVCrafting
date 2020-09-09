@extends('app')

@section('vendor-css')
	<style>
		.panel.success .panel-heading {
			border-bottom: 0;
		}
		.panel:not(.success) {
			min-height: 185px;
		}
	</style>
@stop

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/hunting.js') }}'></script>
@stop

@section('banner')
	<h1>
		<img src='/img/mob.png' width='32' height='32' style='position: relative; top: -4px;'>
		Hunting Log
	</h1>
@stop

@section('content')

	<fieldset>
		<legend>Configure Hunting Log Ranks</legend>

		<div class='row text-center'>
			@foreach (['ACN', 'ARC', 'CNJ', 'GLA', 'LNC', 'MRD', 'PGL', 'ROG', 'THM'] as $class)
				<div class='col-sm-1'>
					<label class='class-selector' data-class='{{ $class }}'>
						<img src='/img/jobs/{{ $class }}.png' width='24' height='24' rel='tooltip' title='{{ $class }}'> <span class='abbr'>{{ $class }}</span>
					</label>
					<div>
						<img src='/img/mob-inactive.png' class='rank-switcher pointer' data-class='{{ $class }}' data-section='0' data-max='5' width='20' height='24'>
					</div>
				</div>
			@endforeach
			@foreach ($companies as $icon => $name)
				<div class='col-sm-1'>
					<label class='class-selector' data-class='{{ $icon }}'>
						<img src='/img/jobs/{{ $icon }}.png' width='24' height='24' rel='tooltip' title='{{ $name }}'> <span class='abbr'>{{ $icon }}</span>
					</label>
					<div>
						<img src='/img/mob-inactive.png' class='rank-switcher pointer' data-class='{{ $icon }}' data-section='0' data-max='3' width='20' height='24'>
					</div>
				</div>
			@endforeach
		</div>

		<div style='margin-top: 30px;'>
			@foreach ($huntingData as $section => $areas)
				{{-- <h2>{{ $section }}</h2> --}}

				@foreach ($areas as $area => $data)
					<div class='hunting-box hidden'>
						<h3>{{ $area }}</h3>

						<div class='row ranks'>
							@foreach ($data as $row)
								<div class='col-sm-3 col-lg-2 rank hidden' data-class='{{ $row['class'] }}' data-rank='{{ $row['rank'] }}' data-section='{{ ceil((int) $row['rank'] / 10) }}'>
									<div class='panel panel-default'>
										<div class='panel-heading'>
											<input type='checkbox' class='hunt-switcher pull-right' id='{{ $row['class'] . preg_replace('/\./', '-', $row['rank']) }}'>
											<img src='/img/jobs/{{ $row['class'] }}.png' width='24' height='24'>
											<span class='abbr'>{{ $row['class'] }}</span>
											- {!! preg_replace('/\.(\d)$/', ' <small class="text-muted">$1</small>', $row['rank']) !!}
										</div>
										<div class='panel-body text-center'>
											<div>
												<label for='{{ $row['class'] . preg_replace('/\./', '-', $row['rank']) }}'>
													<img src='/img/huntinglog/{{ $row['image'] }}' alt='{{ $row['task'] }}' class='pointer'>
												</label>
											</div>
											{{ $row['task'] }}{{--  x {{ $row['number'] }} --}}<br>
											<small>{!! preg_replace('/(\w) \(/', '$1<br>(', preg_replace('/^\(/', '&nbsp;<br>(', $row['location'] ?? '')) !!}</small>
										</div>
									</div>
								</div>
							@endforeach
						</div>
					</div>
				@endforeach
			@endforeach
		</div>
	</fieldset>

	<p>Thanks to the <a href='https://ffxiv.consolegameswiki.com/wiki/Hunting_Log' target='_blank'>FFXIV Wiki</a> for the data and images!</p>
@stop
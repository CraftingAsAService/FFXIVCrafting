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
	<script type='text/javascript'>
		$(function() {
			// On clicking a class icon, fill in the level
			$('.class-selector').click(function() {
				var level = parseInt($(this).data('level'));

				if (level == 0)
					return;

				$('.recipe-level-select a').each(function() {
					var el = $(this),
						start = parseInt(el.data('start')),
						end = parseInt(el.data('end'));
						
					if (level >= start && end >= level)
						el.trigger('click');

					return;
				});
				
				return;
			});

			$('.class-selector.active').trigger('click');
		});
	</script>
@stop

@section('banner')
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
					<legend>Recipe Level</legend>
					<div class='list-group recipe-level-select'>
						@foreach(range(1,46,5) as $level)
						<a href='#' class='list-group-item{{ $level == 1 ? ' active' : '' }}' data-start='{{ $level }}' data-end='{{ $level + 4 }}'>
							{{ $level }} - {{ $level + 4 }}
						</a>
						@endforeach
						<a href='#' class='list-group-item' data-start='55' data-end='55'>
							<i class='glyphicon glyphicon-star'></i>
						</a>
						<a href='#' class='list-group-item' data-start='70' data-end='70'>
							<i class='glyphicon glyphicon-star'></i>
							<i class='glyphicon glyphicon-star'></i>
						</a>
						<a href='#' class='list-group-item' data-start='90' data-end='90'>
							<i class='glyphicon glyphicon-star'></i>
							<i class='glyphicon glyphicon-star'></i>
							<i class='glyphicon glyphicon-star'></i>
						</a>
						<a href='#' class='list-group-item' data-start='110' data-end='110'>
							<i class='glyphicon glyphicon-star'></i>
							<i class='glyphicon glyphicon-star'></i>
							<i class='glyphicon glyphicon-star'></i>
							<i class='glyphicon glyphicon-star'></i>
						</a>
					</div>
					<input type='hidden' name='start' id='recipe-level-start' value='1'>
					<input type='hidden' name='end' id='recipe-level-end' value='5'>
				</fieldset>
			</div>
			<div class='col-sm-9 col-lg-10'>
				<fieldset class='margin-bottom'>
					<legend>Class</legend>
					<div class='btn-group jobs-list' data-toggle='buttons'>
						@foreach($job_list as $job)
						<label class='btn btn-primary class-selector{{ $job->id == reset($crafting_job_ids) ? ' active' : '' }}' data-level='{{ $account ? $account['levels'][strtolower($job->en_name->term)] : 0 }}'>
							<input type='radio' name='class' value='{{ $job->en_abbr->term }}'{{ $job->id == reset($crafting_job_ids) ? ' checked="checked"' : '' }}> 
							<img src='/img/classes/{{ $job->en_abbr->term }}.png' rel='tooltip' title='{{ $job->name->term }}'>
						</label>
						@endforeach
					</div>
				</fieldset>
				<fieldset style='margin-top: 30px;'>
					<legend>Options</legend>

					<div>
						<span>
							<input type='checkbox' name='self_sufficient' id='self_sufficient_switch' value='1' checked='checked' class='bootswitch' data-on-text='YES' data-off-text='NO'>
						</span>
						<strong class='margin-left'>Self Sufficient</strong> <small><em>- "I want to gather things manually instead of buying them"</em></small>
					</div>

					<div class='margin-top'>
						<span>
							<input type='checkbox' name='misc_items' id='misc_items_switch' value='1' class='bootswitch' data-on-text='YES' data-off-text='NO'>
						</span>
						<strong class='margin-left'>Dyes &amp; Furniture</strong>
					</div>

					<div class='margin-top'>
						<span>
							<input type='checkbox' name='component_items' id='component_items_switch' value='1' class='bootswitch' data-on-text='YES' data-off-text='NO'>
						</span>
						<strong class='margin-left'>Component Materials</strong> <small><em>- Requires Dyes &amp; Furniture</em></small>
					</div>
				</fieldset>
				
				<div style='margin-top: 30px;'>
					<button type='submit' class='btn btn-success btn-lg'>Synthesize!</button>
				</div>
				<div style='margin-top: 30px;'>
					<a href='/crafting/advanced'>Advanced options</a>
					@if($previous)
					or <a href='{{ $previous }}'>load your last setup</a>
					@endif
				</div>
			</div>
		</div>
	{!! Form::close() !!}
@stop
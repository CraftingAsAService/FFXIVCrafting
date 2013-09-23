@extends('layout')

@section('javascript')
<script type='text/javascript' src='http://xivdb.com/tooltips.js'></script>
<script type='text/javascript' src='/js/recipe_book.js'></script>
@stop

@section('content')

<h1>
	<i class='glyphicon glyphicon-book'></i>
	Recipe Book
</h1>

<table class='table table-bordered table-striped table-responsive text-center' id='recipe-book'>
	<thead>
		<tr>
			<th class='text-center valign'>Recipe</th>
			<th class='text-center valign'>Class</th>
			<th class='text-center valign'>Between<br>Levels</th>
			<th class='text-center valign' rel='tooltip' title='Add to Shopping List'>
				<i class='glyphicon glyphicon-shopping-cart'></i>
				<i class='glyphicon glyphicon-plus'></i>
			</th>
		</tr>
		<tr>
			<th class='valign'>
				<div class='input-group' style='margin: 0 auto;' id='name-search'>
					<input type='text' class='form-control'>
					<span class='input-group-btn'>
						<button class='btn btn-success' type='button'><i class='glyphicon glyphicon-search'></i></button>
					</span>
				</div>
			</th>
			<th class='valign'>
				<div class='input-group' style='margin: 0 auto;' id='class-search'>
					<button type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown' data-class='all'>
						<img src='/img/classes/NA.png'>
						<span class='caret'></span>
					</button>
					<ul class='dropdown-menu'>
						<li>
							<a href='#' data-class='all'>
								<img src='/img/classes/NA.png'>
								All Classes
							</a>
						</li>
						@foreach(array('CRP','BSM','ARM','GSM','LTW','WVR','ALC','CUL') as $job)
						<li>
							<a href='#' data-class='{{ $job }}'>
								<img src='/img/classes/{{ $job }}.png'>
								{{ $job_list[$job] }}
							</a>
						</li>
						@endforeach
					</ul>
				</div>
			</th>
			<th class='valign'>
				<div>
					<input type='number' class='form-control input-sm' value='1' min='1' max='70' id='min-level' rel='tooltip' title='Min Level'>
				</div>
				<div>
					<input type='number' class='form-control input-sm' value='70' min='1' max='70' id='max-level' rel='tooltip' title='Max Level'>
				</div>
			</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tfoot>
		{{-- @include('recipes.results_footer') --}}
	</tfoot>
	<tbody>
		@include('recipes.results')
	</tbody>
</table>

@stop
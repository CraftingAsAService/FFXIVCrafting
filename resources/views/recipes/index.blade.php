@extends('app')

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/recipe_book.js') }}'></script>
@stop

@section('banner')
	<a href='/recipes' class='btn btn-default pull-right' id='load-setup' rel='tooltip' title='Load saved setup'><i class='glyphicon glyphicon-folder-open'></i></a>
	<a href='#' class='btn btn-default margin-right pull-right' id='save-setup' rel='tooltip' title='Save setup for later'><i class='glyphicon glyphicon-floppy-disk'></i></a>

	<h1>
		Recipe Book
	</h1>
@stop

@section('content')
<div class='table-responsive'>
	<table class='table table-bordered table-striped table-responsive text-center' id='recipe-book'>
		<thead>
			<tr>
				<th class='text-center valign'>Recipe</th>
				<th class='text-center valign'>Class</th>
				<th class='text-center valign'>Between<br>Levels</th>
				<th class='text-center valign' rel='tooltip' title='Add to Crafting List'>
					<i class='glyphicon glyphicon-shopping-cart'></i>
					<i class='glyphicon glyphicon-plus'></i>
				</th>
			</tr>
			<tr>
				<th class='valign'>
					<div class='row'>
					<div class='col-xs-9'>
						<div class='input-group' style='margin: 0 auto;' id='name-search'>
							<input type='text' class='form-control'>
							<span class='input-group-btn'>
								<button class='btn btn-success' type='button'><i class='glyphicon glyphicon-search'></i></button>
							</span>
						</div>
					</div>
					<div class='col-xs-3'>
						<select name='order-by' id='order-by' class='form-control'>
							<option value='' selected='selected'>Name: a to z</option>
							<option value='name_desc'>Name: z to a</option>
							<option value='level_asc'>Level: low to high</option>
							<option value='level_desc'>Level: high to low</option>
						</select>
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
							@foreach($crafting_job_list as $job)
							<li>
								<a href='#' data-class='{{ $job->en_abbr->term }}'>
									<img src='/img/classes/{{ $job->en_abbr->term }}.png'>
									{{ $job->name->term }}
								</a>
							</li>
							@endforeach
						</ul>
					</div>
				</th>
				<th class='valign'>
					<div>
						<input type='number' class='form-control input-sm' value='1' min='1' max='999' id='min-level' rel='tooltip' title='Min Level'>
					</div>
					<div>
						<input type='number' class='form-control input-sm' value='999' min='1' max='999' id='max-level' rel='tooltip' title='Max Level'>
					</div>
				</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tfoot>
			
		</tfoot>
		<tbody>
			@include('recipes.results')
		</tbody>
	</table>
</div>

@stop
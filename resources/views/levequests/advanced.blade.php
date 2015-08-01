@extends('app')

@section('vendor-css')
	<link href='{{ cdn('/css/bootstrap-multiselect.css') }}' rel='stylesheet'>
@stop

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/bootstrap-multiselect.js') }}'></script>
	<script src='{{ cdn('/js/levequests-advanced.js') }}'></script>
@stop

@section('banner')
	<a href='/levequests/advanced' class='btn btn-default pull-right' id='load-setup' rel='tooltip' title='Load saved setup'><i class='glyphicon glyphicon-folder-open'></i></a>
	<a href='#' class='btn btn-default margin-right pull-right' id='save-setup' rel='tooltip' title='Save setup for later'><i class='glyphicon glyphicon-floppy-disk'></i></a>

	<h1>Levequests</h1>
	<h2>Advanced Version</h2>
@stop

@section('content')

<div class='panel panel-default'>
	<div class='panel-heading'>
		Levequest Filter
	</div>
	<div class='panel-body'>
		<form class='leve-form form form-inline'>
			<div class='row'>
				<div class='col-sm-12'>
					<button type='button' role='button' class='filter-form btn btn-success pull-right'>
						Filter &raquo;
					</button>

					<small class='pull-right margin-right margin-top'><a href='#' class='toggle-advanced'>Advanced &raquo;</a></small>

					<div class='form-group'>
						<label>Class</label>
						<select class='multiselect hidden' multiple='multiple' id='class-selector'>
							@foreach($crafting_job_list as $job)
							<option value='{{ $job->abbr }}'{{ $job->id == reset($crafting_job_ids) ? ' selected="selected"' : '' }}>
								{{ $job->name }}
							</option>
							@endforeach
						</select>
					</div>

					<div class='form-group margin-left'>
						<label>Min Level</label>
						<input type='number' min='0' max='{{ config('site.max_level') }}' value='1' class='form-control text-center' id='min-level'>
					</div>

					<div class='form-group margin-left'>
						<label>Max Level</label>
						<input type='number' min='0' max='{{ config('site.max_level') }}' value='{{ config('site.max_level') }}' class='form-control text-center' id='max-level'>
					</div>

					<div class='form-group margin-left'>
						<label>Type</label>
						<select class='multiselect hidden' multiple='multiple' id='type-selector'>
							@foreach(['Town', 'Field', 'Courier', 'Reverse Courier'] as $role)
							<option value='{{ $role }}' selected='selected'>{{ $role }}</option>
							@endforeach
						</select>
					</div>

					<div class='form-group margin-left'>
						<div class='checkbox'>
							<label>
								<input type='checkbox' id='repeatable_only'> Repeats Only
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class='row advanced hidden margin-top'>
				<div class='col-sm-12 margin-top'>
					<div class='form-group margin-left'>
						<input type='text' id='leve_item' placeholder='Item Name Search' class='form-control leve-text-search'>
					</div>
					<div class='form-group margin-left'>
						<input type='text' id='leve_name' placeholder='Leve Name Search' class='form-control leve-text-search'>
					</div>
					<div class='form-group margin-left'>
						<input type='text' id='leve_location' placeholder='Location Name Search' class='form-control leve-text-search'>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<div class='table-responsive'>
	<table class='leve-table table table-bordered table-striped'>
		<thead>
			<tr>
				<th class='invisible'>&nbsp;</th>
				<th class='valign'>
					Item, Level and Amount
				</th>
				<th class='text-center valign'>Leve Name and Type</th>
				<th class='text-center'>XP</th>
				<th class='text-center'>Gil</th>
				<th class='text-center valign'>Location</th>
				<th class='text-center valign' rel='tooltip' title='View Leve Rewards'>
					<i class='glyphicon glyphicon-gift'></i>
				</th>
				<th class='text-center valign' rel='tooltip' title='Add to Crafting List'>
					<i class='glyphicon glyphicon-shopping-cart'></i>
					<i class='glyphicon glyphicon-plus'></i>
				</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>

@stop
@extends('wrapper.layout')

@section('vendor-css')
	<link href='{{ cdn('/css/bootstrap-multiselect.css') }}' rel='stylesheet'>
@stop

@section('javascript')
	<script type='text/javascript' src='{{ cdn('/js/bootstrap-multiselect.js') }}'></script>
	<script src='{{ cdn('/js/leves.js') }}'></script>
@stop

@section('banner')
	<a href='/leve' class='btn btn-default pull-right' id='load-setup' rel='tooltip' title='Load saved setup'><i class='glyphicon glyphicon-folder-open'></i></a>
	<a href='#' class='btn btn-default margin-right pull-right' id='save-setup' rel='tooltip' title='Save setup for later'><i class='glyphicon glyphicon-floppy-disk'></i></a>

	<h1>Levequest Information</h1>
@stop

@section('content')

<div class='alert alert-warning'>
	This page is going away and will be redirected to the <a href='/levequests'>New Levequests Page</a>.  
	If you strongly feel this version is better, please <a href='mailto:tickthokk@gmail.com?Subject=I like the old Leve page'>let me know</a>.  
</div>

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
							<option value='{{ $job->en_abbr->term }}'{{ $job->id == reset($crafting_job_ids) ? ' selected="selected"' : '' }}>
								{{{ $job->name->term }}}
							</option>
							@endforeach
						</select>
					</div>

					<div class='form-group margin-left'>
						<label>Min Level</label>
						<input type='number' min='0' max='45' step='5' value='1' class='form-control text-center' id='min-level'>
					</div>

					<div class='form-group margin-left'>
						<label>Max Level</label>
						<input type='number' min='0' max='45' step='5' value='45' class='form-control text-center' id='max-level'>
					</div>

					<div class='form-group margin-left'>
						<label>Type</label>
						<select class='multiselect hidden' multiple='multiple' id='type-selector'>
							@foreach(array('Town', 'Field', 'Courier', 'Reverse Courier') as $role)
							<option value='{{ $role }}' selected='selected'>{{ $role }}</option>
							@endforeach
						</select>
					</div>

					<div class='form-group margin-left'>
						<div class='checkbox'>
							<label>
								<input type='checkbox' id='triple_only'> Triples Only
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class='row advanced{{ Input::get('name') ? '' : ' hidden' }} margin-top'>
				<div class='col-sm-12 margin-top'>
					<div class='form-group margin-left'>
						<input type='text' id='leve_item' placeholder='Item Name Search' class='form-control leve-text-search'>
					</div>
					<div class='form-group margin-left'>
						<input type='text' id='leve_name' placeholder='Leve Name Search' class='form-control leve-text-search' value='{{ Input::get('name') }}'>
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
				<th class='text-center valign' rel='tooltip' title='View Leve Rewards' data-container='body'>
					<i class='glyphicon glyphicon-gift'></i>
				</th>
				<th class='text-center valign' rel='tooltip' title='Add to Shopping List' data-container='body'>
					<i class='glyphicon glyphicon-shopping-cart'></i>
					<i class='glyphicon glyphicon-plus'></i>
				</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>

<div class='well'>
	Information gathered from <a href='http://www.bluegartr.com/threads/118238-DoH-DoL-Leves-Dyes-Material-Tiers' target='_blank'>BlueGartr user Seravi Edalborez</a>.  Thanks Seravi!
</div>

@stop
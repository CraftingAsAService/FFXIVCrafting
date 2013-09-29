@extends('layout')

@section('vendor-css')
	<link href='/css/bootstrap-multiselect.css' rel='stylesheet'>
@stop

@section('javascript')
	<script type='text/javascript' src='http://xivdb.com/tooltips.js'></script>
	<script type='text/javascript' src='/js/bootstrap-multiselect.js'></script>
	<script src='/js/home.js'></script>
	<script src='/js/leves.js'></script>
@stop

@section('content')

<h1>Leve Information</h1>

<div class='panel panel-default'>
	<div class='panel-heading'>
		Leve Filter
	</div>
	<div class='panel-body'>
		<form class='leve-form form form-inline'>
			<button type='button' role='button' class='filter-form btn btn-success pull-right'>
				Filter &raquo;
			</button>

			<div class='form-group'>
				<label>Class</label>
				<select class='multiselect hidden' multiple='multiple' id='class-selector'>
					@foreach(array('CRP','BSM','ARM','GSM','LTW','WVR','ALC','CUL') as $job)
					<option value='{{ $job }}'{{ $job == 'CRP' ? ' selected="selected"' : '' }}>{{ $job_list[$job] }}</option>
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
					@foreach(array('Town', 'Field', 'Courier', 'Unknown') as $role)
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
		</form>
	</div>
</div>

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
			<th class='text-center valign' rel='tooltip' title='Add to Shopping List'>
				<i class='glyphicon glyphicon-shopping-cart'></i>
				<i class='glyphicon glyphicon-plus'></i>
			</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>

<div class='well'>
	Information gathered from <a href='http://www.bluegartr.com/threads/118238-DoH-DoL-Leves-Dyes-Material-Tiers' target='_blank'>BluGartr user Seravi Edalborez</a>.  Thanks!
</div>

@stop
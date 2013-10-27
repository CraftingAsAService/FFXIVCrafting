<tr>
	<th class='text-center valign'>
		{{ $list->links() }}
	</th>
	<th colspan='3' class='text-center valign'>
		<div class='form-group'>
			<label class='control-label'>Per Page</label>
			<select id='per_page' class='form-control'>
				<option value='10'@if($per_page == 10) selected='selected'@endif>10</option>
				<option value='25'@if($per_page == 25) selected='selected'@endif>25</option>
				<option value='50'@if($per_page == 50) selected='selected'@endif>50</option>
			</select>
		</div>
	</th>
</tr>
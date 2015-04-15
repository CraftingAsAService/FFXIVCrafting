@if ($errors->all())
	<div class="alert alert-danger">
		<h4>The Following Errors were found:</h4>
		@foreach($errors->all() as $error)
			{!! $error !!} <br />
		@endforeach
	</div>
@endif
@if (Session::has('flash_notification.message'))
	<div class="alert alert-{{ Session::get('flash_notification.level') }}">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

		{{ Session::get('flash_notification.message') }}
	</div>
@endif
					@if ($errors->all())
						<div class="alert alert-danger">
							<h4>The Following Errors were found:</h4>
							@foreach($errors->all() as $error)
								{{$error}} <br />
							@endforeach
						</div>
					@endif
					@if (Session::has('error'))
						<div class='alert alert-danger'>
							<button type='button' class='close' data-dismiss='alert'>&times;</button>
							<h4>Error</h4>
							{{ Session::get('error') }}
						</div>
					@endif
					@if (Session::has('notify'))
						<div class='alert alert-warn'>
							<button type='button' class='close' data-dismiss='alert'>&times;</button>
							<h4>Warning</h4>
							{{ Session::get('notify') }}
						</div>
					@endif
					@if (Session::has('success'))
						<div class='alert alert-success'>
							<button type='button' class='close' data-dismiss='alert'>&times;</button>
							<h4>Success</h4>
							{{ Session::get('success') }}
						</div>
					@endif
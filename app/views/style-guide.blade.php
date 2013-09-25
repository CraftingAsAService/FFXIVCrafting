@extends('layout')

@section('content')

	<h1>h1. Headline 1 <small>Subtext if needed</small></h1>
				<h2>h2. Headline 2 <small>Subtext if needed</small></h2>
				<h3>h3. Headline 3 <small>Subtext if needed</small></h3>
				<h4>h4. Headline 4 <small>Subtext if needed</small></h4>
				<h5>h5. Headline 5 <small>Subtext if needed</small></h5>
				<h6>h6. Headline 6 <small>Subtext if needed</small></h6>
				<p>This is just a test of the <a href="#">emergency</a> broadcast system.</p>

				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sit amet nunc mi. In facilisis ac nibh eu vestibulum. Curabitur sit amet adipiscing nulla, at ullamcorper nisi. Suspendisse scelerisque nisi risus, et ornare velit viverra eget. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Morbi vitae dolor at sapien ullamcorper tempus. Vestibulum venenatis leo nec arcu pulvinar venenatis. Etiam semper venenatis cursus.</p>

				<p>Sed eget ligula ligula. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aliquam sem sapien, elementum non dui nec, posuere mollis enim. Nulla venenatis iaculis lacus vel iaculis. Curabitur tincidunt libero ut lacinia scelerisque. Quisque vel ornare orci, vitae pulvinar sem. Maecenas et massa eu ante vehicula vehicula venenatis eu magna. Sed euismod leo ultrices diam pulvinar tincidunt. Fusce luctus congue libero, rhoncus viverra purus semper a.</p>

				<p>In hac habitasse platea dictumst. Morbi sagittis nulla a urna tincidunt, non vulputate orci varius. Mauris nec justo nisl. Pellentesque feugiat, magna quis porta molestie, ipsum felis fermentum nunc, a convallis est dui vitae mi. Mauris dignissim ornare nulla nec dictum. Nulla venenatis, tellus sit amet fringilla semper, erat tellus mollis sapien, nec tempus diam metus et nunc. Sed accumsan tincidunt massa. Morbi posuere scelerisque magna.</p>

				<div class="alert alert-dismissable alert-success">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<strong>Well done!</strong> You successfully read this important <a href="#" class="alert-link">alert message</a>.
				</div>
				<div class="alert alert-dismissable alert-info">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<strong>Heads up!</strong> This alert needs your attention, but it's not super important.
				</div>
				<div class="alert alert-dismissable alert-warning">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<strong>Warning!</strong> Best check yo self, you're not looking too good.
				</div>
				<div class="alert alert-dismissable alert-danger">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<strong>Oh snap!</strong> Change a few things up and try submitting again.
				</div>

				<div class="row">
					<div class="col-md-4">
						<a href="#" class="thumbnail">
							<img src="http://placehold.it/300x200" class="img-responsive">
						</a>
					</div>
					<div class="col-md-4">
						<a href="#" class="thumbnail">
							<img src="http://placehold.it/300x200" class="img-responsive">
						</a>
					</div>
					<div class="col-md-4">
						<a href="#" class="thumbnail">
							<img src="http://placehold.it/300x200" class="img-responsive">
						</a>
					</div>
				</div>


				<p>
					Labels:
					<span class="label label-default">Default</span>
					<span class="label label-primary">Primary</span>
					<span class="label label-success">Success</span>
					<span class="label label-info">Info</span>
					<span class="label label-warning">Warning</span>
					<span class="label label-danger">Danger</span>
				</p>

				<ul class="pagination">
					<li class="disabled"><a href="#">&laquo;</a></li>
					<li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
					<li><a href="#">2</a></li>
					<li><a href="#">3</a></li>
					<li><a href="#">4</a></li>
					<li><a href="#">5</a></li>
					<li><a href="#">&raquo;</a></li>
				</ul>

				<ol class="breadcrumb">
					<li><a href="#">Home</a></li>
					<li><a href="#">Library</a></li>
					<li class="active">Data</li>
				</ol>



				<form role="form">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="exampleInputEmail1">Email address</label>
								<input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
							</div>
							<div class="form-group">
								<label for="exampleInputPassword1">Monies</label>
								<div class="input-group">
									<span class="input-group-addon">$</span>
									<input type="text" class="form-control">
									<span class="input-group-addon">.00</span>
								</div>
							</div>
							<button type="submit" class="btn btn-default">Submit</button>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="exampleInputEmail1">Email address</label>
								<input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
							</div>
							<div class="form-group">
								<label for="exampleInputPassword1">Job Responsibility</label>
								<select name="title" id="title" class="form-control">
									<option value="">-- Please Select --</option>
									<option value="0">Owner / President</option>
									<option value="1">Executive / Officer</option>
									<option value="2">Director</option>
									<option value="3">Manager</option>
									<option value="4">Supervisor</option>
									<option value="5">Representative</option>
									<option value="6">Analyst</option>
									<option value="7">Specialist</option>
									<option value="8">Receptionist</option>
									<option value="9">Buyer</option>
									<option value="10">Engineer</option>
									<option value="11">Accounts Payable</option>
									<option value="12">Technician</option>
									<option value="13">Consultant</option>
									<option value="14">Installer</option>
									<option value="15">Architect</option>
									<option value="16">Office Manager</option>
									<option value="17">Product Manager</option>
									<option value="18">Project Manager</option>
									<option value="19">Counter Manager</option>
									<option value="20">Account Manager</option>
									<option value="21">Inside Rep</option>
									<option value="22">Outside Rep</option>
									<option value="23">Counter Rep</option>
								</select>
							</div>
						</div>
					</div>
				</form>

				<form>

					<hr>
					<button type="submit" class="btn btn-default">Default</button>
					<button type="submit" class="btn btn-primary">Primary</button>
					<button type="submit" class="btn btn-success">Success</button>
					<button type="submit" class="btn btn-info">Info</button>
					<button type="submit" class="btn btn-warning">Warning</button>
					<button type="submit" class="btn btn-danger">Danger</button>

					<hr>

					<label class="checkbox-inline">
						<input type="checkbox" id="inlineCheckbox1" value="option1"> 1
					</label>
					<label class="checkbox-inline">
						<input type="checkbox" id="inlineCheckbox2" value="option2"> 2
					</label>
					<label class="checkbox-inline">
						<input type="checkbox" id="inlineCheckbox3" value="option3"> 3
					</label>
				</form>

				<hr>

				<form class="bs-example form-horizontal">
					<div class="form-group">
						<label for="inputEmail1" class="col-lg-2 control-label">Email</label>
						<div class="col-lg-10">
							<input type="email" class="form-control" id="inputEmail1" placeholder="Email">
						</div>
					</div>
					<div class="form-group">
						<label for="inputPassword1" class="col-lg-2 control-label">Password</label>
						<div class="col-lg-10">
							<input type="password" class="form-control" id="inputPassword1" placeholder="Password">
						</div>
					</div>
					<div class="form-group">
						<div class="col-lg-offset-2 col-lg-10">
							<div class="checkbox">
								<label>
									<input type="checkbox"> Remember me
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-lg-offset-2 col-lg-10">
							<button type="submit" class="btn btn-default">Sign in</button>
						</div>
					</div>
				</form>

				<hr>


				<div class="table-responsive">
					<table class="table table-hover table-striped">
						<thead>
							<tr>
								<th>#</th>
								<th>First Name</th>
								<th>Last Name</th>
								<th>Username</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>1</td>
								<td>Mark</td>
								<td>Otto</td>
								<td>@mdo</td>
							</tr>
							<tr>
								<td>2</td>
								<td>Jacob</td>
								<td>Thornton</td>
								<td>@fat</td>
							</tr>
							<tr>
								<td>3</td>
								<td colspan="2">Larry the Bird</td>
								<td>@twitter</td>
							</tr>
						</tbody>
					</table>
				</div>

@stop
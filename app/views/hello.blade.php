@extends('layout')

@section('vendor-css')
	<link href='//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' rel='stylesheet'>
@stop

@section('javascript')
	<script src='/js/home.js{{ $asset_cache_string }}'></script>
@stop

@section('content')

@if($host_warning)

<div class="panel panel-danger" style='margin-bottom: 60px;'>
	<div class="panel-heading">
		<h3 class="panel-title">Domain Notificiation</h3>
	</div>
	<div class="panel-body">
		You're currently using caas.thokk.com.  Please use <a href='http://craftingasaservice.com/'>http://craftingasaservice.com/</a> instead.  Thanks!
	</div>
</div>
@endif

<div class='home jumbotron'>
	<h1>Crafting as a Service</h1>
	<p>Crafting information and planning for FFXIV: ARR</p>
</div>

<div class='row'>
	<div class='col-sm-6 col-sm-push-6 news'>

		<h2>News<a href='/blog' target='_blank'><span class='glyphicon glyphicon-new-window'></span></a></h2>

		<?php $wardrobe = new \Wardrobe\Core\Repositories\DbPostRepository(); ?>
		@foreach($wardrobe->active(5) as $post)
		<div class='post'>
			<div class='name'>
				<a href='/blog/post/{{{ $post->slug }}}' target='_blank'><span class='glyphicon glyphicon-bookmark'></span>{{ $post->title }}<span class='glyphicon glyphicon-new-window'></span></a>
			</div>
			<div class='when_who'>
				Posted on <span class='when'>{{ date("M d, Y", strtotime($post->publish_date)) }}</span> 
				by <span class='who'>{{ $post->user->first_name }} {{ $post->user->last_name }}</span>
			</div>
			<div class='tags'>
				@foreach(json_decode($post->tags) as $tag)
				@if($tag->tag)
				<span class='tag'>
					<span class='glyphicon glyphicon-tag'></span> {{ $tag->tag }}
				</span>
				@endif
				@endforeach
			</div>
		</div>
		@endforeach
	</div>
	<div class='col-sm-6 col-sm-pull-6'>

		<h2>Equipment Calculator</h2>
		<p>
			Want to know what equipment you can craft at a certain level for your class?  
			Use this tool to select your disciple and level range.
		</p>
		<a href='/equipment' class='btn btn-primary'>Gear me out &raquo;</a>

		<h2>Crafting Calculator</h2>
		<p>
			Want to get everything you need before trying to level Weaver from levels 5 to 10?  
			Use this tool to select your craft and level range.
		</p>
		<a href='/crafting' class='btn btn-primary'>Vocationalize &raquo;</a>

		<h2>Career Calculator</h2>
		<p>
			Ever ask yourself when mining "What should I be digging up?", or when crafting "Just how many total Bronze Ingots do I really need to make?"
			Find the most efficient use of your pickaxe or needle with this tool!
		</p>
		<a href='/gathering' class='btn btn-primary'>See the totals &raquo;</a>
	</div>
</div>

@stop
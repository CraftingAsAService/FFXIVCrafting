@extends('wrapper.layout')

@section('title')
	Post Preview
@stop

@section('css')
	@include(theme_view('inc.css'))
@stop

@section('banner')
	@include(theme_view('inc.links'))
	<h1 class="title"></h1>
@stop

@section('content')
	<section>
		<div class="js-content"></div>
	</section>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jStorage/0.3.0/jstorage.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/marked/0.2.9/marked.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var initial = $.jStorage.get("post-{{ $id }}");
			$(".title").html(initial.title);
			$(".js-content").html(marked(initial.content));

			$.jStorage.subscribe("post-{{ $id }}", function(channel, data){
				$(".title").html(data.title);
				$(".js-content").html(marked(data.content));
			});
		});
	</script>
@stop


@extends('wrapper.layout')

@section('title')
	{{ site_title() }}
@stop

@section('css')
	@include(theme_view('inc.css'))
@stop

@section('banner')
	@include(theme_view('inc.links'))
	<h1>{{ site_title() }}</h1>
	<h4>A Development Log &amp; Crafting Diary</h4>
@stop

@section('content')
	<section class="home">
		@foreach ($posts as $post)
			@include(theme_view('inc.post'))
		@endforeach

		{{ $posts->links() }}
	</section>
@stop

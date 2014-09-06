@extends('wrapper.layout')

@section('title')
	Tags
@stop

@section('css')
	@include(theme_view('inc.css'))
@stop

@section('banner')
	@include(theme_view('inc.links'))
	<h1 class="title">Tags</h1>
@stop

@section('content')
	<section>

		@foreach (Wardrobe::tags() as $item)
			@if ($item['tag'] != "")
				<li><a href="{{ Wardrobe::route('posts.tags', $item['tag']) }}">{{ $item['tag'] }}</a></li>
			@endif
		@endforeach

	</section>
@stop

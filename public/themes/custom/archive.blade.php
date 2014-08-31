@extends('wrapper.layout')

@section('title')
	Archives
@stop

@section('css')
	@include(theme_view('inc.css'))
@stop

@section('banner')
	@include(theme_view('inc.links'))
	{{-- Archive Heading --}}
	{{-- Notice the triple brackets to escape any xss for tags and search term. --}}
	@if (isset($tag))
		<h1 class="title">{{{ ucfirst($tag) }}} Archives</h1>
	@elseif ($search)
		<h1 class="title">Results for {{{ $search }}}</h1>
	@else
		<h1 class="title">Archives</h1>
	@endif
@stop

@section('content')
	<section>

		@foreach ($posts as $post)
			@include(theme_view('inc.post'))
		@endforeach

		{{ $posts->links() }}

	</section>
@stop

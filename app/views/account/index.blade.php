@extends('wrapper.layout')

@section('banner')
	<h1>Load Character</h1>
@stop

@section('content')
	
	<img src='{{ $account['avatar'] }}'>

	{{ var_dump($account['classes']) }}

@stop
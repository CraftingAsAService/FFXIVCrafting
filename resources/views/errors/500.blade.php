@extends('app')

@section('banner')
<h1>
	Site Error
</h1>
@stop

@section('content')

@if($error)
<div class='alert alert-danger'>{{ $error }}</div>
@else
<p>
	Something's not right...
</p>
@endif

<p>
	Please <a href='/report'>report this bug!</a>  Visit the <a href='https://www.reddit.com/r/ffxivcrafting'>subreddit</a> to check for other postings, or <a href='mailto:tickthokk@gmail.com?Subject=Site Error'>email me</a>.
</p>

<img src='/img/sad-moogle.png' width='392' class='img-responsive'>

@stop
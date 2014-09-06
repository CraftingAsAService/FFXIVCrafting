@extends('wrapper.layout')

@section('banner')
<h1>
	Site Error
</h1>
@stop

@section('content')

<p>
	Something's not right...
</p>

<p>
	Please <a href='/report'>report this bug!</a>  If the whole site responds this way I'd suggest just <a href='mailto:tickthokk@gmail.com?Subject=Site Error'>emailing me</a>.
</p>

<img src='/img/sad-moogle.png' width='392' class='img-responsive'>

@stop
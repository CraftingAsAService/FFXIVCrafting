@extends(theme_view('layout'))

@section('title')
  {{ site_title() }}
@stop

@section('content')
	<section class="home">
    <h2>Recent Posts</h2>
    <ul class="archive">
      @foreach (Wardrobe::posts(array('per_page' => 5)) as $post)
        <li>
          <span><i class="icon-calendar"></i>{{ date("M d, Y", strtotime($post['publish_date'])) }}</span> <strong><a href="{{ wardrobe_url('/post/'.$post['slug']) }}">{{ $post['title'] }}</a></strong>
        </li>
      @endforeach
    </ul>
	</section>
@stop

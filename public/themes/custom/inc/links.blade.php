<div class='btn-group pull-right'>
	<button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown'>
		<i class='glyphicon glyphicon-link'></i>
		<span class='caret'></span>
	</button>
	<ul class='dropdown-menu'>
		<li><a href="/blog">Blog Index</a></li>
		<li><a href="{{ Wardrobe::route('posts.archive') }}">Archive</a></li>
		<li><a href="/blog/tags">Tags</a></li>
		<li><a href="/blog/about">About</a></li>
		<li><a href="{{ Wardrobe::route('posts.rss') }}">RSS</a></li>
	</ul>
</div>
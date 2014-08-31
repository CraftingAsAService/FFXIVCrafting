<div class="tags">
	@foreach ($post->tags as $item)
		@if ($item->tag != "")
			<a href="{{ Wardrobe::route('posts.tags', $item->tag) }}" class='label label-primary margin-right'>{{ $item->tag }}</a>
		@endif
	@endforeach
</div>

var map = {
	init:function() {
		$('a[data-toggle=tab]').on('shown.bs.tab', function(e) {
			var href = $(e.target).attr('href');
			$(href).find('.globe:not(.overscroll)').overscroll();
			$(href).find('.globe').addClass('overscroll');
		});

		$('.active a[data-toggle=tab]').trigger('shown.bs.tab');

		// Go through the item_level, look for items.  No nodes, delete the item_level
		// Same all the way down: region_level, cluster_level
		$('.item_level, .region_level, .cluster_level').each(function() {
			var el = $(this);
			if (el.find('.node_level').length == 0)
				el.remove();
			return;
		});

		$('.item_level > div:first-child, .region_level > div:first-child, .cluster_level > div:first-child').click(function() {
			var el = $(this);
			el.parent('li').toggleClass('open');
			return;
		});

		$('.vision').click(function(e) {
			e.stopPropagation();
			var el = $(this);

			el.toggleClass('off');

			

			return;
		});

		$('.find').click(function() {


			return;
		});

		return;
	}
}

$(map.init);
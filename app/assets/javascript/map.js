var map = {
	init:function() {
		$('a[data-toggle=tab]').on('shown.bs.tab', function(e) {
			var href = $(e.target).attr('href');
			$(href).find('.globe:not(.overscroll)').overscroll();
			$(href).find('.globe').addClass('overscroll');
		});

		$('.active a[data-toggle=tab]').trigger('shown.bs.tab');
	}
}

$(map.init);
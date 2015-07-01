var basic = {
	init:function() {
		$('.bootswitch').bootstrapSwitch();

		$('.recipe-level-select a').click(function(event) {
			event.preventDefault();

			var el = $(this);

			$('.recipe-level-select a.active').removeClass('active');
			el.addClass('active');

			$('#recipe-level-start').val(el.data('start'));
			$('#recipe-level-end').val(el.data('end'));

			return;
		});

		// On clicking a class icon, fill in the level
		$('.class-selector').click(function() {
			var el = $(this),
				img = el.find('img');

			var active_img = $('.class-selector:not(.multi) img.selected');
			if (active_img.length) {
				active_img.removeClass('selected')
				active_img.attr('src', active_img.data('originalSrc'));
			}

			img.addClass('selected');
			img.data('originalSrc', img.attr('src'));
			img.attr('src', img.data('activeSrc'));
			
			return;
		});

		if ($('.class-selector.select-me').length == 1)
			$('.class-selector.select-me').first().trigger('click');
		else
			$('.class-selector').first().trigger('click');

		return
	}
}

$(basic.init);
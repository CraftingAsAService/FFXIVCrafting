var levequests = {
	init:function() {
		levequests.events();

		levequests.decipher_hash();

		return;
	},
	decipher_hash:function() {
		var hash = document.location.hash;

		if (hash == '')
			return false;

		// Take off the #, explode
		// hash = hash.slice(1).split('-');

		// $('a[href=#' + hash[0] + '-tab]').trigger('click');

		// $('th[data-section=#' + hash[0] + '-' + hash[1] + ']').trigger('click');

		return true;
	},
	events:function() {

		// On clicking a class icon, fill in the level
		$('.class-selector').click(function() {
			var el = $(this),
				level = parseInt(Math.floor(el.data('level') / 5) * 5),
				level_el = $('.leve-level-select a[data-level=' + level + ']');

			$('.class-selector.active').removeClass('active');
			el.addClass('active');

			$('.leve-level-select a.active').removeClass('active');
			level_el.addClass('active');

			levequests.load_leves();
			
			return;
		});

		$('.leve-level-select a').click(function(event) {
			event.preventDefault();

			var el = $(this);

			$('.leve-level-select a.active').removeClass('active');
			el.addClass('active');

			levequests.load_leves();

			return;
		});

		$('.class-selector.active').trigger('click');

		// Hide images until later to avoid unnecessary loading
		// $('.level-section img.reveal-later').each(function() {
		// 	$(this).data('src', this.src);
		// 	this.src = '';
		// 	return;
		// });

		// $('.level-header th').click(function() {
		// 	var el = $(this);

		// 	if ($(el.data('section')).hasClass('hidden'))
		// 	{
		// 		$('.level-section', el.closest('table')).addClass('hidden');
		// 		$('.level-header i', el.closest('table')).addClass('glyphicon-chevron-down').removeClass('glyphicon-chevron-up');
		// 		$(el.data('section')).removeClass('hidden');
		// 		$('i', el).removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');

		// 		// Reveal the images
		// 		$('img[src=""]', el.closest('tbody').next('tbody')).each(function() {
		// 			$(this).removeClass('reveal-later');
		// 			this.src = $(this).data('src');
		// 			return;
		// 		});

		// 		// Modify the Hash

		// 		var section = el.data('section') + '-section';

		// 		var tmp = $(section);
		// 		tmp.prop('id', '');
		// 		document.location.hash = section.slice(1);
		// 		tmp.prop('id', section.slice(1));
		// 	}
		// 	else
		// 	{
		// 		document.location.hash = '';

		// 		$(el.data('section')).addClass('hidden');
		// 		$('i', el).addClass('glyphicon-chevron-down').removeClass('glyphicon-chevron-up');
		// 	}

		// 	return;
		// });
	},
	load_leves:function() {
		var job = $('.jobs-list label.active').data('class'),
			level = $('.leve-level-select a.active').data('level'),
			// section = $('#' + job + '-leves'),
			subsection = $('#' + job + '-' + level + '-leves');

		// // Hide/show leve sections
		// $('.leve-section').addClass('hidden');
		// section.removeClass('hidden');

		// Hide/show leves
		$('.leve-section .table-responsive').addClass('hidden');
		subsection.removeClass('hidden');

		// Reveal the images
		$('img[src=""]', subsection).each(function() {
			this.src = $(this).data('src');
			return;
		});

		return;
	}
}

$(levequests.init);